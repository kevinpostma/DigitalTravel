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
    
    public function routeAction($startLocation,$endLocation)
    {
        $em = $this->getDoctrine()->getManager();
        
        $locations = $this->getRoute($startLocation, $endLocation);
        
        foreach($locations as &$location) {
            $location = $this->getLocation($location);
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