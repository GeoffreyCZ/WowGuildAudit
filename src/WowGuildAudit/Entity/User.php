<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 20.02.2017
 * Time: 12:18
 */

namespace WowGuildAudit\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", unique=true, nullable=true, length=40)
     */
    protected $googleID;

    /**
     * @var string
     *
     * @ORM\Column(name="google_access_token", type="string", unique=true, nullable=true, length=40)
     */
    protected $googleAccessToken;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", unique=true, nullable=true, length=40)
     */
    protected $username;

    /**
     * @param string $googleID
     */
    public function setGoogleID($googleID)
    {
        $this->googleID = $googleID;
    }

    /**
     * @return string
     */
    public function getGoogleID()
    {
        return $this->googleID;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
    }

    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;
    }
}