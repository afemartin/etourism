<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Entity\ActivityComment;

/**
 * @ORM\Entity
 * @ORM\Table(name="activity")
 * @ORM\HasLifecycleCallbacks()
 */
class Activity
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
     * @ORM\Column(name="geolocation", type="string", length=32, nullable=true)
     */
    private $geolocation;
    
    /**
     * Uploaded file
     */
    private $file = null;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;
    
    /**
     * @ORM\Column(type="string", nullable=true)
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
     * @var integer $status 0=>Inactive, 1=>Active, 2=>Deleted
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;
    
    /**
     * @ORM\OneToMany(targetEntity="ActivityComment", mappedBy="activity")
     */
    private $comments;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
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
     * Set geolocation
     *
     * @param string $geolocation
     */
    public function setGeolocation($geolocation)
    {
        $address = urlencode($geolocation);
        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
        $json = json_decode($json);

        if ($json->{'status'} == 'OK')
        {
            $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $lng = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

            $this->geolocation = $lat . ',' . $lng;
        }
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
            $images = explode("|", $this->image);
            $image = 'act' . $this->id . '-n' . count($images) . '.' . $this->file->guessExtension();
            
            $images[] = $image;
            $this->image = implode("|", $images);
            
            $this->file->move($this->getUploadRootDir(), $image);
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
        $images = explode("|", $this->image);

        if (!empty($images))
        {
            return $this->getUploadDir() . '/' . $images[0];
        }
        
        return null;
    }

    /**
     * Get images
     *
     * @return array 
     */
    public function getImages()
    {
        $images = explode("|", $this->image);
        
        if (!empty($images))
        {
            foreach ($images as &$image)
            {
                $image = $this->getUploadDir() . '/' . $image;
            }
            return $images;
        }
        
        return null;
    }
    
    /**
     * The absolute directory path where uploaded documents should be saved
     * 
     * @return type 
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view
     * 
     * @return string 
     */
    protected function getUploadDir()
    {
        return 'uploads/activities/images';
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
     * Add comments
     *
     * @param ActivityComment $comments
     */
    public function addActivityComment(ActivityComment $comments)
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