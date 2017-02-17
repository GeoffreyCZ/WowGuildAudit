<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 13.02.2017
 * Time: 15:48
 */

namespace WowGuildAudit\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Driver\PDOException;
use Symfony\Component\HttpFoundation\Request;
use WowGuildAudit\Entity\EnumRole;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WowGuildAudit\Entity\Guild;
use WowGuildAudit\Entity\Member;
use WowGuildAudit\Entity\Realm;
use WowGuildAudit\Form\GuildType;
use WowGuildAudit\Form\NewGuildType;

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
     * @param $request Request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/guild/manage", name="manage_guild")
     */
    public function manageGuildAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $guildName = 'testGuild1';
        $roles = $this->getDoctrine()->getRepository(EnumRole::class)->findAll();
        $guild = $this->getDoctrine()->getRepository(Guild::class)->findOneBy(array('name' => $guildName));
        $form = $this->createForm(GuildType::class, $guild);
        $realms = $this->getAllRealms();

        $originalMembers = new ArrayCollection();

        foreach ($guild->getMembers() as $member) {
            $originalMembers->add($member);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($guild->getMembers() as $member) {
                /** @var $member Member */
                $role = $this->getDoctrine()->getRepository(EnumRole::class)->findOneBy(['role' => $member->getRole()]);
                $realm = $this->getDoctrine()->getRepository(Realm::class)->findOneBy(array(
                    'name' => substr($member->getRealm(), 0, -4),
                    'region' => substr($member->getRealm(), -3, 2)
                ));
                if ($realm === null) {
                    $this->addFlash(
                        'danger',
                        'Member ' . $member->getName() . '\'s realm was not found, choose one from the list, please.'
                    );
                    return $this->render('guild/manage.html.twig', [
                        'guild' => $guild,
                        'roles' => $roles,
                        'realms' => $realms,
                        'form' => $form->createView()
                    ]);
                }
                $member->setRole($role);
                $member->setRealm($realm);
            }
            foreach ($originalMembers as $originalMember) {
                if ($guild->getMembers()->contains($originalMember) === false) {
                    /** @var $originalMember Member */
                    $guild->removeMember($originalMember);
                    $originalMember->setGuild(null);
                    $entityManager->remove($originalMember);
                }
            }
            try {
                $entityManager->persist($guild);
                $entityManager->flush();
            } catch(\Exception $exception){
                $this->addFlash(
                    'danger',
                    'Error! You can\'t add one member twice in one guild'
                );

                return $this->render('guild/manage.html.twig', [
                    'guild' => $guild,
                    'roles' => $roles,
                    'realms' => $realms,
                    'form' => $form->createView()
                ]);
            };
            $this->addFlash(
                'success',
                'Your changes were successfully saved!'
            );
        }

        return $this->render('guild/manage.html.twig', [
            'guild' => $guild,
            'roles' => $roles,
            'realms' => $realms,
            'form' => $form->createView()
        ]);

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
        $form = $this->createForm(NewGuildType::class, $guild);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $realm = $this->getDoctrine()->getRepository(Realm::class)->findOneBy(array(
                'name' => substr($guild->getRealm(), 0, -4),
                'region' => substr($guild->getRealm(), -3, 2)
            ));
            $key = mb_substr(str_shuffle("abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789"), 0, 1) . substr(md5(time()), 1, 9);

            $guild->setGuildKey($key);
            $guild->setRealm($realm);

            $duplicityCheck = $this->getDoctrine()->getRepository(Guild::class)->findBy(array(
                'name' => $guild->getName(),
                'realm' => $guild->getRealm()
            ));

            if (!$duplicityCheck) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($guild);
                $entityManager->flush();
                return $this->redirectToRoute('manage_guild', array('guild' => $guild));
            } else {
                $this->addFlash(
                    'danger',
                    'Guild already exists!'
                );
                return $this->render('guild/new.html.twig', [
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
        return $realms = $this->getDoctrine()->getRepository(Realm::class)->findAll();
    }

}