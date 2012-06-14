<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Resource;
use PFCD\TourismBundle\Entity\News;

/**
 * @ORM\Entity
 * @ORM\Table(name="organization")
 * @ORM\HasLifecycleCallbacks()
 */
class Organization implements AdvancedUserInterface
{
    const STATUS_PENDING = 0;
    const STATUS_ENABLED = 1;
    const STATUS_LOCKED  = 2;
    const STATUS_DELETED = 3;

    private $statusText = array('0' => 'Pending', '1' => 'Enabled', '2' => 'Locked', '3' => 'Deleted');

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
     * @ORM\Column(name="email", type="string", length=64, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="password", type="string", length=40)
     */
    private $password;

    /**
     * @ORM\Column(name="salt", type="string", length=32, nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(name="acronim", type="string", length=8, nullable=true)
     */
    private $acronim;

    /**
     * @ORM\Column(name="short_desc", type="string", length=512, nullable=true)
     */
    private $shortDesc;

    /**
     * @ORM\Column(name="full_desc", type="text", nullable=true)
     */
    private $fullDesc;

    /**
     * @ORM\Column(name="foundation_year", type="smallint", nullable=true)
     */
    private $foundationYear;

    /**
     * @ORM\Column(name="geolocation", type="string", length=32, nullable=true)
     */
    private $geolocation;

    /**
     * @var string $country ISO 3166-1 alpha-2
     * @link http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
     * 
     * @ORM\Column(name="country", type="string", length=2, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(name="city", type="string", length=32, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(name="address", type="string", length=32, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(name="postal_code", type="string", length=16, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(name="phone", type="string", length=16, nullable=true)
     */
    private $phone;

    /**
     * @var string $locale ISO 639-1
     * @link http://en.wikipedia.org/wiki/ISO_639-1
     *
     * @ORM\Column(name="locale", type="string", length=2, nullable=true)
     */
    private $locale;

    /**
     * Uploaded file
     */
    private $file = null;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $logo;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $video;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var integer $status 0=>Pending, 1=>Enabled, 2=>Locked, 3=>Deleted
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="activity")
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="Resource", mappedBy="resource")
     */
    private $resources;

    /**
     * @ORM\OneToMany(targetEntity="News", mappedBy="news")
     */
    private $news;

    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
        $this->status = self::STATUS_PENDING;
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

    public function equals(UserInterface $organization)
    {
        return $this->username === $organization->getUsername() || $this->email === $organization->getEmail();
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
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $encoder = new MessageDigestPasswordEncoder('sha1', false, 1);
        $this->password = $encoder->encodePassword($password, $this->getSalt());
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
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
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
     * Set address
     *
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
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
     * Set file
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set logo
     */
    public function setLogo()
    {
        if ($this->file !== null)
        {
            $this->logo = 'logo.' . $this->file->guessExtension();
            $this->file->move(__DIR__ . '/../../../../web/' . $this->getUploadDir(), $this->logo);
            $this->logo = $this->getUploadDir() . '/' . $this->logo;
            unset($this->file);
        }
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }
    
    /**
     * Get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view
     * 
     * @return string 
     */
    protected function getUploadDir()
    {
        return 'uploads/org' . $this->id;
    }
    
    /**
     * Set video
     *
     * @param string $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * Get video
     *
     * @return string 
     */
    public function getVideo()
    {
        return $this->video;
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
     * Get status in human readable mode
     *
     * @return string
     */
    public function getStatusText()
    {
        return $this->statusText[$this->status];
    }

    /**
     * Add activities
     *
     * @param Activity $activities
     */
    public function addActivity(Activity $activities)
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
     * Add resources
     *
     * @param Resource $resources
     */
    public function addResource(Resource $resources)
    {
        $this->resources[] = $resources;
    }

    /**
     * Get resources
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Add news
     *
     * @param News $news
     */
    public function addNews(News $news)
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

    /**
     * Checks whether the user's account has expired.
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * @return Boolean true if the user is not locked, false otherwise
     */
    public function isAccountNonLocked()
    {
        return $this->status != self::STATUS_LOCKED;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * @return Boolean true if the user is enabled, false otherwise
     */
    public function isEnabled()
    {
        return $this->status == self::STATUS_ENABLED;
    }
    
}