<?php

namespace OTS\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('OTSCoreBundle:Core:index.html.twig');
    }

    public function legalAction()
    {
        return $this->render('OTSCoreBundle:Core:legal.html.twig');
    }

    public function termsAction()
    {
        return $this->render('OTSCoreBundle:Core:terms.html.twig');
    }
}
