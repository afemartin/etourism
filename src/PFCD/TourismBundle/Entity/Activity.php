<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Entity\Resource;
use PFCD\TourismBundle\Entity\Comment;
use PFCD\TourismBundle\Entity\Image;

/**
 * @ORM\Entity(repositoryClass="PFCD\TourismBundle\Repository\ActivityRepository")
 * @ORM\Table(name="activity")
 * @ORM\HasLifecycleCallbacks()
 */
class Activity
{
    const STATUS_PENDING = 0; // visible to author but not to users
    const STATUS_ENABLED = 1; // visible to everyone (allow comments and reservations)
    const STATUS_LOCKED  = 2; // visible to everyone (no comments or reservations)
    const STATUS_DELETED = 3; // only visible to admin

    private $statusText = array('0' => 'Pending', '1' => 'Enabled', '2' => 'Locked', '3' => 'Deleted');
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="activities")
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
     * @var integer
     * 
     * @ORM\Column(name="capacity", type="smallint")
     */
    private $capacity;
    
    /**
     * @ORM\Column(name="duration", type="smallint", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(name="geolocation", type="string", length=32, nullable=true)
     */
    private $geolocation;
    
    /**
     * Uploaded file
     */
    private $file = null;
    
    /**
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;
    
    /**
     * @ORM\Column(name="video", type="string", nullable=true)
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
     * @ORM\OneToMany(targetEntity="Image", mappedBy="activity")
     */
    private $gallery;
    
    /**
     * @ORM\OneToMany(targetEntity="Session", mappedBy="activity")
     */
    private $sessions;
    
    /**
     * @ORM\ManyToMany(targetEntity="Resource", inversedBy="activities")
     * @ORM\JoinTable(name="activity_resource")
     */
    private $resources;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="activity")
     */
    private $comments;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->gallery = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->resources = new ArrayCollection();
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
     * Set title
     *
     * @param integer $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return integer 
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
     * @param integer $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency
     *
     * @return integer 
     */
    public function getCurrency()
    {
        return $this->currency;
    }
    
    /**
     * Set capacity
     *
     * @param smallint $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * Get capacity
     *
     * @return smallint 
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set duration
     *
     * @param smallint $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Get duration
     *
     * @return smallint 
     */
    public function getDuration()
    {
        return $this->duration;
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
     * Set image
     */
    public function setImage()
    {
        if ($this->file !== null)
        {
            $this->image = 'image.' . $this->file->guessExtension();
            $this->file->move(__DIR__ . '/../../../../web/' . $this->getUploadDir(), $this->image);
            $this->image = $this->getUploadDir() . '/' . $this->image;
            unset($this->file);
        }
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view
     * 
     * @return string 
     */
    protected function getUploadDir()
    {
        return 'uploads/org' . $this->organization->getId() . '/act' . $this->id;
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
     * Add images to the gallery
     *
     * @param Image $image
     */
    public function addImage(Image $image)
    {
        $this->gallery[] = $image;
    }

    /**
     * Set gallery of images
     *
     * @return 
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * Get gallery of images
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGallery()
    {
        return $this->gallery;
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
     * Add comments
     *
     * @param Comment $comments
     */
    public function addComment(Comment $comments)
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