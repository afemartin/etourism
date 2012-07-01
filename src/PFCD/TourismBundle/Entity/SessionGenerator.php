<?php

namespace PFCD\TourismBundle\Entity;

use PFCD\TourismBundle\Entity\Activity;

use \DateTime;
use \DatePeriod;
use \DateInterval;

class SessionGenerator
{
    private $activity;
    
    private $dateStart;
    
    private $dateEnd;

    private $startTime1;
    
    private $startTime2;
    
    private $startTime3;
    
    private $startTime4;
    
    private $daysWeek;

    private $status;
    
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

    public function getDatesRange()
    {
        $datesRange = array();
        
        if ($this->daysWeek)
        {
            if (!$this->dateEnd)
            {
                $this->dateEnd = clone $this->dateStart;
                $this->dateEnd->add(new DateInterval('P1YT23H59M59S'));
            }

            $interval = DateInterval::createFromDateString('1 day');
            $days = new DatePeriod($this->dateStart, $interval, $this->dateEnd);

            foreach ($days as $day)
            {
                if (in_array($day->format("N"), $this->daysWeek))
                {
                    $datesRange[] = $day;
                }
            }
        }
        
        return $datesRange;
    }

    public function setStartTime1($startTime1)
    {
        $this->startTime1 = $startTime1;
    }

    public function getStartTime1()
    {
        return $this->startTime1;
    }

    public function setStartTime2($startTime2)
    {
        $this->startTime2 = $startTime2;
    }

    public function getStartTime2()
    {
        return $this->startTime2;
    }

    public function setStartTime3($startTime3)
    {
        $this->startTime3 = $startTime3;
    }

    public function getStartTime3()
    {
        return $this->startTime3;
    }

    public function setStartTime4($startTime4)
    {
        $this->startTime4 = $startTime4;
    }

    public function getStartTime4()
    {
        return $this->startTime4;
    }

    public function getStartTimes()
    {
        $startTimes = array();
        
        if ($this->startTime1) $startTimes[] = $this->startTime1;
        if ($this->startTime2) $startTimes[] = $this->startTime2;
        if ($this->startTime3) $startTimes[] = $this->startTime3;
        if ($this->startTime4) $startTimes[] = $this->startTime4;
        
        return $startTimes;
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