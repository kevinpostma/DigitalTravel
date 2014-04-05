<?php

namespace Dt\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description of HomePageController
 *
 * @author Kevin
 */
class ContactController extends Controller
{
    public function indexAction()
    {
        return $this->render('DtWebBundle:Contact:index.html.twig', array());
    }
}