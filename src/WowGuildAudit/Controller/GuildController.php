<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 13.02.2017
 * Time: 15:48
 */

namespace WowGuildAudit\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Buzz\Browser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use WowGuildAudit\Entity\EnumRole;
use WowGuildAudit\Entity\Guild;
use WowGuildAudit\Entity\Member;
use WowGuildAudit\Entity\Realm;
use WowGuildAudit\Entity\Team;
use WowGuildAudit\Form\NewGuildType;
use WowGuildAudit\Form\TeamType;

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
     * Renders guild management page with all teams
     * @param $request Request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/guild/manage", name="manage_guild")
     */
    public function manageGuildAction(Request $request)
    {
        $user = $request->getSession()->get('user');
        $guild = $this->getDoctrine()->getRepository(Guild::class)->findOneBy(['id' => $user->getGuild()->getId()]);

        return $this->render('guild/manage.html.twig', [
            'guild' => $guild
        ]);

    }

    /**
     * Method that handles AJAX request to add guild team
     * @param Request $request
     * @return JsonResponse
     */
    public function addTeamAction(Request $request)
    {
        $guild = $this->getDoctrine()->getRepository(Guild::class)->findOneBy(['id' => $request->get('guildId')]);
        $name = $request->get('name');
        $team = new Team();
        $team->setName($name);
        $team->setGuild($guild);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($team);
        $entityManager->flush();
        $response = array("code" => 100, "success" => true);
        return new JsonResponse($response);
    }

    /**
     * Method that handles AJAX request to remove guild team
     * @param Request $request
     * @return JsonResponse
     */
    public function removeTeamAction(Request $request)
    {
        $teamId = $request->get('teamId');
        $team = $this->getDoctrine()->getRepository(Team::class)->findOneBy(['id' => $teamId]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($team);
        $entityManager->flush();
        $response = array("code" => 100, "success" => true);
        return new JsonResponse($response);
    }

    /**
     * Renders team management page with all members
     * @param $request Request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/guild/manage/team/{teamId}", name="manage_guild_team")
     */
    public function manageTeamAction(Request $request, $teamId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $request->getSession()->get('user');
        $roles = $this->getDoctrine()->getRepository(EnumRole::class)->findAll();
        $team = $this->getDoctrine()->getRepository(Team::class)->findOneBy(array('id' => $teamId));
        $form = $this->createForm(TeamType::class, $team);
        $realms = $this->getAllRealms();

        $originalMembers = new ArrayCollection();

        foreach ($team->getMembers() as $member) {
            $originalMembers->add($member);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($team->getMembers() as $member) {
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
                    return $this->render('guild/manageTeam.html.twig', [
                        'team' => $team,
                        'roles' => $roles,
                        'realms' => $realms,
                        'form' => $form->createView()
                    ]);
                }
                $member->setRole($role);
                $member->setRealm($realm);
            }
            foreach ($originalMembers as $originalMember) {
                if ($team->getMembers()->contains($originalMember) === false) {
                    /** @var $originalMember Member */
                    $team->removeMember($originalMember);
                    $originalMember->setTeam(null);
                    $entityManager->remove($originalMember);
                }
            }
            try {
                $entityManager->persist($team);
                $entityManager->flush();
            } catch (\Exception $exception) {
                $this->addFlash(
                    'danger',
                    'Error! You can\'t add one member twice in one guild'
                );

                return $this->render('guild/manageTeam.html.twig', [
                    'team' => $team,
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

        return $this->render('guild/manageTeam.html.twig', [
            'team' => $team,
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
     * Renders guild creation page
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/guild/battle", name="guild_battle")
     */
    public function fetchFromBattleNetAction()
    {
        $ch = curl_init('https://eu.api.battle.net/wow/guild/Stormrage/Avalerion?fields=members&locale=en_GB&apikey=va9gftzrqx3b6t3xkmeknc6cuf46yevc');
//        $request = new Request('https://eu.api.battle.net/wow/guild/Stormrage/Avalerion?fields=members&locale=en_GB&apikey=va9gftzrqx3b6t3xkmeknc6cuf46yevc');
        $request = curl_exec($ch);
//        var_dump($request);
//        $collection = $this->parse($jsonContent);
        return $this->render('guild/battle.html.twig', [
            'response' => $request
        ]);
    }

    private function parse($response)
    {
        foreach ($response['content'] as $member) {
            print_r($member);
            echo('<br>');
            echo('<br>');
            echo('<br>');
        }
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