<?php

namespace Dt\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description of HomePageController
 *
 * @author Kevin
 */
class HomePageController extends Controller
{
    public function indexAction()
    {
        return $this->render('DtWebBundle:Default:index.html.twig', array());
    }
}