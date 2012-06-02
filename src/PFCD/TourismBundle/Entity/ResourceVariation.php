<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use PFCD\TourismBundle\Entity\Resource;

/**
 * @ORM\Entity
 * @ORM\Table(name="resource_variation")
 * @ORM\HasLifecycleCallbacks()
 */
class ResourceVariation
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
     * @ORM\ManyToOne(targetEntity="Resource", inversedBy="variations")
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     */
    private $resource;

    /**
     * @ORM\Column(name="reason", type="string", length=255)
     */
    private $reason;

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
     * @var integer if negative then variation <= (resource.ammount + resorce.variation-same-period)
     * 
     * @ORM\Column(name="variation", type="smallint")
     */
    private $variation;    
   
    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;
    
    /**
     * @var integer $status 0=>Inactive, 1=>Active, 2=>Deleted
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;
    
    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        
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
     * Set reason
     *
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
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
     * Set variation
     *
     * @param smallint $variation
     */
    public function setVariation($variation)
    {
        $this->variation = $variation;
    }

    /**
     * Get variation
     *
     * @return smallint 
     */
    public function getVariation()
    {
        return $this->variation;
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
     * Set resource
     *
     * @param Resource $resource
     */
    public function setResource(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get resource
     *
     * @return Resource 
     */
    public function getResource()
    {
        return $this->resource;
    }

}