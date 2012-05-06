<?php

namespace PFCD\TourismBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_comment")
 * @ORM\HasLifecycleCallbacks()
 */
class NewsComment
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="News", inversedBy="comments")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id")
     */
    private $news;

    /**
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @ORM\OneToOne(targetEntity="NewsComment")
     * @ORM\JoinColumn(name="reply_at", referencedColumnName="id")
     */
    private $replyAt;

    /**
     * @ORM\Column(name="good", type="smallint", nullable=true)
     */
    private $good;

    /**
     * @ORM\Column(name="bad", type="smallint", nullable=true)
     */
    private $bad;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var integer $status 0=>Inactive, 1=>Active, 2=>Flaged, 3=>Deleted
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    public function __construct()
    {
        $this->status = 1;
        
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
        $metadata->addPropertyConstraint('user', new NotBlank());
        $metadata->addPropertyConstraint('news', new NotBlank());
        $metadata->addPropertyConstraint('comment', new NotBlank());
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
     * Set user
     *
     * @param PFCD\TourismBundle\Entity\User $user
     */
    public function setUser(\PFCD\TourismBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return PFCD\TourismBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set news
     *
     * @param PFCD\TourismBundle\Entity\News $news
     */
    public function setNews(\PFCD\TourismBundle\Entity\News $news)
    {
        $this->news = $news;
    }

    /**
     * Get news
     *
     * @return PFCD\TourismBundle\Entity\News 
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Set comment
     *
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return text 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set replyAt
     *
     * @param smallint $replyAt
     */
    public function setReplyAt($replyAt)
    {
        $this->replyAt = $replyAt;
    }

    /**
     * Get replyAt
     *
     * @return smallint 
     */
    public function getReplyAt()
    {
        return $this->replyAt;
    }

    /**
     * Set good
     *
     * @param integer $good
     */
    public function setGood($good)
    {
        $this->good = $good;
    }

    /**
     * Get good
     *
     * @return integer 
     */
    public function getGood()
    {
        return $this->good;
    }

    /**
     * Set bad
     *
     * @param integer $bad
     */
    public function setBad($bad)
    {
        $this->bad = $bad;
    }

    /**
     * Get bad
     *
     * @return integer 
     */
    public function getBad()
    {
        return $this->bad;
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

}