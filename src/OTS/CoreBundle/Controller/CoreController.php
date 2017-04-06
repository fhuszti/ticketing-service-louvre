<?php

namespace OTS\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OTSCoreBundle:Default:index.html.twig');
    }
}
