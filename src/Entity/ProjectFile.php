<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: "project_file")]
#[ORM\Entity]
class ProjectFile
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    public $id;

    #[ORM\Column(type: "text", nullable: false)]
    public $name;

    #[ORM\Column(type: "text", nullable: false)]
    public $path;

    #[ORM\Column(type: "text", nullable: false)]
    public $size;

    #[ORM\Column(type: "text", nullable: false)]
    public $extension;

    #[ORM\Column(type: "datetime")]
    public $dateCreated;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "projectFile")]
	#[ORM\JoinColumn(name: "Project_id", referencedColumnName: "id", nullable: true)]
	private $project;

    public function __construct()
    {
		$this->dateCreated 		= new \DateTime();
    }

	/**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * Set receiver
     *
     * @param App\Entity\User $receiver
     *
     * @return File
     */
    public function setReceiver($receiver = null)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return App\Entity\User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

	/**
     * Set project
     *
     * @param App\Entity\Project $project
     *
     * @return Ads
     */
    public function setProject($project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return App\Entity\project
     */
    public function getProject()
    {
        return $this->project;
    }
	
    /**
     * Set dateCreated
     *
     * @param string $dateCreated
     * @return string
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return string 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
	
	/**
     * Set viewed
     *
     * @param string $viewed
     * @return string
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;

        return $this;
    }

    /**
     * Get viewed
     *
     * @return string 
     */
    public function getViewed()
    {
        return $this->viewed;
    }

	/**
     * Set extension
     *
     * @param string $extension
     * @return string
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

	/**
     * Set size
     *
     * @param string $size
     * @return string
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string 
     */
    public function getSize()
    {
        return $this->size;
    }

	/**
     * Set name
     *
     * @param string $name
     * @return string
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
     * Set path
     *
     * @param string $path
     * @return string
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

}