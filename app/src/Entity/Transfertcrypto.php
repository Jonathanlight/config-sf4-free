<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Transfertcrypto
 *
 * @ORM\Table(name="transfertcrypto")
 * @ORM\Entity(repositoryClass="App\Repository\TransfertcryptoRepository")
 */
class Transfertcrypto
{
    use DeletableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Adressewallet
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Adressewallet", cascade={"persist"}, inversedBy="transfertcrypto")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $adressewallet;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="transfertcrypto")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $utilisateur;

    /**
     * @var Crypto
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Crypto", cascade={"persist"}, inversedBy="transfertcrypto")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $crypto;

    /**
     * @var string
     *
     * @ORM\Column(name="hashTransaction", type="string", nullable=true)
     */
    private $hashTransaction;

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
     * @ORM\Column(name="state", type="integer", nullable=true)
     */
    private $state;

    /**
     * @var int
     *
     * @ORM\Column(name="validated", type="integer")
     */
    private $validated;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set frais
     *
     * @param float $frais
     *
     * @return Transfertcrypto
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
     * @return Transfertcrypto
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
     * Set state
     *
     * @param integer $state
     *
     * @return Transfertcrypto
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
     * Set validated
     *
     * @param integer $validated
     *
     * @return Transfertcrypto
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
     * @return Transfertcrypto
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
     * @return Transfertcrypto
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
     * @return Transfertcrypto
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
     * Set adressewallet
     *
     * @param Adressewallet $adressewallet
     *
     * @return Transfertcrypto
     */
    public function setAdressewallet(Adressewallet $adressewallet = null)
    {
        $this->adressewallet = $adressewallet;

        return $this;
    }

    /**
     * @return Adressewallet
     */
    public function getAdressewallet()
    {
        return $this->adressewallet;
    }

    /**
     * Set crypto
     *
     * @param Crypto $crypto
     *
     * @return Transfertcrypto
     */
    public function setCrypto(?Crypto $crypto)
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
     * @return string
     */
    public function getHashTransaction(): string
    {
        return $this->hashTransaction;
    }

    /**
     * @param float $hashTransaction
     * @return Transfertcrypto
     */
    public function setHashTransaction(float $hashTransaction): Transfertcrypto
    {
        $this->hashTransaction = $hashTransaction;
        return $this;
    }
}
