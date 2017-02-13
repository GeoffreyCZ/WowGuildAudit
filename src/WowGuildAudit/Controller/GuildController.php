<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 13.02.2017
 * Time: 15:48
 */

namespace WowGuildAudit\Controller;

use WowGuildAudit\Entity\EnumRole;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WowGuildAudit\Entity\Guild;

/**
 * Class GuildController
 * @package WowGuildAudit\Controller
 */
class GuildController extends Controller
{
    /**
     * Queries all guilds from DB and renders them using appropriate view
     * @Route("/guild-list", name="guild_list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAllGuildsAction()
    {
        $guilds = $this->getDoctrine()->getRepository(Guild::class)->findAll();
        return $this->render('guild/list.html.twig', ['guilds' => $guilds]);
    }

    /**
     * Renders guild management page with all members
     * @param $guildName string Name of the managed guild from the url
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/guild/{guildName}", name="manage_guild")
     */
    public function manageGuildAction($guildName)
    {
        $guild = $this->getDoctrine()->getRepository(Guild::class)->findOneBy(array('name' => $guildName));
        $roles = $this->getDoctrine()->getRepository(EnumRole::class)->findAll();
        //todo proper error message when no guild found
        return $this->render('guild/manage.html.twig', ['guild' => $guild,
            'roles' => $roles]);

    }

}