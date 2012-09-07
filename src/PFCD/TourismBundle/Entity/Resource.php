<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use PFCD\TourismBundle\Entity\Organization;

use PFCD\TourismBundle\Entity\Activity;

/**
 * @ORM\Entity(repositoryClass="PFCD\TourismBundle\Repository\ResourceRepository")
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

    const STATUS_ENABLED = 1;   // The resource is available rigth now
    const STATUS_LOCKED  = 2;   // The resource is not available rigth now
    const STATUS_DELETED = 3;   // The resource wont be available anymore
    
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
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    private $note;

    /**
     * @var integer $status 0=>Unknown, 1=>Material (internal), 2=>Human (internal), 3=>Material (external), 4=>Human (external)
     * 
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var boolean true means that this resource it is unique and may be shared
     *              by several activities, so it is necessary to check if it is
     *              already required by another session of another activity
     * 
     * @ORM\Column(name="conflict", type="boolean", nullable=true)
     */
    private $conflict;
    
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
     * @ORM\ManyToMany(targetEntity="Activity", mappedBy="resources")
     */
    private $activities;

    public function __construct()
    {
        $this->status = self::STATUS_ENABLED;
        $this->activities = new ArrayCollection();
        
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
     * Set note
     *
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
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
        return $this->type ? 'entity.resource.field.type.' . $this->type : null;
    }
    
    /**
     * Set conflict
     *
     * @param boolean $conflict
     */
    public function setConflict($conflict)
    {
        $this->conflict = $conflict;
    }

    /**
     * Get conflict
     *
     * @return boolean 
     */
    public function getConflict()
    {
        return $this->conflict;
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
        return 'entity.resource.field.status.' . $this->status;
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

}