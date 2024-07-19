<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: "project")]
#[ORM\Entity]
class Project
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    public $id;

    #[ORM\Column(type: "string", length: 250, nullable: false)]
    public $title;

    #[ORM\Column(type: "datetime")]
    public $dateCreated;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "userProject")]
	#[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
	public $user;

    #[ORM\Column(type: "string", length: 20, nullable: false)]
    public $budget;

    #[ORM\Column(type: "string", length: 20, nullable: false)]
    public $duration;

	#[OneToMany(targetEntity: ChatMessage::class, mappedBy: "project")]
	public $projectChatMessage;

	#[OneToMany(targetEntity: Invitation::class, mappedBy: "project")]
	public $ProjectFile;

    #[ORM\ManyToOne(targetEntity: ProjectCategory::class, inversedBy: "ProjectCategory")]
	#[ORM\JoinColumn(name: "ProjectCategory_id", referencedColumnName: "id", nullable: true)]
	public $projectCategory;

    #[ORM\ManyToOne(targetEntity: ProjectType::class, inversedBy: "ProjectType")]
	#[ORM\JoinColumn(name: "ProjectCategory_id", referencedColumnName: "id", nullable: true)]
	public $projectType;

    #[ORM\Column(type: "text", length: 50000, nullable: false)]
    public $description;


    public function __construct()
    {
        // parent::__construct();
        // your own logic
		$this->dateCreated 			= new \DateTime();
		$this->projectChatMessage 	= new ArrayCollection();
		$this->ProjectFile 			= new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return string
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
	
	/**
     * Set status
     *
     * @param string $status
     * @return string
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
	
/**
     * Set description
     *
     * @param string $description
     * @return string
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set budget
     *
     * @param string $budget
     * @return string
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return string 
     */
    public function getBudget()
    {
        return $this->budget;
    }
	
    /**
     * Set duration
     *
     * @param string $duration
     * @return string
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return string 
     */
    public function getDuration()
    {
        return $this->duration;
    }
	
    /**
     * Set multi
     *
     * @param string $multi
     * @return string
     */
    public function setMulti($multi)
    {
        $this->multi = $multi;

        return $this;
    }

    /**
     * Get multi
     *
     * @return string 
     */
    public function getMulti()
    {
        return $this->multi;
    }
	
	/**
     * Set user
     *
     * @param App\Entity\User $user
     *
     * @return Project
     */
    public function setUser($user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return App\Entity\user
     */
    public function getUser()
    {
        return $this->user;
    }
	
	/**
     * Set manager
     *
     * @param App\Entity\Manager $manager
     *
     * @return Project
     */
    public function setManager($manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return App\Entity\manager
     */
    public function getManager()
    {
        return $this->manager;
    }
	
	/**
     * Add projectChatMessage
     *
     * @param App\Entity\ChatMessage $projectChatMessage
     *
     * @return projectChatMessage
     */
    public function projectChatMessage($projectChatMessage)
    {
        $this->projectChatMessage = $projectChatMessage;

        return $this;
    }

    /**
     * Remove projectChatMessage
     *
     * @param App\Entity\ChatMessage $projectChatMessage
     */
    public function removeProjectChatMessage($projectChatMessage)
    {
        $this->projectChatMessage->removeElement($projectChatMessage);
    }

    /**
     * Get projectChatMessage
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectChatMessage()
    {
        return $this->projectChatMessage;
    }

	/**
     * Add projectFile
     *
     * @param App\Entity\ChatMessage $projectFile
     *
     * @return projectFile
     */
    public function addProjectFile($projectFile)
    {
        $this->projectFile = $projectFile;

        return $this;
    }

    /**
     * Remove projectFile
     *
     * @param App\Entity\ChatMessage $projectFile
     */
    public function removeProjectFile($projectFile)
    {
        $this->projectFile->removeElement($projectFile);
    }

    /**
     * Get projectFile
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectFile()
    {
        return $this->projectFile;
    }


    /**
     * Set projectCategory
     *
     * @param string $projectCategory
     * @return string
     */
    public function setProjectCategory($projectCategory)
    {
        $this->projectCategory = $projectCategory;

        return $this;
    }

    /**
     * Get projectCategory
     *
     * @return string 
     */
    public function getProjectCategory()
    {
        return $this->projectCategory;
    }

    /**
     * Set projectType
     *
     * @param string $projectType
     * @return string
     */
    public function setProjectType($projectType)
    {
        $this->projectType = $projectType;

        return $this;
    }

    /**
     * Get projectType
     *
     * @return string 
     */
    public function getProjectType()
    {
        return $this->projectType;
    }

    /**
     * Set projectFileProject
     *
     * @param string $projectFileProject
     * @return string
     */
    public function setPath($projectFileProject)
    {
        $this->projectFileProject = $projectFileProject;

        return $this;
    }

    /**
     * Get projectFileProject
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->projectFileProject;
    }
}