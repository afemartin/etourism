<?php

namespace PFCD\TourismBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Entity\Comment;
use PFCD\TourismBundle\Entity\Image;

/**
 * @ORM\Entity(repositoryClass="PFCD\TourismBundle\Repository\ActivityRepository")
 * @ORM\Table(name="activity")
 * @ORM\HasLifecycleCallbacks()
 */
class Activity
{
    const STATUS_PENDING = 0;   // The activity is not visible (only in preview mode)
    const STATUS_ENABLED = 1;   // The activity is visible and can receive reservations
    const STATUS_LOCKED  = 2;   // The activity is visible but can not receive reservations
    const STATUS_DELETED = 3;   // The activity is not visible
    
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
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=128)
     */
    private $title;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="short_desc", type="string", length=512)
     */
    private $shortDesc;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="full_desc", type="text", nullable=true)
     */
    private $fullDesc;

    /**
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var string $currency ISO 4217
     * @link http://en.wikipedia.org/wiki/ISO_4217
     * 
     * @ORM\Column(name="currency", type="string", length=3)
     */
    private $currency;

    /**
     * @var integer
     * 
     * @ORM\Column(name="capacity", type="smallint")
     */
    private $capacity;
    
    /**
     * @ORM\Column(name="duration_days", type="smallint")
     */
    private $durationDays;
    
    /**
     * @ORM\Column(name="duration_time", type="time")
     */
    private $durationTime;

    /**
     * @ORM\Column(name="geolocation", type="string", length=32, nullable=true)
     */
    private $geolocation;
    
    /**
     * @ORM\Column(name="map_zoom", type="smallint", nullable=true)
     */
    private $zoom;
    
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
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    private $note;
    
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
     * @ORM\Column(name="languages", type="string", nullable=true)
     */
    private $languages;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="activity")
     */
    private $comments;
    
    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="activities")
     * @ORM\JoinTable(name="activity_category")
     */
    private $categories;
    
    public function __construct()
    {
        $this->durationDays = 0;
        $this->status = self::STATUS_PENDING;
        $this->gallery = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        
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
     * Get message composed by the title and the corresponding status
     * (only if it is different from STATUS_ENABLE)
     * 
     * @return string 
     */
    public function getTitleAndStatus()
    {
        if ($this->status != self::STATUS_ENABLED)
        {
            // Retrieve the container to get the translator service:
            // This practice it is not recommended but for this case it is the
            // only way to translate the status and put together with the title
            global $kernel;
            if ('AppCache' == get_class($kernel)) $kernel = $kernel->getKernel();
            $translator = $kernel->getContainer()->get('translator');

            return $this->title . ' (' . $translator->trans($this->getStatusText()) . ')';
        }
        else
        {
            return $this->title;
        }
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
     * Set durationDays
     *
     * @param smallint $durationDays
     */
    public function setDurationDays($durationDays)
    {
        $this->durationDays = $durationDays;
    }

    /**
     * Get durationDays
     *
     * @return smallint 
     */
    public function getDurationDays()
    {
        return $this->durationDays;
    }

    /**
     * Set durationTime
     *
     * @param smallint $durationTime
     */
    public function setDurationTime($durationTime)
    {
        $this->durationTime = $durationTime;
    }

    /**
     * Get durationTime
     *
     * @return smallint 
     */
    public function getDurationTime()
    {
        return $this->durationTime;
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
     * Set zoom
     *
     * @param string $zoom
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
    }

    /**
     * Get zoom
     *
     * @return string 
     */
    public function getZoom()
    {
        return $this->zoom;
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
        return 'entity.activity.field.status.' . $this->status;
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
     * Set languages
     *
     * @param array $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = implode('|', $languages);
    }

    /**
     * Get languages
     *
     * @return array
     */
    public function getLanguages()
    {
        return explode('|', $this->languages);
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
    
    /**
     * Add category
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
    }
    
    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
}