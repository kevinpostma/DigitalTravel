<?php

namespace Dt\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}