<?php

namespace PFCD\TourismBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Entity\Language;
use PFCD\TourismBundle\Entity\Comment;
use PFCD\TourismBundle\Entity\Image;

/**
 * @ORM\Entity
 * @ORM\Table(name="article")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    const STATUS_PENDING = 0; // visible to author but not to users
    const STATUS_ENABLED = 1; // visible to everyone (allow comments)
    const STATUS_LOCKED  = 2; // visible to everyone (no comments)
    const STATUS_DELETED = 3; // only visible to admin

    private $statusText = array('0' => 'Pending', '1' => 'Enabled', '2' => 'Locked', '3' => 'Deleted');
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="articles")
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
     * @ORM\OneToMany(targetEntity="Image", mappedBy="article")
     */
    private $gallery;
        
    /**
     * @ORM\ManyToMany(targetEntity="Language")
     * @ORM\JoinTable(name="article_language")
     */
    private $languages;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article")
     */
    private $comments;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->gallery = new ArrayCollection();
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
        return 'uploads/org' . $this->organization->getId() . '/art' . $this->id;
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
     * Add languages
     *
     * @param Language $languages
     */
    public function addLanguage(Language $languages)
    {
        $this->languages[] = $languages;
    }

    /**
     * Get languages
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getLanguages()
    {
        return $this->languages;
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