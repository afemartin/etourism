<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Resource;

/**
 * @ORM\Entity
 * @ORM\Table(name="activity_resource")
 * @ORM\HasLifecycleCallbacks()
 */
class ActivityResource
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
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="resources")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="Resource", inversedBy="activities")
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     */
    private $resource;

    /**
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
     * Set activity
     *
     * @param Activity $activity
     */
    public function setActivity(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Get activity
     *
     * @return Activity 
     */
    public function getActivity()
    {
        return $this->activity;
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