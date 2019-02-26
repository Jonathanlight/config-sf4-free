<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Soldecrypto
 *
 * @ORM\Table(name="soldecrypto")
 * @ORM\Entity(repositoryClass="App\Repository\SoldecryptoRepository")
 */
class Soldecrypto
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
     * @var float
     *
     * @ORM\Column(name="btcSolde", type="float", nullable=true)
     */
    private $btcSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="bchSolde", type="float", nullable=true)
     */
    private $bchSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="ethSolde", type="float", nullable=true)
     */
    private $ethSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="etcSolde", type="float", nullable=true)
     */
    private $etcSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="ltcSolde", type="float", nullable=true)
     */
    private $ltcSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="zecSolde", type="float", nullable=true)
     */
    private $zecSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="xrpSolde", type="float", nullable=true)
     */
    private $xrpSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="eosSolde", type="float", nullable=true, options={"default":0})
     */
    private $eosSolde;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetimetz")
     */
    private $updated;


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
     * Set btcSolde
     *
     * @param float $btcSolde
     *
     * @return Soldecrypto
     */
    public function setBtcSolde($btcSolde)
    {
        $this->btcSolde = $btcSolde;

        return $this;
    }

    /**
     * Get btcSolde
     *
     * @return float
     */
    public function getBtcSolde()
    {
        return $this->btcSolde;
    }

    /**
     * Set bchSolde
     *
     * @param float $bchSolde
     *
     * @return Soldecrypto
     */
    public function setBchSolde($bchSolde)
    {
        $this->bchSolde = $bchSolde;

        return $this;
    }

    /**
     * Get bchSolde
     *
     * @return float
     */
    public function getBchSolde()
    {
        return $this->bchSolde;
    }

    /**
     * Set ethSolde
     *
     * @param float $ethSolde
     *
     * @return Soldecrypto
     */
    public function setEthSolde($ethSolde)
    {
        $this->ethSolde = $ethSolde;

        return $this;
    }

    /**
     * Get ethSolde
     *
     * @return float
     */
    public function getEthSolde()
    {
        return $this->ethSolde;
    }

    /**
     * Set etcSolde
     *
     * @param float $etcSolde
     *
     * @return Soldecrypto
     */
    public function setEtcSolde($etcSolde)
    {
        $this->etcSolde = $etcSolde;

        return $this;
    }

    /**
     * Get etcSolde
     *
     * @return float
     */
    public function getEtcSolde()
    {
        return $this->etcSolde;
    }

    /**
     * Set ltcSolde
     *
     * @param float $ltcSolde
     *
     * @return Soldecrypto
     */
    public function setLtcSolde($ltcSolde)
    {
        $this->ltcSolde = $ltcSolde;

        return $this;
    }

    /**
     * Get ltcSolde
     *
     * @return float
     */
    public function getLtcSolde()
    {
        return $this->ltcSolde;
    }

    /**
     * Set zecSolde
     *
     * @param float $zecSolde
     *
     * @return Soldecrypto
     */
    public function setZecSolde($zecSolde)
    {
        $this->zecSolde = $zecSolde;

        return $this;
    }

    /**
     * Get zecSolde
     *
     * @return float
     */
    public function getZecSolde()
    {
        return $this->zecSolde;
    }

    /**
     * Set xrpSolde
     *
     * @param float $xrpSolde
     *
     * @return Soldecrypto
     */
    public function setXrpSolde($xrpSolde)
    {
        $this->xrpSolde = $xrpSolde;

        return $this;
    }

    /**
     * Get xrpSolde
     *
     * @return float
     */
    public function getXrpSolde()
    {
        return $this->xrpSolde;
    }

    /**
     * Set eosSolde
     *
     * @param float $eosSolde
     *
     * @return Soldecrypto
     */
    public function setEosSolde($eosSolde)
    {
        $this->eosSolde = $eosSolde;

        return $this;
    }

    /**
     * Get eosSolde
     *
     * @return float
     */
    public function getEosSolde()
    {
        return $this->eosSolde;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Soldecrypto
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
}
