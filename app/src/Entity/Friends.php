<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Friends
 *
 * @ORM\Table(name="friends")
 * @ORM\Entity(repositoryClass="App\Repository\FriendsRepository")
 */
class Friends
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="friend")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $utilisateur;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="myfriend")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $utilisateurFriend;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var int
     *
     * @ORM\Column(name="disabled", type="integer", nullable=true)
     */
    private $disabled;

    /**
     * @var int
     *
     * @ORM\Column(name="active", type="integer", nullable=true)
     */
    private $active;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Friends
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Friends
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set disabled
     *
     * @param integer $disabled
     *
     * @return Friends
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return int
     */
    public function getDisabled()
    {
        return $this->disabled;
    }


    /**
     * Set active
     *
     * @param integer $active
     *
     * @return Friends
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }


    /**
     * Set utilisateur
     *
     * @param Utilisateur $utilisateur
     *
     * @return Friends
     */
    public function setUtilisateur(Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }


    /**
     * Set utilisateurFriend
     *
     * @param Utilisateur $utilisateurFriend
     *
     * @return Friends
     */
    public function setUtilisateurFriend(Utilisateur $utilisateurFriend = null)
    {
        $this->utilisateurFriend = $utilisateurFriend;

        return $this;
    }

    /**
     * Get utilisateurFriend
     *
     * @return Utilisateur
     */
    public function getUtilisateurFriend()
    {
        return $this->utilisateurFriend;
    }
}
