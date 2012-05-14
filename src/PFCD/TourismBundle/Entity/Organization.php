<?php

namespace PFCD\TourismBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="organization")
 * @ORM\HasLifecycleCallbacks()
 */
class Organization implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="username", type="string", length=16, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(name="password", type="string", length=40)
     */
    private $password;

    /**
     * @ORM\Column(name="salt", type="string", length=32, nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(name="email", type="string", length=64, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="phone", type="string", length=16, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(name="acronim", type="string", length=8, nullable=true)
     */
    private $acronim;

    /**
     * @ORM\Column(name="short_desc", type="string", length=512)
     */
    private $shortDesc;

    /**
     * @ORM\Column(name="full_desc", type="text", nullable=true)
     */
    private $fullDesc;

    /**
     * @ORM\Column(name="postal_code", type="string", length=16, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(name="city", type="string", length=32, nullable=true)
     */
    private $city;

    /**
     * @ORM\OneToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $country;

    /**
     * @var string $locale
     *
     * @ORM\Column(name="locale", type="string", length=7, nullable=true)
     */
    private $locale;
    
    /**
     * @ORM\Column(name="foundation_year", type="smallint", nullable=true)
     */
    private $foundationYear;

    /**
     * @ORM\Column(name="geolocation", type="string", length=32, nullable=true)
     */
    private $geolocation;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var integer $status 0=>Pending activation, 1=>Active, 2=>Inactive, 3=>Deleted
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;
    
    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="activity")
     */
    private $activities;
    
    /**
     * @ORM\OneToMany(targetEntity="News", mappedBy="news")
     */
    private $news;

    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
        $this->status = 1;
        $this->activities = new ArrayCollection();
        $this->news = new ArrayCollection();
        
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * @ORM\preUpdate
     */
    public function setUpdatedValue()
    {
       $this->setUpdated(new \DateTime());
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('username', new NotBlank());
        $metadata->addPropertyConstraint('password', new NotBlank());
        $metadata->addPropertyConstraint('email', new NotBlank());
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('shortDesc', new NotBlank());
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array('ROLE_ORGANIZATION');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    
    public function equals(UserInterface $user)
    {
        return $this->username === $user->getUsername();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        if ($this->password != $password)
        {
            $this->password = sha1($password);
        }
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
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * Set phone
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set acronim
     *
     * @param string $acronim
     */
    public function setAcronim($acronim)
    {
        $this->acronim = $acronim;
    }

    /**
     * Get acronim
     *
     * @return string 
     */
    public function getAcronim()
    {
        return $this->acronim;
    }

    /**
     * Set shortDesc
     *
     * @param string $shortDesc
     */
    public function setShortDesc($shortDesc)
    {
        $this->shortDesc = $shortDesc;
    }

    /**
     * Get shortDesc
     *
     * @return string 
     */
    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    /**
     * Set fullDesc
     *
     * @param text $fullDesc
     */
    public function setFullDesc($fullDesc)
    {
        $this->fullDesc = $fullDesc;
    }

    /**
     * Get fullDesc
     *
     * @return text 
     */
    public function getFullDesc()
    {
        return $this->fullDesc;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param PFCD\TourismBundle\Entity\Country $country
     */
    public function setCountry(\PFCD\TourismBundle\Entity\Country $country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return PFCD\TourismBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Set foundationYear
     *
     * @param smallint $foundationYear
     */
    public function setFoundationYear($foundationYear)
    {
        $this->foundationYear = $foundationYear;
    }

    /**
     * Get foundationYear
     *
     * @return smallint 
     */
    public function getFoundationYear()
    {
        return $this->foundationYear;
    }

    /**
     * Set geolocation
     *
     * @param string $geolocation
     */
    public function setGeolocation($geolocation)
    {
        $this->geolocation = $geolocation;
    }

    /**
     * Get geolocation
     *
     * @return string 
     */
    public function getGeolocation()
    {
        return $this->geolocation;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return datetime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add activities
     *
     * @param PFCD\TourismBundle\Entity\Activity $activities
     */
    public function addActivity(\PFCD\TourismBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;
    }

    /**
     * Get activities
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Add news
     *
     * @param PFCD\TourismBundle\Entity\News $news
     */
    public function addNews(\PFCD\TourismBundle\Entity\News $news)
    {
        $this->news[] = $news;
    }

    /**
     * Get news
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getNews()
    {
        return $this->news;
    }
    
}