<?php

namespace OTS\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('OTSCoreBundle:Core:index.html.twig');
    }
}
