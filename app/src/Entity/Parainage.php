<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parainage
 *
 * @ORM\Table(name="parainage")
 * @ORM\Entity(repositoryClass="App\Repository\ParainageRepository")
 */
class Parainage
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="utilisateurParrain")
     * @ORM\JoinColumn(name="utilisateurParrain_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $utilisateurParrain;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", cascade={"persist"}, inversedBy="utilisateurFilleul")
     * @ORM\JoinColumn(name="utilisateurFilleul_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $utilisateurFilleul;

    /**
     * @var int
     *
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat;

    /**
     * @var float
     *
     * @ORM\Column(name="solde", type="float", nullable=true)
     */
    private $solde;

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
     * Set etat
     *
     * @param integer $etat
     *
     * @return Parainage
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return int
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set solde
     *
     * @param float $solde
     *
     * @return Parainage
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Parainage
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
     * Set utilisateurParrain
     *
     * @param Utilisateur $utilisateurParrain
     *
     * @return Parainage
     */
    public function setUtilisateurParrain(Utilisateur $utilisateurParrain = null)
    {
        $this->utilisateurParrain = $utilisateurParrain;

        return $this;
    }

    /**
     * Get utilisateurParrain
     *
     * @return Utilisateur
     */
    public function getUtilisateurParrain()
    {
        return $this->utilisateurParrain;
    }


    /**
     * Set utilisateurFilleul
     *
     * @param Utilisateur $utilisateurFilleul
     *
     * @return Parainage
     */
    public function setUtilisateurFilleul(Utilisateur $utilisateurFilleul = null)
    {
        $this->utilisateurFilleul = $utilisateurFilleul;

        return $this;
    }

    /**
     * Get utilisateurFilleul
     *
     * @return Utilisateur
     */
    public function getUtilisateurFilleul()
    {
        return $this->utilisateurFilleul;
    }
}
