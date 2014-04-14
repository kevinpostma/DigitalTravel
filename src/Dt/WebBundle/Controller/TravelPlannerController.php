<?php

namespace Dt\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Dt\CoreBundle\Entity\Neo4j\Location;

use HireVoice\Neo4j\EntityManager;
use HireVoice\Neo4j\Repository;

/**
 * Description of HomePageController
 *
 * @author Kevin
 */
class TravelPlannerController extends Controller
{
    public function indexAction()
    {
        return $this->render('DtWebBundle:TravelPlanner:index.html.twig', array());
    }
    
    public function neotestAction() 
    {
        $em = $this->container->get('neo4j.manager');
        $repo = $em->getRepository('Dt\\CoreBundle\\Entity\\Neo4j\\Location' );
        
        
        $list = $em->createCypherQuery()
            ->query('START
                a = node(0),
                b = node(8)
                MATCH 
                paths = AllShortestPaths((a)-[:DESTINATION]-(b))
                WITH nodes(paths) as locationNodes, paths
                RETURN DISTINCT extract(location in locationNodes | id(location)) as locations, 
                reduce(distance=0, r in relationships(paths) | distance+r.distance) as totalDistance
                ORDER BY totalDistance asc
                LIMIT 3')->getList();
        
        
        $item = $list->current();
        while($item->valid()) {
            var_dump($item->offsetGet($item->key()));
            $item->next();
        }
                
//START
//a = node(0), // Eelde
//b = node(8)  // Los Angeles
//MATCH 
//paths = AllShortestPaths((a)-[:DESTINATION]-(b))
//WITH nodes(paths) as locationNodes, paths
//RETURN DISTINCT extract(location in locationNodes | location.name) as locations, 
//reduce(distance=0, r in relationships(paths) | distance+r.distance) as totalDistance
//ORDER BY totalDistance asc
//LIMIT 1
        
        return new \Symfony\Component\HttpFoundation\Response();

    }
}