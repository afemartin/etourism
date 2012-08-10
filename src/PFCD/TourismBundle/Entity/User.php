<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use PFCD\TourismBundle\Entity\Reservation;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements AdvancedUserInterface
{
    const GENDER_UNKNOWN = 0;
    const GENDER_MALE    = 1;
    const GENDER_FEMALE  = 2;
    
    private $genderText = array('0'=>'Unknown', '1'=>'Male', '2'=>'Female');
    
    const STATUS_PENDING = 0;
    const STATUS_ENABLED = 1;
    const STATUS_LOCKED  = 2;
    const STATUS_DELETED = 3;
    
    private $statusText = array('0'=>'Pending', '1'=>'Enabled', '2'=>'Locked', '3'=>'Deleted');

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="email", type="string", length=64, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(name="salt", type="string", length=32, nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(name="firstname", type="string", length=32, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(name="lastname", type="string", length=32, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var integer $gender 0=>unknown, 1=>male, 2=>female (ISO/IEC 5218)
     *      
     * @ORM\Column(name="gender", type="smallint", nullable=true)
     */
    private $gender;

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
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var integer $status 0=>Pending activation, 1=>Enabled, 2=>Locked, 3=>Deleted
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="user")
     */
    private $reservations;

    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
        $this->gender = self::GENDER_UNKNOWN;
        $this->status = self::STATUS_PENDING;
        $this->reservations = new ArrayCollection();

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
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        
    }

    /**
     * @inheritDoc
     */
    public function equals(UserInterface $user)
    {
        return $this->email === $user->getEmail();
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
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->getEmail();
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
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Get key for reset password by mail (after change the password the link will be useless)
     *
     * @return string 
     */
    public function getResetKey()
    {
        return md5('reset-key-'.$this->password.'-salted-with-'.$this->salt);
    }

    /**
     * Get key for activate users accouynt by mail (after change the status the link will be useless)
     *
     * @return string 
     */
    public function getActivationKey()
    {
        return md5('activation-key-'.$this->status.'-salted-with-'.$this->salt);
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }
    
    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    /**
     * Set birthday
     *
     * @param date $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Get birthday
     *
     * @return date 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @return integer 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Get gender in human readable mode
     *
     * @return string
     */
    public function getGenderText()
    {
        return $this->genderText[$this->gender];
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
     * Add reservations
     *
     * @param Reservation $reservations
     */
    public function addReservation(Reservation $reservations)
    {
        $this->reservations[] = $reservations;
    }

    /**
     * Get reservations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
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