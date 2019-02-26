<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Vendre
 *
 * @ORM\Table(name="vendre")
 * @ORM\Entity(repositoryClass="App\Repository\VendreRepository")
 */
class Vendre
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
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="vendre")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $utilisateur;

    /**
     * @var float
     *
     * @ORM\Column(name="sellAmount", type="float")
     */
    private $sellAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="sellPrice", type="float")
     */
    private $sellPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="sellCost", type="float")
     */
    private $sellCost;


    /**
     * @var float
     *
     * @ORM\Column(name="addSolde", type="float")
     */
    private $addSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="sendVirement", type="float")
     */
    private $sendVirement;

    /**
     * @var float
     *
     * @ORM\Column(name="sellPriceMarge", type="float")
     */
    private $sellPriceMarge;

    /**
     * @var float
     *
     * @ORM\Column(name="pourcentage", type="float")
     */
    private $pourcentage;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetimetz")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sellDate", type="datetimetz", nullable=true)
     */
    private $sellDate;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
     * @var int
     *
     * @ORM\Column(name="stateVente", type="integer")
     */
    private $stateVente;

    /**
     * @var Crypto
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Crypto", cascade={"persist"}, inversedBy="vendre")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $crypto;

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
     * @param float $sellAmount
     */
    public function setSellAmount(float $sellAmount): void
    {
        $this->sellAmount = $sellAmount;
    }

    /**
     * @return float
     */
    public function getSellAmount(): float
    {
        return $this->sellAmount;
    }

    /**
     * @param float $sellPrice
     */
    public function setSellPrice(float $sellPrice): void
    {
        $this->sellPrice  = $sellPrice ;
    }

    /**
     * @return float
     */
    public function getSellPrice(): float
    {
        return $this->sellPrice ;
    }

    /**
     * @param float $sellCost
     */
    public function setSellCost(float $sellCost): void
    {
        $this->sellCost  = $sellCost ;
    }

    /**
     * @return float
     */
    public function getSellCost(): float
    {
        return $this->sellCost ;
    }

    /**
     * @param float $addSolde
     */
    public function setAddSolde(float $addSolde): void
    {
        $this->addSolde  = $addSolde ;
    }

    /**
     * @return float
     */
    public function getAddSolde(): float
    {
        return $this->addSolde ;
    }

    /**
     * @param float $sendVirement
     */
    public function setSendVirement(float $sendVirement): void
    {
        $this->sendVirement  = $sendVirement ;
    }

    /**
     * @return float
     */
    public function getSendVirement(): float
    {
        return $this->sendVirement ;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param $sellDate
     */
    public function setSellDate(\DateTime $sellDate): void
    {
        $this->sellDate = $sellDate;
    }

    /**
     * @return \DateTime
     */
    public function getSellDate(): \DateTime
    {
        return $this->sellDate;
    }

    /**
     * @param $sellPriceMarge
     */
    public function setSellPriceMarge(float $sellPriceMarge): void
    {
        $this->sellPriceMarge = $sellPriceMarge;
    }

    /**
     * @return float
     */
    public function getSellPriceMarge(): float
    {
        return $this->sellPriceMarge;
    }

    /**
     * @param $pourcentage
     */
    public function setPourcentage(float $pourcentage): void
    {
        $this->pourcentage = $pourcentage;
    }

    /**
     * @return float
     */
    public function getPourcentage(): float
    {
        return $this->pourcentage;
    }

    /**
     * @param $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }


    /**
     * @param int $stateVente
     */
    public function setStateVente(int $stateVente): void
    {
        $this->stateVente = $stateVente;
    }

    /**
     * @return int
     */
    public function getStateVente(): int
    {
        return $this->stateVente;
    }

    /**
     * @param Crypto|null $crypto
     */
    public function setCrypto(?Crypto $crypto): void
    {
        $this->crypto = $crypto;
    }

    /**
     * @return Crypto
     */
    public function getCrypto(): Crypto
    {
        return $this->crypto;
    }

    /**
     * @param Utilisateur|null $utilisateur
     */
    public function setUtilisateur(?Utilisateur $utilisateur): void
    {
        $this->utilisateur = $utilisateur;
    }

    /**
     * @return Utilisateur
     */
    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }
}
