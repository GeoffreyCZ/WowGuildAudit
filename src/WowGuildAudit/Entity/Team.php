<?php
/**
 * Created by PhpStorm.
 * User: Michal Kroupa
 * Date: 22.02.2017
 * Time: 12:00
 */

namespace WowGuildAudit\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Team")
 */
class Team
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
     * @ORM\ManyToOne(targetEntity="Guild", inversedBy="teams")
     * @ORM\JoinColumn(name="guild_id", referencedColumnName="id")
     */
    private $guild;

    /**
     * @ORM\OneToMany(targetEntity="Member", mappedBy="team", cascade={"persist"})
     */
    private $members;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param mixed $members
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }
}