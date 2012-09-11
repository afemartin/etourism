<?php

namespace PFCD\TourismBundle\Entity;

use PFCD\TourismBundle\Constants;

use PFCD\TourismBundle\Entity\Session;

use \DateTime;
use \DateInterval;

class SessionFilter
{
    private $activity;
    
    private $dateStart;
    
    private $dateEnd;

    private $startTime;
    
    private $daysWeek;

    private $status;
    
    public function __construct()
    {
        $this->dateStart = new DateTime();
        $this->dateStart->setTime(0, 0, 0);
        
        $this->dateEnd = new DateTime();
        $this->dateEnd->add(new DateInterval('P1M'));
        $this->dateEnd->setTime(23, 59, 59);
        
        $this->daysWeek = array(Constants::MONDAY, Constants::TUESDAY, Constants::WEDNESDAY, Constants::THURSDAY, Constants::FRIDAY, Constants::SATURDAY, Constants::SUNDAY);
        
        $this->status = array(Session::STATUS_ENABLED, Session::STATUS_LOCKED);
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

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function setDaysWeek($daysWeek)
    {
        $this->daysWeek = $daysWeek;
    }

    public function getDaysWeek()
    {
        return $this->daysWeek;
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