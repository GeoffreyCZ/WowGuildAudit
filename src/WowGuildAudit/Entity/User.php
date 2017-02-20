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
    protected $googleId;

    /**
     * @param string $googleId
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }

    /**
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
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
}