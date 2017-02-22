<?php

namespace WowGuildAudit\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use WowGuildAudit\Entity\User;

class DefaultController extends Controller
{
    /**
     * Renders homepage
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * Renders spreadsheet page
     * @Route("/spreadsheet", name="spreadsheet")
     */
    public function spreadsheetAction()
    {
        return $this->render('spreadsheet/spreadsheet.html.twig');
    }

    /**
     * Renders FAQ page
     * @Route("/faq", name="faq")
     */
    public function faqAction()
    {
        return $this->render('default/faq.html.twig');
    }
}
