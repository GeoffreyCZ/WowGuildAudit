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
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
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
