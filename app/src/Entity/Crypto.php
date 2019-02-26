<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;

/**
 * Crypto
 *
 * @ORM\Table(name="crypto")
 * @ORM\Entity(repositoryClass="App\Repository\CryptoRepository")
 * @see ORM\
 * @Serializer\ExclusionPolicy("ALL")
 */
class Crypto
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="indice", type="string", length=20)
     * @Serializer\Expose
     */
    private $indice;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $image;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operation", mappedBy="crypto", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $operation;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Adressewallet", mappedBy="crypto", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $adressewallet;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Vendre", mappedBy="crypto", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $vendre;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Transfertcrypto", mappedBy="crypto", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $transfertcrypto;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Transfertfriend", mappedBy="crypto", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $transfertfriend;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetimetz")
     * @Serializer\Expose
     */
    private $created;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->operation = new ArrayCollection();
        $this->vendre = new ArrayCollection();
        $this->adressewallet = new ArrayCollection();
        $this->transfertcrypto = new ArrayCollection();
        $this->transfertfriend = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Crypto
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set indice
     *
     * @param string $indice
     *
     * @return Crypto
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;

        return $this;
    }

    /**
     * Get indice
     *
     * @return string
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Crypto
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Crypto
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
     * Add operation
     *
     * @param Operation $operation
     *
     * @return Crypto
     */
    public function addOperation(Operation $operation)
    {
        $this->operation[] = $operation;

        return $this;
    }

    /**
     * Remove operation
     *
     * @param Operation $operation
     */
    public function removeOperation(Operation $operation)
    {
        $this->operation->removeElement($operation);
    }

    /**
     * Get operation
     *
     * @return Collection
     */
    public function getOperation()
    {
        return $this->operation;
    }


    /**
     * Add $adressewallet
     *
     * @param Adressewallet $adressewallet
     *
     * @return Crypto
     */
    public function addAdressewallet(Adressewallet $adressewallet)
    {
        $this->adressewallet[] = $adressewallet;

        return $this;
    }

    /**
     * Remove adressewallet
     *
     * @param Adressewallet $adressewallet
     */
    public function removeAdressewallet(Adressewallet $adressewallet)
    {
        $this->adressewallet->removeElement($adressewallet);
    }

    /**
     * Get adressewallet
     *
     * @return Collection
     */
    public function getAdressewallet()
    {
        return $this->adressewallet;
    }



    /**
     * Add vendre
     *
     * @param Vendre $vendre
     *
     * @return Crypto
     */
    public function addVendre(Vendre $vendre)
    {
        $this->vendre[] = $vendre;

        return $this;
    }

    /**
     * Remove vendre
     *
     * @param Vendre $vendre
     */
    public function removeVendre(Vendre $vendre)
    {
        $this->vendre->removeElement($vendre);
    }

    /**
     * Get vendre
     *
     * @return Collection
     */
    public function getVendre()
    {
        return $this->vendre;
    }



    /**
     * Add transfertcrypto
     *
     * @param Transfertcrypto $transfertcrypto
     *
     * @return Transfertcrypto
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
     * Add transfertfriend
     *
     * @param Transfertfriend $transfertfriend
     *
     * @return Transfertfriend
     */
    public function addTransfertfriend(Transfertfriend $transfertfriend)
    {
        $this->transfertfriend[] = $transfertfriend;

        return $this;
    }

    /**
     * Remove transfertfriend
     *
     * @param Transfertfriend $transfertfriend
     */
    public function removeTransfertfriend(Transfertfriend $transfertfriend)
    {
        $this->transfertfriend->removeElement($transfertfriend);
    }

    /**
     * Get transfertfriend
     *
     * @return Collection
     */
    public function getTransfertfriend()
    {
        return $this->transfertfriend;
    }
}
