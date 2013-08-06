<?php

namespace ad\ExtraBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ad\ExtraBundle\Validator\Constraints as adAssert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * File
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ad\ExtraBundle\Entity\Repository\FileRepository")
 */
class File
{	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var Date()
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @Assert\File(maxSize="10M")
     * @adAssert\FileTypeNonAllowed()
     */
    public $file;
    
    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="ad\ExtraBundle\Entity\Category", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $categoryId;
    
    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="ad\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $userId;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getWebPath()
    {
    	return null === $this->name ? null : $this->getUploadDir().'/'.$this->name;
    }
    
    protected function getUploadRootDir()
    {
    	// le chemin absolu du répertoire dans lequel sauvegarder les photos de profil
    	return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
    
    protected function getUploadDir()
    {
    	// get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
    	return 'uploads/files';
    }
     
    public function uploadFile()
    {
    	// move copie le fichier présent chez le client dans le répertoire indiqué.
    	$this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
    	$this->name = $this->file->getClientOriginalName();
    	
    	// La propriété file ne servira plus
    	$this->file = null;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
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
     * Set size
     *
     * @param integer $size
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;
    
        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return File
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set categoryId
     *
     * @param \ad\ExtraBundle\Entity\Category $categoryId
     * @return File
     */
    public function setCategoryId(\ad\ExtraBundle\Entity\Category $categoryId)
    {
        $this->categoryId = $categoryId;
    
        return $this;
    }

    /**
     * Get categoryId
     *
     * @return \ad\ExtraBundle\Entity\Category 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set userId
     *
     * @param \ad\UserBundle\Entity\User $userId
     * @return File
     */
    public function setUserId(\ad\UserBundle\Entity\User $userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return \ad\UserBundle\Entity\User 
     */
    public function getUserId()
    {
        return $this->userId;
    }
}