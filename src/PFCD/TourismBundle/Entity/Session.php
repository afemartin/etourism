<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use PFCD\TourismBundle\Entity\Activity;
use PFCD\TourismBundle\Entity\Reservation;

/**
 * @ORM\Entity(repositoryClass="PFCD\TourismBundle\Repository\SessionRepository")
 * @ORM\Table(name="session")
 * @ORM\HasLifecycleCallbacks()
 */
class Session
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
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="sessions")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     */
    private $activity;
    
    /**
     * @ORM\Column(name="date", type="date")
     */
    private $date;
    
    /**
     * @var integer $dayOfWeek ISO-8601 1=>Monday, 2=>Tuesday, 3=>Wednesday, 4=>Thursday, 5=>Friday, 6=>Saturday, 7=>Sunday
     * @link http://en.wikipedia.org/wiki/ISO_8601
     * 
     * @ORM\Column(name="day_of_week", type="smallint")
     */
    private $dayOfWeek;

    /**
     * @ORM\Column(name="time", type="time", nullable=true)
     */
    private $time;

    /**
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    private $comment;

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
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="session")
     */
    private $reservations;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->reservations = new ArrayCollection();
        
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
     * Set date
     *
     * @param date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
        $this->dayOfWeek = $date->format('N');
    }

    /**
     * Get date
     *
     * @return date 
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * Get day of week
     *
     * @return dayOfWeek 
     */
    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    /**
     * Set time
     *
     * @param time $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * Get time
     *
     * @return time 
     */
    public function getTime()
    {
        return $this->time;
    }
    
    /**
     * Set comment
     *
     * @param integer $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return integer 
     */
    public function getComment()
    {
        return $this->comment;
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
     * Add reservations
     *
     * @param Reservation $reservations
     */
    public function addReservation(Reservation $reservations)
    {
        $this->reservations[] = $reservations;
    }

    /**
     * Get reservations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
    }

}