<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use PFCD\TourismBundle\Entity\Organization;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{
    const STATUS_ENABLED = 1;   // The resource is available rigth now
    const STATUS_DELETED = 3;   // The resource wont be available anymore
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="categories")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     */
    private $organization;

    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var integer $status 1=>Enabled, 3=>Deleted
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Resource", mappedBy="category")
     */
    private $resources;
    
    /**
     * @ORM\ManyToMany(targetEntity="Activity", mappedBy="categories")
     */
    private $activities;

    public function __construct()
    {
        $this->status = self::STATUS_ENABLED;
        $this->resources = new ArrayCollection();
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
        return 'entity.category.field.status.' . $this->status;
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
     * Add resource
     *
     * @param Resource $resource
     */
    public function addResource(Resource $resource)
    {
        $this->resources[] = $resource;
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
     * Add activity
     *
     * @param Activity $activity
     */
    public function addActivity(Activity $activity)
    {
        $this->activities[] = $activity;
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