<?php

namespace Dt\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Dt\CoreBundle\Entity\Locations;
/**
 * Description of HomePageController
 *
 * @author Kevin
 */
class ApiController extends Controller
{
    
    public function countriesAction() {
        
        $em = $this->getDoctrine()->getManager();
        $countryRepository = $em->getRepository('DtCoreBundle:Country');
        
        $countries = $countryRepository->findAll();

        return new Response(json_encode($countries));
    }
    
    public function locationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $countryRepository = $em->getRepository('DtCoreBundle:Locations');
        
        
        $countries = $countryRepository->findAll();
        
        $serializer = $this->get('jms_serializer');
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($serializer->serialize($countries, 'json'));
        $response->setStatusCode(200);
        
        return $response;
    }
    
    public function locationAction($locationId)
    {
        $serializer = $this->get('jms_serializer');
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($serializer->serialize($this->getLocation($locationId), 'json'));
        $response->setStatusCode(200);
        
        return $response;
    }
    
    public function getPoisByRegionAction($region)
    {
        $em = $this->getDoctrine()->getManager();
        $poiRepository = $em->getRepository('DtCoreBundle:Tags');
        $regionRepository = $em->getRepository('DtCoreBundle:Regions');
        
        $region = $regionRepository->find($region);
        
        $serializer = $this->get('jms_serializer');
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($serializer->serialize($region->getTag(), 'json'));
        $response->setStatusCode(200);
        
        return $response;
    }
    
    
    public function getAllPoisAction($offset, $limit)
    {
        $em = $this->getDoctrine()->getManager();
        $poiRepository = $em->getRepository('DtCoreBundle:Tags');
        
        if($limit == 0) {
            $pois = $poiRepository->findAll();
        } else {
            $pois = $poiRepository->findBy(array(), null, $limit, $offset);
        }
        $count = $em->createQuery('SELECT COUNT(t.id) FROM DtCoreBundle:Tags t')->getSingleScalarResult();
        
        
        $result = array(
            'items' => $pois,
            'total' => $count,
        );
        
        $serializer = $this->get('jms_serializer');
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($serializer->serialize($result, 'json'));
        $response->setStatusCode(200);
        
        return $response;
    }
    
    
    public function routeAction($startLocation,$endLocation, \Symfony\Component\HttpFoundation\Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $content = $this->get("request")->getContent();
        if (!empty($content))
        {
            $filters = json_decode($content, true); // 2nd param to get as array
        }
        
        
        $filterLocations = array();
        foreach($filters['filters'] as $filter) {
            if($filter['type'] == 'locations') {
                $location = $this->getLocationByName($filter['value']);
                if($location != null) {
                    $filterLocations[] = $location;
                }
            }
        }
        
        
        if(count($filterLocations) > 0) {
            
            $locations = array();
            while(count($filterLocations) > 0) {
                
                $tempRoutes = array();
                /** Add first location **/
                foreach($filterLocations as $stopLocation) {
                    $tempRoutes[] = $this->getRoute($startLocation, $stopLocation->getId());
                }
                
                while(count($tempRoutes) > 1) {
                    if($tempRoutes[0] > $tempRoutes[1]) {
                        unset($tempRoutes[0]);
                    } else {
                        unset($tempRoutes[1]);
                    }
                }
                
                $shortestRoute = $tempRoutes[0];
                
                foreach($shortestRoute as &$location) {
                    $location = $this->getLocation($location);
                    $locations [] = $location;
                }
                
                $deletableLocation = end($shortestRoute);
                
                foreach($filterLocations as $key => $val) {
                    if($val->getId() == $deletableLocation->getId()) {
                        unset($filterLocations[$key]);
                        break;
                    }
                }
                
            }
            
            $lastStartLocation = end($locations);
            $tempRoute =  $this->getRoute($lastStartLocation->getId(), $endLocation);
            
            foreach($tempRoute as $location) {
                $location = $this->getLocation($location);
                $locations[] = $location;
            }
            
            
        } else {
            $locations = $this->getRoute($startLocation, $endLocation);

            foreach($locations as &$location) {
                $location = $this->getLocation($location);
            }
        }
        
        $serializer = $this->get('jms_serializer');
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setContent($serializer->serialize($locations, 'json'));
        $response->setStatusCode(200);
        
        return $response;
        
    }
    
    
    private function getLocation($locationId)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('DtCoreBundle:Locations');
        return $repository->find($locationId);
    }
    
    private function getLocationByName($name)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('DtCoreBundle:Locations');
        $location = $repository->findOneBy(array('city' => $name));
        return $location;
    }


    private function getRoute($startLocation, $endLocation)
    {
        $em = $this->container->get('neo4j.manager');
        $repo = $em->getRepository('Dt\\CoreBundle\\Entity\\Neo4j\\Location' );
        
        $list = $em->createCypherQuery()
            ->query('START
                a = node('.$startLocation.'),
                b = node('.$endLocation.')
                MATCH 
                paths = AllShortestPaths((a)-[:DESTINATION]-(b))
                WITH nodes(paths) as locationNodes, paths
                RETURN DISTINCT extract(location in locationNodes | id(location)) as locations, 
                reduce(distance=0, r in relationships(paths) | distance+r.distance) as totalDistance
                ORDER BY totalDistance asc
                LIMIT 3')->getList();
        
        $array = array();
        $item = $list->current();
        while($item->valid()) {
            $array[] = $item->offsetGet($item->key());
            $item->next();
        }
        
        return $array;
    }
}