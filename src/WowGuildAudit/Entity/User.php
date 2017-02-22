<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 20.02.2017
 * Time: 12:18
 */

namespace WowGuildAudit\Entity;

use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;

/**
 * User
 *
 * @ORM\Table(name="User")
 * @ORM\Entity
 */
class User
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
     * @ORM\Column(name="email", type="string", unique=true, nullable=true, length=40)
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity="Guild", mappedBy="user")
     */
    private $guild;

    /**
     * @ORM\Column(name="premium", type="boolean", )
     */
    private $premium = false;

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

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getGuild()
    {
        return $this->guild;
    }

    /**
     * @param mixed $guild
     */
    public function setGuild($guild)
    {
        $this->guild = $guild;
    }

    /**
     * @return mixed
     */
    public function getPremium()
    {
        return $this->premium;
    }

    /**
     * @param mixed $premium
     */
    public function setPremium($premium)
    {
        $this->premium = $premium;
    }
}