<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use PFCD\TourismBundle\Entity\User;
use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Entity\Payment;

/**
 * @ORM\Entity(repositoryClass="PFCD\TourismBundle\Repository\ReservationRepository")
 * @ORM\Table(name="reservation")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation
{
    const STATUS_REQUESTED = 1; // user make a reservation (FROM 1 TO [2,3,4])
    const STATUS_ACCEPTED  = 2; // organization accept the reservation (FROM 2 TO [4])
    const STATUS_REJECTED  = 3; // organization reject the reservation (END)
    const STATUS_CANCELED  = 4; // user cancel the reservation before or after to be accepted (END)

    private $statusText = array('1' => 'Requested', '2' => 'Accepted', '3' => 'Rejected', '4' => 'Canceled');
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reservations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="reservations")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
    private $session;

    /**
     * @ORM\Column(name="persons", type="smallint")
     */
    private $persons;
    
    /**
     * @ORM\Column(name="comment", type="string")
     */
    private $comment;

    /**
     * @var datetime $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var integer $status 1=>Requested, 2=>Acepted, 3=>Rejected, 4=>Canceled
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="Payment", inversedBy="reservation")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

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
     * Set session
     *
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Get session
     *
     * @return Session 
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set persons
     *
     * @param integer $persons
     */
    public function setPersons($persons)
    {
        $this->persons = $persons;
    }

    /**
     * Get persons
     *
     * @return integer 
     */
    public function getPersons()
    {
        return $this->persons;
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
     * Set payment
     *
     * @param Payment $payment
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get payment
     *
     * @return Payment 
     */
    public function getPayment()
    {
        return $this->payment;
    }

}