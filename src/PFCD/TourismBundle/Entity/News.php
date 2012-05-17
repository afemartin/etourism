<?php

namespace PFCD\TourismBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="news")
 * @ORM\HasLifecycleCallbacks()
 */
class News
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="news")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     */
    private $organization;

    /**
     * @ORM\Column(name="title", type="string", length=128)
     */
    private $title;

    /**
     * @ORM\Column(name="short_desc", type="string", length=512)
     */
    private $shortDesc;

    /**
     * @ORM\Column(name="full_desc", type="text", nullable=true)
     */
    private $fullDesc;

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
    
    /**
     * @ORM\OneToMany(targetEntity="NewsComment", mappedBy="news")
     */
    private $comments;

    public function __construct()
    {
        $this->status = 1;
        $this->comments = new ArrayCollection();
        
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
        $metadata->addPropertyConstraint('organization', new NotBlank());
        $metadata->addPropertyConstraint('title', new NotBlank());
        $metadata->addPropertyConstraint('shortDesc', new NotBlank());
    }

    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set organization
     *
     * @param PFCD\TourismBundle\Entity\Organization $organization
     */
    public function setOrganization(\PFCD\TourismBundle\Entity\Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Get organization
     *
     * @return PFCD\TourismBundle\Entity\Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
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
     * Add comments
     *
     * @param PFCD\TourismBundle\Entity\NewsComment $comments
     */
    public function addNewsComment(\PFCD\TourismBundle\Entity\NewsComment $comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

}