<?php

namespace Dt\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}