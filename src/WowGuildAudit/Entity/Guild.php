<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 13.02.2017
 * Time: 16:36
 */

namespace WowGuildAudit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Guild")
 */
class Guild
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Realm")
     * @ORM\JoinColumn(name="realm_id", referencedColumnName="id")
     */
    private $realm;

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="guild", cascade={"persist"})
     */
    private $teams;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $guildKey;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="guild")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * @param mixed $realm
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * @return mixed
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param mixed $teams
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
    }

    /**
     * @return mixed
     */
    public function getGuildKey()
    {
        return $this->guildKey;
    }

    /**
     * @param mixed $guildKey
     */
    public function setGuildKey($guildKey)
    {
        $this->guildKey = $guildKey;
    }

    public function addMember(Member $member) {
        $member->setTeam($this);

        $this->members->add($member);
    }

    public function removeMember(Member $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}