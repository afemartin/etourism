<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use PFCD\TourismBundle\Entity\Reservation;

/**
 * @ORM\Entity(repositoryClass="PFCD\TourismBundle\Repository\PaymentRepository")
 * @ORM\Table(name="payment")
 * @ORM\HasLifecycleCallbacks()
 */
class Payment
{
    const TYPE_CREDIT_CARD   = 1;
    const TYPE_PAYPAL        = 2;
    const TYPE_BANK_TRANSFER = 3;
    const TYPE_CASH          = 4;
    
    const STATUS_PENDING_P = 0;     // The accepted reservation generate automatically the associated payment (FROM 0 TO [1])
    const STATUS_PAID      = 1;     // The organization receive the payment from the user (FROM 1 TO [2])
    const STATUS_PENDING_R = 2;     // The user cancel the reservation after paid it so the organization will have to refund the money (FROM 2 TO [3] or END)
    const STATUS_REFUNDED  = 3;     // The organization has refunded the money to the user (END)

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Reservation", inversedBy="payment")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id")
     */
    private $reservation;

    /**
     * @var integer $type 1=>CreditCard, 2=>Bank transfer, 3=>Paypal, 4=>Cash
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    private $type;

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
     * @var integer $status 0=>Pending payment, 1=>Paid, 2=>Pending refund, 3=>Refunded
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING_P;
        
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
     * Set reservation
     *
     * @param Reservation $reservation
     */
    public function setReservation(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get reservation
     *
     * @return Reservation 
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Get type in human readable mode
     *
     * @return string
     */
    public function getTypeText()
    {
        return $this->type ? 'entity.payment.field.type.' . $this->type : null;
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
        return 'entity.payment.field.status.' . $this->status;
    }
    
}