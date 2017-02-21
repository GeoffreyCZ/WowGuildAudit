<?php

namespace WowGuildAudit\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * Renders homepage
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('default/index.html.twig', ['user' => $user]);
    }

    /**
     * Renders spreadsheet page
     * @Route("/spreadsheet", name="spreadsheet")
     */
    public function spreadsheetAction() {
        return $this->render('spreadsheet/spreadsheet.html.twig');
    }

    /**
     * Renders FAQ page
     * @Route("/faq", name="faq")
     */
    public function faqAction() {
        return $this->render('default/faq.html.twig');
    }
}
