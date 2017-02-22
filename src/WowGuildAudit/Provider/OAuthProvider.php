<?php
namespace WowGuildAudit\Provider;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use WowGuildAudit\Entity\User;

/**
 * Class OAuthUserProvider
 * @package AppBundle\Security\Core\User
 */
class OAuthProvider extends OAuthUserProvider
{
    protected $session, $doctrine, $admins;

    public function __construct($session, $doctrine, $service_container)
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->container = $service_container;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $googleId = $response->getUsername(); /* An ID like: 112259658235204980084 */
        $email = $response->getEmail();
        $this->session->set('email', $email);

        $result = $this->doctrine->getRepository(User::class)->findOneBy(['googleID' => $googleId]);

        if (!count($result)) {
            $user = new User($googleId);
            $user->setGoogleID($googleId);
            $user->setEmail($email);

            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();
        } else {
            $user = $result;
        }

        return $this->loadUserByUsername($response->getUsername());
    }

//    public function loadUserByUsername($username)
//    {
//
//        $result = $this->doctrine->getRepository(User::class)->findOneBy(['googleID' => $username]);
//
//        if (count($result)) {
//            return $result;
//        } else {
//            return new User($username);
//        }
//    }
}