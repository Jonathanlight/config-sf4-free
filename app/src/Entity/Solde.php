<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solde
 *
 * @ORM\Table(name="solde")
 * @ORM\Entity(repositoryClass="App\Repository\SoldeRepository")
 */
class Solde
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
     * @ORM\Column(name="solde", type="float", nullable=true)
     */
    private $solde;

    /**
     * @var float
     *
     * @ORM\Column(name="soldeOld", type="float", nullable=true)
     */
    private $soldeOld;

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
     * Set solde
     *
     * @param float $solde
     *
     * @return Solde
     */
    public function setSolde($solde)
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * Get solde
     *
     * @return float
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * Set soldeOld
     *
     * @param float $soldeOld
     *
     * @return Solde
     */
    public function setSoldeOld($soldeOld)
    {
        $this->soldeOld = $soldeOld;

        return $this;
    }

    /**
     * Get soldeOld
     *
     * @return float
     */
    public function getSoldeOld()
    {
        return $this->soldeOld;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Operation
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->created;
    }
}
