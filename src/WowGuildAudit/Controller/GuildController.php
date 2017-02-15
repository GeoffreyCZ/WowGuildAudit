<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 13.02.2017
 * Time: 15:48
 */

namespace WowGuildAudit\Controller;

use Symfony\Component\HttpFoundation\Request;
use WowGuildAudit\Entity\EnumRole;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WowGuildAudit\Entity\Guild;
use WowGuildAudit\Entity\Realm;
use WowGuildAudit\Form\GuildType;

/**
 * Class GuildController
 * @package WowGuildAudit\Controller
 */
class GuildController extends Controller
{
    /**
     * Queries all guilds from DB and renders them using appropriate view
     * @Route("/guild/list", name="guild_list")
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
     * @Route("/guild/manage", name="manage_guild")
     */
    public function manageGuildAction($guildName)
    {
        $guild = $this->getDoctrine()->getRepository(Guild::class)->findOneBy(array('name' => $guildName));
        $roles = $this->getDoctrine()->getRepository(EnumRole::class)->findAll();
        $realms = $this->getAllRealms();
        //todo proper error message when no guild found
        return $this->render('guild/manage.html.twig', ['guild' => $guild,
            'roles' => $roles, 'realms' => $realms]);

    }

    /**
     * Renders guild creation page
     * @param $request Request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/guild/new", name="new_guild")
     */
    public function newGuildAction(Request $request)
    {
        $realms = $this->getAllRealms();
        $guild = new Guild();
        $form = $this->createForm(GuildType::class, $guild);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $realm = $this->getDoctrine()->getRepository(Realm::class)->findOneBy(array(
                'name' => substr($guild->getRealm(), 0, -4),
                'region' => substr($guild->getRealm(), -3, 2)
                ));
            $key = mb_substr(str_shuffle("abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789"), 0, 1).substr(md5(time()), 1, 9);
            $guild->setGuildKey($key);
            $guild->setRealm($realm);

            $duplicityCheck = $this->getDoctrine()->getRepository(Guild::class)->findBy(array(
                'name' => $guild->getName(),
                'realm' => $guild->getRealm()
            ));

            if(!$duplicityCheck) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($guild);
                $entityManager->flush();
                return $this->render('guild/manage.html.twig', [
                    'guild' => $guild
                ]);
            } else {
                return $this->render('guild/new.html.twig', [
                    'error' => true,
                    'realms' => $realms,
                    'form' => $form->createView()
                ]);

            }
        }
        return $this->render('guild/new.html.twig', [
            'realms' => $realms,
            'form' => $form->createView()
        ]);
    }

    /**
     * Returns collection of all realms from DB
     * @return array
     */
    private function getAllRealms()
    {
        return $this->getDoctrine()->getRepository(Realm::class)->findAll();
    }

}