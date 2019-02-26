<?php

namespace App\Entity;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Operation;
use App\Entity\Solde;
use App\Entity\Vendre;
use App\Entity\Article;
use App\Entity\Depot;
use App\Entity\Parainage;
use App\Entity\Soldecrypto;
use App\Entity\Transfertfriend;
use App\Entity\Transfertcrypto;
use App\Entity\Friends;
use App\Entity\Adressewallet;
use App\Entity\Retrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation as Serializer;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @Serializer\ExclusionPolicy("ALL")
 */
class Utilisateur implements UserInterface
{
    const GENRE_MAN = "H";
    const GENRE_WOMAN = "F";

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
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Operation", mappedBy="utilisateur", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $operation;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Depot", mappedBy="utilisateur", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $depot;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Friends", mappedBy="utilisateur", cascade={"persist", "remove"}, orphanRemoval=true)
     * @JoinColumn(name="friend_id", referencedColumnName="id")
     */
    private $friend;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Friends", mappedBy="utilisateurFriend", cascade={"persist", "remove"}, orphanRemoval=true)
     * @JoinColumn(name="myfriend_id", referencedColumnName="id")
     */
    private $myfriend;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="utilisateur", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $commentaire;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Retrait", mappedBy="utilisateur", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $retrait;

    /**
     * @ORM\OneToOne(targetEntity="Solde", cascade={"persist"})
     * @JoinColumn(name="solde_id", referencedColumnName="id")
     * @Serializer\Expose
     */
    private $solde;

    /**
     * @ORM\OneToOne(targetEntity="Soldecrypto", cascade={"persist"})
     * @JoinColumn(name="soldecrypto_id", referencedColumnName="id")
     * @Serializer\Expose
     */
    private $soldecrypto;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Vendre", mappedBy="utilisateur")
     */
    private $vendre;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Adressewallet", mappedBy="utilisateur")
     */
    private $adressewallet;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Transfertcrypto", mappedBy="utilisateur")
     */
    private $transfertcrypto;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Transfertfriend", mappedBy="utilisateur")
     * @JoinColumn(name="transfertfriend_id", referencedColumnName="id")
     */
    private $transfertfriend;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Transfertfriend", mappedBy="utilisateurFriend")
     * @JoinColumn(name="transfertmyfriend_id", referencedColumnName="id")
     */
    private $transfertmyfriend;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Parainage", mappedBy="utilisateurParrain")
     * @JoinColumn(name="utilisateurParrain_id", referencedColumnName="id")
     */
    private $utilisateurParrain;

    /**
     * @var ArrayCollection|Collection
     *
     * @ORM\OneToMany(targetEntity="Parainage", mappedBy="utilisateurFilleul")
     * @JoinColumn(name="utilisateurFilleul_id", referencedColumnName="id")
     */
    private $utilisateurFilleul;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Serializer\Expose
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Serializer\Expose
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="ipadresse", type="string", length=50)
     * @Serializer\Expose
     */
    private $ipadresse;

    /**
     * @var string
     *
     * @ORM\Column(name="codepostale", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $codepostale;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=20, nullable=true)
     * @Serializer\Expose
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=20, nullable=true)
     * @Serializer\Expose
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=100, nullable=true)
     * @Serializer\Expose
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=100)
     * @Serializer\Expose
     */
    private $reference;

    /**
     * @var integer
     *
     * @ORM\Column(name="cgv", type="integer", nullable=true)
     * @Serializer\Expose
     */
    private $cgv;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=2, nullable=true)
     * @Serializer\Expose
     */
    private $genre;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=20, nullable=true)
     * @Serializer\Expose
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Serializer\Expose
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="passwordReset", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $passwordReset;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="passwordUpdated", type="datetimetz", nullable=true)
     * @Serializer\Expose
     */
    private $passwordUpdated;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Serializer\Expose
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     * @Serializer\Expose
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $avatar;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", nullable=true)
     * @Serializer\Expose
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $bic;

    /**
     * @var string
     *
     * @ORM\Column(name="loadIdentite", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $loadIdentite;

    /**
     * @var string
     *
     * @ORM\Column(name="loadJustificatifDomicile", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $loadJustificatifDomicile;

    /**
     * @var string
     *
     * @ORM\Column(name="justificatifSelfie", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $justificatifSelfie;

    /**
     * @var integer
     *
     * @ORM\Column(name="validated", type="integer", nullable=true)
     * @Serializer\Expose
     */
    private $validated;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=255)
     * @Serializer\Expose
     */
    private $roles;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datenaissance", type="datetimetz")
     * @Serializer\Expose
     */
    private $datenaissance;

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
        $this->article = new ArrayCollection();
        $this->commentaire = new ArrayCollection();
        $this->vendre = new ArrayCollection();
        $this->depot = new ArrayCollection();
        $this->retrait = new ArrayCollection();
        $this->adressewallet = new ArrayCollection();
        $this->friend = new ArrayCollection();
        $this->myfriend = new ArrayCollection();
        $this->transfertcrypto = new ArrayCollection();
        $this->transfertfriend = new ArrayCollection();
        $this->transfertmyfriend = new ArrayCollection();
        $this->utilisateurParrain = new ArrayCollection();
        $this->utilisateurFilleul = new ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Utilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Utilisateur
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return Utilisateur
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return Utilisateur
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }


    /**
     * Set country
     *
     * @param string $country
     *
     * @return Utilisateur
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
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
     * Set codepostale
     *
     * @param string $codepostale
     *
     * @return Utilisateur
     */
    public function setCodepostale($codepostale)
    {
        $this->codepostale = $codepostale;

        return $this;
    }

    /**
     * Get codepostale
     *
     * @return string
     */
    public function getCodepostale()
    {
        return $this->codepostale;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Utilisateur
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set genre
     *
     * @param string $genre
     *
     * @return Utilisateur
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Utilisateur
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set passwordReset
     *
     * @param string $passwordReset
     *
     * @return Utilisateur
     */
    public function setPasswordReset($passwordReset)
    {
        $this->passwordReset = $passwordReset;

        return $this;
    }

    /**
     * Get passwordReset
     *
     * @return string
     */
    public function getPasswordReset()
    {
        return $this->passwordReset;
    }

    /**
     * Set passwordUpdated
     *
     * @param \DateTime
     *
     * @return Utilisateur
     */
    public function setPasswordUpdated($passwordUpdated)
    {
        $this->passwordUpdated = $passwordUpdated;

        return $this;
    }

    /**
     * Get passwordUpdated
     *
     * @return string
     */
    public function getPasswordUpdated()
    {
        return $this->passwordUpdated;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Utilisateur
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * Set email
     *
     * @param string $email
     *
     * @return Utilisateur
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Utilisateur
     */
    public function setUsername($username)
    {
        $this->username = $username;

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
     * Set reference
     *
     * @param string $reference
     *
     * @return Utilisateur
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return Utilisateur
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set ipadresse
     *
     * @param string $ipadresse
     *
     * @return Utilisateur
     */
    public function setIpadresse($ipadresse)
    {
        $this->ipadresse = $ipadresse;

        return $this;
    }

    /**
     * Get ipadresse
     *
     * @return string
     */
    public function getIpadresse()
    {
        return $this->ipadresse;
    }

    /**
     * Set cgv
     *
     * @param integer $cgv
     *
     * @return Utilisateur
     */
    public function setCgv($cgv)
    {
        $this->cgv = $cgv;

        return $this;
    }

    /**
     * Get cgv
     *
     * @return integer
     */
    public function getCgv()
    {
        return $this->cgv;
    }

    /**
     * Set active
     *
     * @param integer $active
     *
     * @return Utilisateur
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }


    /**
     * Set datenaissance
     *
     * @param \DateTime $datenaissance
     *
     * @return Utilisateur
     */
    public function setDatenaissance($datenaissance)
    {
        $this->datenaissance = $datenaissance;

        return $this;
    }

    /**
     * Get datenaissance
     *
     * @return \DateTime
     */
    public function getDatenaissance()
    {
        return $this->datenaissance;
    }


    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Utilisateur
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

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return Utilisateur
     */
    public function setRoles($roles)
    {
        $this->roles = serialize($roles);

        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        return unserialize($this->roles);
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        // TODO: Implement loadUserByUsername() method.
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        // TODO: Implement refreshUser() method.
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        // TODO: Implement supportsClass() method.
    }

    /**
     * Add operation
     *
     * @param Operation $operation
     *
     * @return Utilisateur
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
     * @param \App\Entity\Solde|null $solde
     */
    public function setSolde(Solde $solde = null)
    {
        $this->solde = $solde;
    }

    /**
     * @return mixed
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * @param \App\Entity\Soldecrypto|null $soldecrypto
     */
    public function setSoldecrypto(Soldecrypto $soldecrypto = null)
    {
        $this->soldecrypto = $soldecrypto;
    }

    /**
     * @return mixed
     */
    public function getSoldecrypto()
    {
        return $this->soldecrypto;
    }

    /**
     * Add depot
     *
     * @param Depot $depot
     *
     * @return Utilisateur
     */
    public function addDepot(Depot $depot)
    {
        $this->depot[] = $depot;

        return $this;
    }

    /**
     * Remove depot
     *
     * @param Depot $depot
     */
    public function removeDepot(Depot $depot)
    {
        $this->depot->removeElement($depot);
    }


    /**
     * Add friend
     *
     * @param Friends $friend
     *
     * @return Utilisateur
     */
    public function addFriend(Friends $friend)
    {
        $this->friend[] = $friend;

        return $this;
    }

    /**
     * Remove friend
     *
     * @param Friends $friend
     */
    public function removeFriend(Friends $friend)
    {
        $this->friend->removeElement($friend);
    }

    /**
     * Add myfriend
     *
     * @param Friends $myfriend
     *
     * @return Utilisateur
     */
    public function addMyfriend(Friends $myfriend)
    {
        $this->myfriend[] = $myfriend;

        return $this;
    }

    /**
     * Remove myfriend
     *
     * @param Friends $myfriend
     */
    public function removeMyfriend(Friends $myfriend)
    {
        $this->myfriend->removeElement($myfriend);
    }

    /**
     * Add commentaire
     *
     * @param Commentaire $commentaire
     *
     * @return Utilisateur
     */
    public function addCommentaire(Commentaire $commentaire)
    {
        $this->commentaire[] = $commentaire;

        return $this;
    }

    /**
     * Remove commentaire
     *
     * @param Commentaire $commentaire
     */
    public function removeCommentaire(Commentaire $commentaire)
    {
        $this->commentaire->removeElement($commentaire);
    }

    /**
     * Get commentaire
     *
     * @return Collection
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Get depot
     *
     * @return Collection
     */
    public function getDepot()
    {
        return $this->depot;
    }

    /**
     * @return string
     */
    public function getLoadIdentite()
    {
        return $this->loadIdentite;
    }

    /**
     * Set loadIdentite
     *
     * @param string $loadIdentite
     *
     * @return Utilisateur
     */
    public function setLoadIdentite($loadIdentite)
    {
        $this->loadIdentite = $loadIdentite;
    }

    /**
     * @return string
     */
    public function getLoadJustificatifDomicile()
    {
        return $this->loadJustificatifDomicile;
    }

    /**
     * Set loadJustificatifDomicile
     *
     * @param string $loadJustificatifDomicile
     *
     * @return Utilisateur
     */
    public function setLoadJustificatifDomicile($loadJustificatifDomicile)
    {
        $this->loadJustificatifDomicile = $loadJustificatifDomicile;
    }


    /**
     * @return string
     */
    public function getJustificatifSelfie()
    {
        return $this->justificatifSelfie;
    }

    /**
     * Set justificatifSelfie
     *
     * @param string $justificatifSelfie
     *
     * @return Utilisateur
     */
    public function setJustificatifSelfie($justificatifSelfie)
    {
        $this->justificatifSelfie = $justificatifSelfie;
    }


    /**
     * @return int
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set validated
     *
     * @param int $validated
     *
     * @return Utilisateur
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;
    }


    /**
     * Add vendre
     *
     * @param Vendre $vendre
     *
     * @return Utilisateur
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
     * Add adressewallet
     *
     * @param Adressewallet $adressewallet
     *
     * @return Utilisateur
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
     * Add transfertcrypto
     *
     * @param Transfertcrypto $transfertcrypto
     *
     * @return Utilisateur
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
     * @return Utilisateur
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

    /**
     * Add transfertmyfriend
     *
     * @param Transfertfriend $transfertmyfriend
     *
     * @return Utilisateur
     */
    public function addTransfertmyfriend(Transfertfriend $transfertmyfriend)
    {
        $this->transfertmyfriend[] = $transfertmyfriend;

        return $this;
    }

    /**
     * Remove transfertmyfriend
     *
     * @param Transfertfriend $transfertmyfriend
     */
    public function removeTransfertmyfriend(Transfertfriend $transfertmyfriend)
    {
        $this->transfertmyfriend->removeElement($transfertmyfriend);
    }

    /**
     * Get transfertmyfriend
     *
     * @return Collection
     */
    public function getTransfertmyfriend()
    {
        return $this->transfertmyfriend;
    }

    /**
     * Set iban
     *
     * @param string $iban
     *
     * @return Utilisateur
     */
    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * Get iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set bic
     *
     * @param string $bic
     *
     * @return Utilisateur
     */
    public function setBic($bic)
    {
        $this->bic = $bic;

        return $this;
    }

    /**
     * Get bic
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Add retrait
     *
     * @param Retrait $retrait
     *
     * @return Utilisateur
     */
    public function addRetrait(Retrait $retrait)
    {
        $this->retrait[] = $retrait;

        return $this;
    }

    /**
     * Remove retrait
     *
     * @param Retrait $retrait
     */
    public function removeRetrait(Retrait $retrait)
    {
        $this->retrait->removeElement($retrait);
    }

    /**
     * Get retrait
     *
     * @return Collection
     */
    public function getRetrait()
    {
        return $this->retrait;
    }


    /**
     * Add utilisateurParrain
     *
     * @param Parainage $utilisateurParrain
     *
     * @return Utilisateur
     */
    public function addUtilisateurParrain(Parainage $utilisateurParrain)
    {
        $this->utilisateurParrain[] = $utilisateurParrain;

        return $this;
    }

    /**
     * Remove utilisateurParrain
     *
     * @param Parainage $utilisateurParrain
     */
    public function removeUtilisateurParrain(Parainage $utilisateurParrain)
    {
        $this->utilisateurParrain->removeElement($utilisateurParrain);
    }

    /**
     * Get utilisateurParrain
     *
     * @return Collection
     */
    public function getUtilisateurParrain()
    {
        return $this->utilisateurParrain;
    }

    /**
     * Add utilisateurFilleul
     *
     * @param Parainage $utilisateurFilleul
     *
     * @return Utilisateur
     */
    public function addUtilisateurFilleul(Parainage $utilisateurFilleul)
    {
        $this->utilisateurFilleul[] = $utilisateurFilleul;

        return $this;
    }

    /**
     * Remove utilisateurFilleul
     *
     * @param Parainage $utilisateurFilleul
     */
    public function removeUtilisateurFilleul(Parainage $utilisateurFilleul)
    {
        $this->utilisateurFilleul->removeElement($utilisateurFilleul);
    }

    /**
     * Get utilisateurFilleul
     *
     * @return Collection
     */
    public function getUtilisateurFilleul()
    {
        return $this->utilisateurFilleul;
    }
}
