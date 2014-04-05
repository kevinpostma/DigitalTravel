<?php

namespace Dt\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DtCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
