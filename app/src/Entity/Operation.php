<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Utilisateur;
use App\Entity\Crypto;
use JMS\Serializer\Annotation as Serializer;

/**
 * Operation
 *
 * @ORM\Table(name="operation")
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 * @see ORM\
 * @Serializer\ExclusionPolicy("ALL")
 */
class Operation
{
    use DeletableTrait;

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
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur", cascade={"persist"}, inversedBy="operation")
     * @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", onDelete="SET NULL")
     * @Serializer\Expose
     */
    private $utilisateur;

    /**
     * @var Crypto
     *
     * @ORM\ManyToOne(targetEntity="Crypto", cascade={"persist"}, inversedBy="operation")
     * @ORM\JoinColumn(name="crypto_id", referencedColumnName="id", onDelete="SET NULL")
     * @Serializer\Expose
     */
    private $crypto;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     * @Serializer\Expose
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="float", nullable=true)
     * @Serializer\Expose
     */
    private $cost;

    /**
     * @var float
     *
     * @ORM\Column(name="pourcentageFrais", type="float", nullable=true)
     * @Serializer\Expose
     */
    private $pourcentageFrais;

    /**
     * @var float
     *
     * @ORM\Column(name="quantite", type="float", nullable=true)
     * @Serializer\Expose
     */
    private $quantite;

    /**
     * @var float
     *
     * @ORM\Column(name="frais", type="float", nullable=true)
     * @Serializer\Expose
     */
    private $frais;

    /**
     * @var float
     *
     * @ORM\Column(name="buyPrice", type="float", nullable=true)
     * @Serializer\Expose
     */
    private $buyPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     * @Serializer\Expose
     */
    private $reference;


    /**
     * @var int
     *
     * @ORM\Column(name="cvg", type="integer")
     * @Serializer\Expose
     */
    private $cvg;

    /**
     * @var int
     *
     * @ORM\Column(name="stateTransfert", type="integer", nullable=true)
     * @Serializer\Expose
     */
    private $stateTransfert;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     * @Serializer\Expose
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseWallet", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $adresseWallet;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $exchange;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateBuy", type="datetimetz", nullable=true)
     * @Serializer\Expose
     */
    private $dateBuy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTransfert", type="datetimetz", nullable=true)
     * @Serializer\Expose
     */
    private $dateTransfert;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetimetz")
     * @Serializer\Expose
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
     * Set amount
     *
     * @param float $amount
     *
     * @return Operation
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
     * Set cost
     *
     * @param float $cost
     *
     * @return Operation
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set quantite
     *
     * @param float $quantite
     *
     * @return Operation
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }


    /**
     * Get quantite
     *
     * @return float
     */
    public function getQuantite()
    {
        return $this->quantite;
    }


    /**
     * Set frais
     *
     * @param float $frais
     *
     * @return Operation
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
     * Set pourcentageFrais
     *
     * @param float $pourcentageFrais
     *
     * @return Operation
     */
    public function setPourcentageFrais($pourcentageFrais)
    {
        $this->pourcentageFrais = $pourcentageFrais;

        return $this;
    }

    /**
     * Get pourcentageFrais
     *
     * @return float
     */
    public function getPourcentageFrais()
    {
        return $this->pourcentageFrais;
    }




    /**
     * Set buyPrice
     *
     * @param float $buyPrice
     *
     * @return Operation
     */
    public function setBuyPrice($buyPrice)
    {
        $this->buyPrice = $buyPrice;

        return $this;
    }

    /**
     * Get buyPrice
     *
     * @return float
     */
    public function getBuyPrice()
    {
        return $this->buyPrice;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Operation
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set cvg
     *
     * @param integer $cvg
     *
     * @return Operation
     */
    public function setCvg($cvg)
    {
        $this->cvg = $cvg;

        return $this;
    }

    /**
     * Get cvg
     *
     * @return int
     */
    public function getCvg()
    {
        return $this->cvg;
    }

    /**
     * Set stateTransfert
     *
     * @param integer $stateTransfert
     *
     * @return Operation
     */
    public function setStateTransfert($stateTransfert)
    {
        $this->stateTransfert = $stateTransfert;

        return $this;
    }

    /**
     * Get stateTransfert
     *
     * @return int
     */
    public function getStateTransfert()
    {
        return $this->stateTransfert;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Operation
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
     * Set adresseWallet
     *
     * @param string $adresseWallet
     *
     * @return Operation
     */
    public function setAdresseWallet($adresseWallet)
    {
        $this->adresseWallet = $adresseWallet;

        return $this;
    }

    /**
     * Get adresseWallet
     *
     * @return string
     */
    public function getAdresseWallet()
    {
        return $this->adresseWallet;
    }

    /**
     * Set exchange
     *
     * @param string $exchange
     *
     * @return Operation
     */
    public function setExchange($exchange)
    {
        $this->exchange = $exchange;

        return $this;
    }

    /**
     * Get exchange
     *
     * @return string
     */
    public function getExchange()
    {
        return $this->exchange;
    }


    /**
     * Set dateBuy
     *
     * @param \DateTime $dateBuy
     *
     * @return Operation
     */
    public function setDateBuy($dateBuy)
    {
        $this->dateBuy = $dateBuy;

        return $this;
    }

    /**
     * Get dateBuy
     *
     * @return \DateTime
     */
    public function getDateBuy()
    {
        return $this->dateBuy;
    }


    /**
     * Set dateTransfert
     *
     * @param \DateTime $dateTransfert
     *
     * @return Operation
     */
    public function setDateTransfert($dateTransfert)
    {
        $this->dateTransfert = $dateTransfert;

        return $this;
    }

    /**
     * Get dateTransfert
     *
     * @return \DateTime
     */
    public function getDateTransfert()
    {
        return $this->dateTransfert;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Operation
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
     * @return Operation
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
     * Set crypto
     *
     * @param Crypto $crypto
     *
     * @return Operation
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
