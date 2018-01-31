<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Advert_Category", type="string", length=255)
     */
    private $advertCategory;
    
    /**
     * @ORM\ManyToMany(targetEntity="Advert", mappedBy="categories")
     */
    private $adverts;
    
    public function __construct() {
        $this->adverts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set advertCategory
     *
     * @param string $advertCategory
     *
     * @return Category
     */
    public function setAdvertCategory($advertCategory)
    {
        $this->advertCategory = $advertCategory;

        return $this;
    }

    /**
     * Get advertCategory
     *
     * @return string
     */
    public function getAdvertCategory()
    {
        return $this->advertCategory;
    }

    /**
     * Add advert
     *
     * @param \AppBundle\Entity\Advert $advert
     *
     * @return Category
     */
    public function addAdvert(\AppBundle\Entity\Advert $advert)
    {
        $this->adverts[] = $advert;

        return $this;
    }

    /**
     * Remove advert
     *
     * @param \AppBundle\Entity\Advert $advert
     */
    public function removeAdvert(\AppBundle\Entity\Advert $advert)
    {
        $this->adverts->removeElement($advert);
    }

    /**
     * Get adverts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdverts()
    {
        return $this->adverts;
    }
}
