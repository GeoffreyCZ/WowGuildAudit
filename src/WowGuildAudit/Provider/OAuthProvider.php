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
        //Data from Google response
        $googleId = $response->getUsername(); /* An ID like: 112259658235204980084 */
//        $email = $response->getEmail();
//        $nickname = $response->getNickname();
//        $realname = $response->getRealName();
//        $avatar = $response->getProfilePicture();

        //set data in session
//        $this->session->set('email', $email);
//        $this->session->set('nickname', $nickname);
//        $this->session->set('realname', $realname);
//        $this->session->set('avatar', $avatar);

        //Check if this Google user already exists in our app DB
        $result = $this->doctrine->getRepository(User::class)->findOneBy(['id' => $googleId]);

        //add to database if doesn't exists
        if (!count($result)) {
            $user = new User();
            $user->setGoogleID($googleId);
//            $user->setRoles('ROLE_USER');

            //Set some wild random pass since its irrelevant, this is Google login

            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();
        } else {
            $user = $result[0]; /* return User */
        }

        //set id
        $this->session->set('id', $user->getId());

        return $this->loadUserByUsername($response->getUsername());
    }
}