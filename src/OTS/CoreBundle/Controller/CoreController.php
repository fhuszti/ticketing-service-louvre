<?php

namespace OTS\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CoreController extends Controller
{
    /**
     * @Route("/{_locale}", requirements={"_locale" = "|fr|en"}, defaults={"_locale"="fr"}, name="ots_core_home")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('OTSCoreBundle:Core:index.html.twig');
    }

    /**
     * @Route("/legal/{_locale}", requirements={"_locale" = "|fr|en"}, defaults={"_locale"="fr"}, name="ots_core_legal")
     * @Method("GET")
     */
    public function legalAction()
    {
        return $this->render('OTSCoreBundle:Core:legal.html.twig');
    }

    /**
     * @Route("/terms/{_locale}", requirements={"_locale" = "|fr|en"}, defaults={"_locale"="fr"}, name="ots_core_terms")
     * @Method("GET")
     */
    public function termsAction()
    {
        return $this->render('OTSCoreBundle:Core:terms.html.twig');
    }
}
