<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use PFCD\TourismBundle\Entity\Organization;

use PFCD\TourismBundle\Entity\ActivityResource;
use PFCD\TourismBundle\Entity\ResourceVariation;

/**
 * @ORM\Entity
 * @ORM\Table(name="resource")
 * @ORM\HasLifecycleCallbacks()
 */
class Resource
{
    const TYPE_UNKNOWN      = 0;
    const TYPE_MATERIAL_INT = 1;
    const TYPE_HUMAN_INT    = 2;
    const TYPE_MATERIAL_EXT = 3;
    const TYPE_HUMAN_EXT    = 4;

    private $typeText = array('0' => 'Unknown', '1' => 'Material (internal)', '2' => 'Human (internal)', '3' => 'Material (external)', '4' => 'Human (external)');

    const STATUS_ENABLED = 1;
    const STATUS_DELETED = 3;

    private $statusText = array('1' => 'Enabled', '3' => 'Deleted');
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="resources")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     */
    private $organization;

    /**
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(name="description", type="string", length=512)
     */
    private $description;

    /**
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var string $currency ISO 4217
     * @link http://en.wikipedia.org/wiki/ISO_4217
     * 
     * @ORM\Column(name="currency", type="string", length=3, nullable=true)
     */
    private $currency;

    /**
     * @ORM\Column(name="date_start", type="date", nullable=true)
     */
    private $dateStart;

    /**
     * @ORM\Column(name="date_end", type="date", nullable=true)
     */
    private $dateEnd;

    /**
     * @ORM\Column(name="time_start", type="time", nullable=true)
     */
    private $timeStart;

    /**
     * @ORM\Column(name="time_end", type="time", nullable=true)
     */
    private $timeEnd;

    /**
     * @ORM\Column(name="monday", type="boolean", nullable=true)
     */
    private $monday;

    /**
     * @ORM\Column(name="tuesday", type="boolean", nullable=true)
     */
    private $tuesday;

    /**
     * @ORM\Column(name="wednesday", type="boolean", nullable=true)
     */
    private $wednesday;

    /**
     * @ORM\Column(name="thursday", type="boolean", nullable=true)
     */
    private $thursday;

    /**
     * @ORM\Column(name="friday", type="boolean", nullable=true)
     */
    private $friday;

    /**
     * @ORM\Column(name="saturday", type="boolean", nullable=true)
     */
    private $saturday;

    /**
     * @ORM\Column(name="sunday", type="boolean", nullable=true)
     */
    private $sunday;

    /**
     * @var integer $status 0=>Unknown, 1=>Material (internal), 2=>Human (internal), 3=>Material (external), 4=>Human (external)
     * 
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="amount", type="smallint")
     */
    private $amount;    
   
    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;
    
    /**
     * @var integer $status 1=>Enabled, 3=>Deleted
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;
    
    /**
     * @ORM\OneToMany(targetEntity="ActivityResource", mappedBy="resource")
     */
    private $activities;
    
    /**
     * @ORM\OneToMany(targetEntity="ResourceVariation", mappedBy="resource")
     */
    private $variations;
    

    public function __construct()
    {
        $this->status = self::STATUS_ENABLED;
        $this->activities = new ArrayCollection();
        $this->variations = new ArrayCollection();
        
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set dateStart
     *
     * @param date $dateStart
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    }

    /**
     * Get dateStart
     *
     * @return date 
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param date $dateEnd
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    /**
     * Get dateEnd
     *
     * @return date 
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set timeStart
     *
     * @param time $timeStart
     */
    public function setTimeStart($timeStart)
    {
        $this->timeStart = $timeStart;
    }

    /**
     * Get timeStart
     *
     * @return time 
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }

    /**
     * Set timeEnd
     *
     * @param time $timeEnd
     */
    public function setTimeEnd($timeEnd)
    {
        $this->timeEnd = $timeEnd;
    }

    /**
     * Get timeEnd
     *
     * @return time 
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * Set daysWeek
     *
     * @param array
     */
    public function setDaysWeek($daysWeek)
    {
        $this->setMonday(false);
        $this->setTuesday(false);
        $this->setWednesday(false);
        $this->setThursday(false);
        $this->setFriday(false);
        $this->setSaturday(false);
        $this->setSunday(false);
        
        foreach ($daysWeek as $day)
        {
            if ($day == 'monday')
                $this->setMonday(true);
            elseif ($day == 'tuesday')
                $this->setTuesday(true);
            elseif ($day == 'wednesday')
                $this->setWednesday(true);
            elseif ($day == 'thursday')
                $this->setThursday(true);
            elseif ($day == 'friday')
                $this->setFriday(true);
            elseif ($day == 'saturday')
                $this->setSaturday(true);
            elseif ($day == 'sunday')
                $this->setSunday(true);
        }
    }

    /**
     * Get daysWeek
     *
     * @return array
     */
    public function getDaysWeek()
    {
        $daysWeek = array();
        if ($this->getMonday())
            $daysWeek[] = 'monday';
        if ($this->getTuesday())
            $daysWeek[] = 'tuesday';
        if ($this->getWednesday())
            $daysWeek[] = 'wednesday';
        if ($this->getThursday())
            $daysWeek[] = 'thursday';
        if ($this->getFriday())
            $daysWeek[] = 'friday';
        if ($this->getSaturday())
            $daysWeek[] = 'saturday';
        if ($this->getSunday())
            $daysWeek[] = 'sunday';
        return $daysWeek;
    }
    
    /**
     * Set monday
     *
     * @param boolean $monday
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;
    }

    /**
     * Get monday
     *
     * @return boolean 
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * Set tuesday
     *
     * @param boolean $tuesday
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;
    }

    /**
     * Get tuesday
     *
     * @return boolean 
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * Set wednesday
     *
     * @param boolean $wednesday
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;
    }

    /**
     * Get wednesday
     *
     * @return boolean 
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * Set thursday
     *
     * @param boolean $thursday
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;
    }

    /**
     * Get thursday
     *
     * @return boolean 
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * Set friday
     *
     * @param boolean $friday
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;
    }

    /**
     * Get friday
     *
     * @return boolean 
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * Set saturday
     *
     * @param boolean $saturday
     */
    public function setSaturday($saturday)
    {
        $this->saturday = $saturday;
    }

    /**
     * Get saturday
     *
     * @return boolean 
     */
    public function getSaturday()
    {
        return $this->saturday;
    }

    /**
     * Set sunday
     *
     * @param boolean $sunday
     */
    public function setSunday($sunday)
    {
        $this->sunday = $sunday;
    }

    /**
     * Get sunday
     *
     * @return boolean 
     */
    public function getSunday()
    {
        return $this->sunday;
    }

    /**
     * Set type
     *
     * @param smallint $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return smallint 
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Get type in human readable mode
     *
     * @return string
     */
    public function getTypeText()
    {
        return $this->typeText[$this->type];
    }

    /**
     * Set amount
     *
     * @param smallint $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return smallint 
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @param smallint $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return smallint 
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
     * Set organization
     *
     * @param Organization $organization
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Get organization
     *
     * @return Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Add activities
     *
     * @param ActivityResource $activities
     */
    public function addActivityResource(ActivityResource $activities)
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
     * Add variations
     *
     * @param ResourceVariation $variations
     */
    public function addResourceVariation(ResourceVariation $variations)
    {
        $this->variations[] = $variations;
    }

    /**
     * Get variations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getVariations()
    {
        return $this->variations;
    }
}