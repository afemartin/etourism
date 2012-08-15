<?php

namespace PFCD\TourismBundle\Entity;

class OrganizationFilter
{
    private $country;
    
    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

}