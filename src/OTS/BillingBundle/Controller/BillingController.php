<?php

namespace OTS\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BillingController extends Controller
{
    public function indexAction()
    {
        return $this->render('OTSBillingBundle:Billing:index.html.twig');
    }
}
