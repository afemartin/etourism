<?php

namespace PFCD\TourismBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 * @ORM\HasLifecycleCallbacks()
 */
class Settings
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="home_desc", type="text")
     */
    private $homeDesc;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="about_desc", type="text")
     */
    private $aboutDesc;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="user_legal", type="text")
     */
    private $userLegal;
    
    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;
        
    public function __construct()
    {
        $this->id = 1;
        $this->enabled = false;
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
     * Set homeDesc
     *
     * @param text $homeDesc
     */
    public function setHomeDesc($homeDesc)
    {
        $this->homeDesc = $homeDesc;
    }

    /**
     * Get homeDesc
     *
     * @return text 
     */
    public function getHomeDesc()
    {
        return $this->homeDesc;
    }

    /**
     * Set aboutDesc
     *
     * @param text $aboutDesc
     */
    public function setAboutDesc($aboutDesc)
    {
        $this->aboutDesc = $aboutDesc;
    }

    /**
     * Get aboutDesc
     *
     * @return text 
     */
    public function getAboutDesc()
    {
        return $this->aboutDesc;
    }

    /**
     * Set userLegal
     *
     * @param text $userLegal
     */
    public function setUserLegal($userLegal)
    {
        $this->userLegal = $userLegal;
    }

    /**
     * Get userLegal
     *
     * @return text 
     */
    public function getUserLegal()
    {
        return $this->userLegal;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
    
}