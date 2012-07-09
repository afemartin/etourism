<?php

namespace PFCD\TourismBundle\Repository;

use Doctrine\ORM\EntityRepository;

use PFCD\TourismBundle\Entity\Session;
use PFCD\TourismBundle\Entity\Reservation;

use \DateTime;
use \DatePeriod;
use \DateInterval;

class SessionRepository extends EntityRepository
{
    
    public function findAllFiltered($organization = null, $activity = null, $dateStart = null, $dateEnd = null, $time = null, $daysWeek = null, $status = null)
    {
        $qb = $this->createQueryBuilder('s');
        
        $qb->select('s');
        
        if ($organization !== null)
        {
            $qb->innerJoin('s.activity', 'a');
            $qb->where('a.organization = :organization');
            $qb->setParameter('organization', $organization);
        }
        else
        {
            $qb->where('1 = 1');
        }
        
        if ($activity !== null)
        {
            $qb->andWhere('s.activity = :activity');
            $qb->setParameter('activity', $activity);
        }
        
        if ($dateStart !== null)
        {
            $qb->andWhere('s.date >= :start_date');
            $qb->setParameter('start_date', $dateStart);
        }
        
        if ($dateEnd !== null)
        {
            $qb->andWhere('s.date <= :start_end');
            $qb->setParameter('start_end', $dateEnd);
        }
        
        if ($time !== null)
        {
            $qb->andWhere('s.time = :time');
            $qb->setParameter('time', $time);
        }
        
        if ($daysWeek !== null && !empty($daysWeek))
        {
            $qb->andWhere('s.dayOfWeek IN (:days_week)');
            $qb->setParameter('days_week', $daysWeek);
        }
        
        if ($status !== null && !empty($status))
        {
            $qb->andWhere('s.status IN (:status)');
            $qb->setParameter('status', $status);
        }

        $qb->orderBy('s.date', 'ASC');
        $qb->addOrderBy('s.time', 'ASC');

        // var_dump($qb->getDQL());
        // var_dump($qb->getQuery()->getSQL());

        return $qb->getQuery()->getResult();
    }
    
    const DATE_FORMAT = "Y-m-d";

    public function findAvailability($activity, $year, $month)
    {
        // get the first and last day of the month
        $dateBeg = new DateTime($year.'-'.$month.'-1');
        $dateEnd = new DateTime($dateBeg->format('Y-m-t'));
        
        // adjust the interval to full weeks from Monday to Sunday
        if ($dateBeg->format('N') != 1)
        {
            $dateBeg = new DateTime(date(self::DATE_FORMAT, strtotime('previous monday', $dateBeg->getTimestamp())));
        }
        if ($dateEnd->format('N') != 7)
        {
            $dateEnd = new DateTime(date(self::DATE_FORMAT, strtotime('next sunday', $dateEnd->getTimestamp())));
        }
        
        // get the sessions and reservations of the selected interval of dates
        
        $dateEnd->setTime(23, 59, 59);
        $interval = DateInterval::createFromDateString('1 day');
        $dates = new DatePeriod($dateBeg, $interval, $dateEnd);
        
        $calendar = array();
        
        foreach ($dates as $date)
        {
            $calendar[$date->format("Y-m-d")]['date'] = $date;
        }
        
        $status_session = array(Session::STATUS_ENABLED, Session::STATUS_LOCKED);
        $status_reservation = array(Reservation::STATUS_REQUESTED, Reservation::STATUS_ACCEPTED);
        
        $qb = $this->createQueryBuilder('s');
        $qb->select('s, SUM(r.persons)');
        $qb->where('s.activity = :activity');
        $qb->leftJoin('s.reservations', 'r', 'WITH', 'r.status IN (:status_r)');
        $qb->andWhere('s.date >= :date_start');
        $qb->andWhere('s.date <= :date_end');
        $qb->andWhere('s.status IN (:status_s)');
        $qb->groupBy('s.id');
        
        $qb->setParameter('activity', $activity);
        $qb->setParameter('status_r', $status_reservation);
        $qb->setParameter('date_start', $dateBeg);
        $qb->setParameter('date_end', $dateEnd);
        $qb->setParameter('status_s', $status_session);

        $result = $qb->getQuery()->getResult();
        
        foreach ($result as $row)
        {
            $session = $row[0];
            $id = $session->getId();
            $date = $session->getDate()->format("Y-m-d");
            $time = $session->getTime() !== null ? $session->getTime()->format("H:i") : 'undefined';
            $capacity = $session->getActivity()->getCapacity();
            $persons = $row[1] ? $row[1] : 0;
            
            $calendar[$date]['sessions'][$time]['id'] = $id;
            $calendar[$date]['sessions'][$time]['time'] = $time;
            $calendar[$date]['sessions'][$time]['capacity'] = $capacity - $persons;
            $calendar[$date]['sessions'][$time]['status'] = 1;
        }
        
        return $calendar;
    }
    
}

?>
