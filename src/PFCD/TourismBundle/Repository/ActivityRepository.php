<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

use \DateTime;
use \DatePeriod;
use \DateInterval;

class ActivityRepository extends EntityRepository
{

    const CALENDAR_DAYS_INTERVAL = 13;
    const CALENDAR_DAYS_SPAN = 14;
    const DATE_FORMAT = "Y-m-d";

    public function findAvailability($dateStart, $capacity, $daysweek)
    {
        // if not initial date or start date already was before set today as start date
        if (!$dateStart || $dateStart < new DateTime())
        {
            $dateStart = new DateTime();
        }

        // adjust the interval to full weeks from Monday to Sunday
        $dateStart = new DateTime(date(self::DATE_FORMAT, strtotime('Monday this week', $dateStart->getTimestamp())));
        $dateEnd = clone $dateStart;
        $dateEnd->add(new DateInterval('P' . self::CALENDAR_DAYS_INTERVAL . 'DT23H59M59S'));

        $interval = DateInterval::createFromDateString('1 day');
        $days = new DatePeriod($dateStart, $interval, $dateEnd);
        
        $calendar = array();
        $calendar['years'][$dateStart->format("Y")] = 0;
        $calendar['months'][$dateStart->format("F")] = 0;
        $calendar['years'][$dateEnd->format("Y")] = 0;
        $calendar['months'][$dateEnd->format("F")] = 0;
        
        foreach ($days as $day)
        {
            $year = $day->format("Y");
            $calendar['years'][$year]++;
            
            $month = $day->format("F");
            $calendar['months'][$month]++; 
            
            $date = $day->format("Y-m-d");
            $calendar['dates'][$date]['dayweek'] = $day->format("D");
            $calendar['dates'][$date]['daymonth'] = $day->format("d");
            $calendar['dates'][$date]['capacity'] = $daysweek[$day->format("N")] ? $capacity : 0;
        }

        return $calendar;
    }

}

?>
