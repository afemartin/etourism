<?php

namespace PFCD\TourismBundle\Entity;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Payment;

use \DateTime;
use \DateInterval;

class PaymentFilter
{
    private $activity;
    
    private $dateStart;
    
    private $dateEnd;

    private $status;
    
    public function __construct()
    {
        $this->status = array(Payment::STATUS_PENDING_P, Payment::STATUS_PAID, Payment::STATUS_PENDING_R, Payment::STATUS_REFUNDED);
    }
    
    public function getActivity()
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        
        if ($dateStart) $this->dateStart->setTime(0, 0, 0);
    }

    public function getDateStart()
    {
        return $this->dateStart;
    }

    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
        if ($dateEnd) $this->dateEnd->setTime(23, 59, 59);
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

}