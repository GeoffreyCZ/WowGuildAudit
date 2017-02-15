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
use WowGuildAudit\Entity\Realm;

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
     * @Route("/manage-guild/{guildName}", name="manage_guild")
     */
    public function manageGuildAction($guildName)
    {
        $guild = $this->getDoctrine()->getRepository(Guild::class)->findOneBy(array('name' => $guildName));
        $roles = $this->getDoctrine()->getRepository(EnumRole::class)->findAll();
        $realms = $this->getDoctrine()->getRepository(Realm::class)->findAll();
        //todo proper error message when no guild found
        return $this->render('guild/manage.html.twig', ['guild' => $guild,
            'roles' => $roles, 'realms' => $realms]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/new-guild", name="new_guild")
     */
    public function newGuildAction() {
        return $this->render('guild/new.html.twig');
    }

}