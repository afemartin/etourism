<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use PFCD\TourismBundle\Entity\Resource;
use PFCD\TourismBundle\Entity\Category;
use PFCD\TourismBundle\Entity\Session;

/**
 * @ORM\Entity(repositoryClass="PFCD\TourismBundle\Repository\ResourceRepository")
 * @ORM\Table(name="resource")
 * @ORM\HasLifecycleCallbacks()
 */
class Resource
{
    const STATUS_ENABLED = 1;   // The resource category is enabled
    const STATUS_DELETED = 3;   // The resource category is not visible
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="resources")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    private $note;

    /**
     * @var boolean true means that this resource it is unique and may be shared
     *              by several activities, so it is necessary to check if it is
     *              already required by another session of another activity
     * 
     * @ORM\Column(name="conflict", type="boolean", nullable=true)
     */
    private $conflict;
    
    /**
     * @ORM\Column(name="date_start_lock", type="date", nullable=true)
     */
    private $dateStartLock;
    
    /**
     * @ORM\Column(name="date_end_lock", type="date", nullable=true)
     */
    private $dateEndLock;
    
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
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="resources")
     */
    private $sessions;

    public function __construct()
    {
        $this->status = self::STATUS_ENABLED;
        $this->sessions = new ArrayCollection();
        
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
     * Get name and category name to easily diferenciate between them
     *
     * @return string 
     */
    public function getNameAndCategory()
    {
        return '[' . $this->category->getName() . '] ' . $this->name;
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
     * Set dateStartLock
     *
     * @param date $dateStartLock
     */
    public function setDateStartLock($dateStartLock)
    {
        $this->dateStartLock = $dateStartLock;
    }

    /**
     * Get dateStartLock
     *
     * @return date 
     */
    public function getDateStartLock()
    {
        return $this->dateStartLock;
    }
    
    /**
     * Set dateEndLock
     *
     * @param date $dateEndLock
     */
    public function setDateEndLock($dateEndLock)
    {
        $this->dateEndLock = $dateEndLock;
    }

    /**
     * Get dateEndLock
     *
     * @return date 
     */
    public function getDateEndLock()
    {
        return $this->dateEndLock;
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
     * Set category
     *
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add sessions
     *
     * @param Session $sessions
     */
    public function addSession(Session $sessions)
    {
        $this->sessions[] = $sessions;
    }

    /**
     * Get sessions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSessions()
    {
        return $this->sessions;
    }

}