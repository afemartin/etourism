<?php

namespace PFCD\TourismBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="country")
 * @ORM\HasLifecycleCallbacks()
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=32)
     */
    private $name;

    /**
     * @ORM\Column(name="locale", type="string", length=7)
     */
    private $locale;

    /**
     * @var integer $continent 1=>Africa, 2=>America, 3=>Asia, 4=>Europe, 5=>Oceania
     *
     * @ORM\Column(name="continent", type="smallint")
     */
    private $continent;

    /**
     * @ORM\Column(name="short_desc", type="string", length=512)
     */
    private $shortDesc;

    /**
     * @ORM\Column(name="full_desc", type="text", nullable=true)
     */
    private $fullDesc;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var integer $status 1=>List, 2=>Specific section
     * 
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;
    
    /**
     * @ORM\OneToMany(targetEntity="Organization", mappedBy="country")
     */
    private $organizations;

    public function __construct()
    {
        $this->status = 1;
        $this->organizations = new ArrayCollection();
        
        $this->setUpdated(new \DateTime());
    }

    /**
     * @ORM\preUpdate
     */
    public function setUpdatedValue()
    {
       $this->setUpdated(new \DateTime());
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('locale', new NotBlank());
        $metadata->addPropertyConstraint('continent', new NotBlank());
        $metadata->addPropertyConstraint('shortDesc', new NotBlank());
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
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set continent
     *
     * @param integer $continent
     */
    public function setContinent($continent)
    {
        $this->continent = $continent;
    }

    /**
     * Get continent
     *
     * @return integer 
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * Set shortDesc
     *
     * @param string $shortDesc
     */
    public function setShortDesc($shortDesc)
    {
        $this->shortDesc = $shortDesc;
    }

    /**
     * Get shortDesc
     *
     * @return string 
     */
    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    /**
     * Set fullDesc
     *
     * @param text $fullDesc
     */
    public function setFullDesc($fullDesc)
    {
        $this->fullDesc = $fullDesc;
    }

    /**
     * Get fullDesc
     *
     * @return text 
     */
    public function getFullDesc()
    {
        return $this->fullDesc;
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
     * Add organizations
     *
     * @param PFCD\TourismBundle\Entity\Organization $organizations
     */
    public function addOrganization(\PFCD\TourismBundle\Entity\Organization $organizations)
    {
        $this->organizations[] = $organizations;
    }

    /**
     * Get organizations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }
}