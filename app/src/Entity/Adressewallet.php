<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation as Serializer;

/**
 * Adressewallet
 *
 * @ApiResource
 * @ORM\Table(name="adressewallet")
 * @ORM\Entity(repositoryClass="App\Repository\AdressewalletRepository")
 * @see ORM\
 * @Serializer\ExclusionPolicy("ALL")
 */
class Adressewallet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var Crypto
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Crypto", cascade={"persist"}, inversedBy="adressewallet")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\Expose
     */
    private $crypto;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Transfertcrypto", mappedBy="adressewallet", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $transfertcrypto;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="adressewallet")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $utilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255)
     * @Serializer\Expose
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetimetz")
     */
    private $updated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetimetz")
     */
    private $created;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
    * Constructor
    */
    public function __construct()
    {
        $this->transfertcrypto = new ArrayCollection();
    }


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
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Adressewallet
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return Adressewallet
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }



    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Adressewallet
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
     * @return Adressewallet
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
     * Set state
     *
     * @param integer $state
     *
     * @return Adressewallet
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
     * Set crypto
     *
     * @param Crypto $crypto
     *
     * @return Adressewallet
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


    /**
     * Add transfertcrypto
     *
     * @param Transfertcrypto $transfertcrypto
     *
     * @return Adressewallet
     */
    public function addTransfertcrypto(Transfertcrypto $transfertcrypto)
    {
        $this->transfertcrypto[] = $transfertcrypto;

        return $this;
    }

    /**
     * Remove transfertcrypto
     *
     * @param Transfertcrypto $transfertcrypto
     */
    public function removeTransfertcrypto(Transfertcrypto $transfertcrypto)
    {
        $this->transfertcrypto->removeElement($transfertcrypto);
    }

    /**
     * Get transfertcrypto
     *
     * @return Collection
     */
    public function getTransfertcrypto()
    {
        return $this->transfertcrypto;
    }



    /**
     * Set utilisateur
     *
     * @param Utilisateur $utilisateur
     *
     * @return Adressewallet
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
}
