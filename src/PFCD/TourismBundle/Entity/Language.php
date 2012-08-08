<?php

namespace PFCD\TourismBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="language")
 * @ORM\HasLifecycleCallbacks()
 */
class Language
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $code ISO 639-1
     * @link http://en.wikipedia.org/wiki/ISO_639-1
     * 
     * @ORM\Column(name="code", type="string", length=2)
     */
    private $code;

    /**
     * @ORM\Column(name="name", type="string", length=32)
     */
    private $name;

    /**
     * @ORM\Column(name="flag", type="string", length=16)
     */
    private $flag;

    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;
    
    public function __construct()
    {
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
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set flag
     *
     * @param string $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    /**
     * Get flag
     *
     * @return string 
     */
    public function getFlag()
    {
        return $this->flag;
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