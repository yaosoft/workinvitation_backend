<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: "Invitation")]
#[ORM\Entity]
class Invitation
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    public $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "userInvitation")]
	#[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
	public $user;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "ProjectInvitation")]
	#[ORM\JoinColumn(name: "Project_id", referencedColumnName: "id", nullable: true)]
	public $project;

    #[ORM\Column(type: "string", length: 250, nullable: false)]
    public $receiverEmail;

    #[ORM\Column(type: "string", length: 250, nullable: false)]
    public $receiverName;

    #[ORM\Column(type: "integer", length: 2, nullable: false)]
    public $attempts;

    #[ORM\Column(type: "integer", length: 250, nullable: false)]
    public $status;

    #[ORM\Column(type: "datetime")]
    public $dateSending;


    #[ORM\Column(type: "datetime")]
    public $dateCreated;

    public function __construct()
    {
        // parent::__construct();
        // your own logic
		$this->dateCreated 			= new \DateTime();
		$this->projectChatMessage 	= new ArrayCollection();
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
     * Set project
     *
     * @param string $project
     * @return string
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return string 
     */
    public function getProject()
    {
        return $this->project;
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
     * Set receiverEmail
     *
     * @param string $receiverEmail
     * @return string
     */
    public function setReceiverEmail($receiverEmail)
    {
        $this->receiverEmail = $receiverEmail;

        return $this;
    }

    /**
     * Get receiverEmail
     *
     * @return string 
     */
    public function getReceiverEmail()
    {
        return $this->receiverEmail;
    }

	/**
     * Set receiverName
     *
     * @param string $receiverName
     * @return string
     */
    public function setReceiverName($receiverName)
    {
        $this->receiverName = $receiverName;

        return $this;
    }

    /**
     * Get receiverName
     *
     * @return string 
     */
    public function getReceiverName()
    {
        return $this->receiverName;
    }

    /**
     * Set attempts
     *
     * @param string $attempts
     * @return string
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * Get attempts
     *
     * @return string 
     */
    public function getAttempts()
    {
        return $this->attempts;
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
     * Set dateSending
     *
     * @param string $dateSending
     * @return string
     */
    public function setDateSending($dateSending)
    {
        $this->dateSending = $dateSending;

        return $this;
    }

    /**
     * Get dateSending
     *
     * @return string 
     */
    public function getDateSending()
    {
        return $this->dateSending;
    }
}