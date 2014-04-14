<?php

namespace Dt\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
/**
 * Description of HomePageController
 *
 * @author Kevin
 */
class PoiController extends Controller
{
    public function indexAction()
    {
        return $this->render('DtWebBundle:Poi:index.html.twig', array());
    }
    
    public function poisAction($start, $limit)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('DtCoreBundle:Location');
        
        
        $items = array();
        for($i = 0; $i < $limit; $i++) {
            $items[] = array(
                'title' => 'eiffeltoren',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut nisi erat, pellentesque quis interdum eget, hendrerit nec arcu. Maecenas vitae tortor est. Pellentesque placerat id magna eu tempus. Vivamus luctus sagittis venenatis. Morbi eu neque lectus. Sed gravida sollicitudin mauris, nec volutpat augue porttitor nec.',
                'country' => 'Netherlands',
                'region' => 'Noord braband',
                'rating' => rand(0,5),
            );
        }
        
        
        return new Response(json_encode(array(
            'total' => 60,
            'start' => $start,
            'limit' => $limit,
            'items' => $items
        )));
    }
    
    public function countriesAction() {
        
        $em = $this->getDoctrine()->getManager();
        $countryRepository = $em->getRepository('DtCoreBundle:Country');
        
        $countries = $countryRepository->findAll();

        return new Response(json_encode($countries));
    }
    
    public function tagsAction() {
        
        $tags = array(
            array(
                'tag' => "Musea",
            ),
            array(
                'tag' => "Historisch",
            ),
            array(
                'tag' => "Kunst",
            ),
            array(
                'tag' => "Cultuur",
            ),
            array(
                'tag' => "Religieuze bouwwerken",
            ),
            array(
                'tag' => "Parken",
            ),
            array(
                'tag' => "Theater en muziek",
            ),
            array(
                'tag' => "Uitgaansgebieden",
            ),
        );
        
        return new Response(json_encode($tags));
    }
}