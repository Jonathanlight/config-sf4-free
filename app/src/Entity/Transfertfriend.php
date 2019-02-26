<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transfertfriend
 *
 * @ORM\Table(name="transfertfriend")
 * @ORM\Entity(repositoryClass="App\Repository\TransfertfriendRepository")
 */
class Transfertfriend
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="transfertfriend")
     * @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $utilisateur;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="transfertmyfriend")
     * @ORM\JoinColumn(name="utilisateurFriend_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $utilisateurFriend;

    /**
     * @var Crypto
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Crypto", cascade={"persist"}, inversedBy="transfertfriend")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $crypto;

    /**
     * @var float
     *
     * @ORM\Column(name="frais", type="float", nullable=true)
     */
    private $frais;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255, nullable=true)
     */
    private $content;

    /**
     * @var int
     *
     * @ORM\Column(name="validated", type="integer")
     */
    private $validated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * Set state
     *
     * @param integer $state
     *
     * @return Transfertfriend
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Transfertfriend
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set frais
     *
     * @param float $frais
     *
     * @return Transfertfriend
     */
    public function setFrais($frais)
    {
        $this->frais = $frais;

        return $this;
    }

    /**
     * Get frais
     *
     * @return float
     */
    public function getFrais()
    {
        return $this->frais;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Transfertfriend
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }



    /**
     * Set validated
     *
     * @param integer $validated
     *
     * @return Transfertfriend
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated
     *
     * @return int
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Transfertfriend
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Transfertfriend
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
     * Set utilisateur
     *
     * @param Utilisateur $utilisateur
     *
     * @return Transfertfriend
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
     * @return Transfertfriend
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


    /**
     * Set crypto
     *
     * @param Crypto $crypto
     *
     * @return Transfertfriend
     */
    public function setCrypto(Crypto $crypto = null)
    {
        $this->crypto = $crypto;

        return $this;
    }

    /**
     * Get crypto
     *
     * @return Crypto
     */
    public function getCrypto()
    {
        return $this->crypto;
    }
}
