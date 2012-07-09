<?php

namespace PFCD\TourismBundle\Entity;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Reservation;

use \DateTime;
use \DateInterval;

class ReservationFilter
{
    private $activity;
    
    private $dateStart;
    
    private $dateEnd;

    private $status;
    
    public function __construct()
    {
        $this->status = array(Reservation::STATUS_REQUESTED, Reservation::STATUS_ACCEPTED, Reservation::STATUS_REJECTED, Reservation::STATUS_CANCELED);
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
    }

    public function getDateStart()
    {
        return $this->dateStart;
    }

    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
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