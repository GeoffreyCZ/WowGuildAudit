<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 20.02.2017
 * Time: 15:29
 */

namespace WowGuildAudit\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{
    /**
     * Renders homepage
     * @Route("/google/login/check", name="login")
     */
    public function loginAction()
    {
        return $this->render('default/login.html.twig');
    }

}