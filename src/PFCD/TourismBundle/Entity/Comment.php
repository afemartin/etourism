<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use PFCD\TourismBundle\Entity\User;
use PFCD\TourismBundle\Entity\Organization;
use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\News;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
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
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     */
    private $organization;

    /**
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="comments")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     */
    private $activity;
    
    /**
     * @ORM\ManyToOne(targetEntity="News", inversedBy="comments")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id")
     */
    private $news;

    /**
     * @var integer $punctuation 1=>very poor, 2=>poor, 3=>good, 4=>very good, 5=>excellent
     * 
     * @ORM\Column(name="punctuation", type="smallint", nullable=true)
     */
    private $punctuation;

    /**
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @ORM\OneToOne(targetEntity="Comment")
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
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
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
     * Set news
     *
     * @param News $news
     */
    public function setNews(News $news)
    {
        $this->news = $news;
    }

    /**
     * Get news
     *
     * @return News 
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Set punctuation
     *
     * @param smallint $punctuation
     */
    public function setPunctuation($punctuation)
    {
        $this->punctuation = $punctuation;
    }

    /**
     * Get punctuation
     *
     * @return smallint 
     */
    public function getPunctuation()
    {
        return $this->punctuation;
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