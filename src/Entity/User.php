<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: "users")]
#[ORM\Entity]
class User extends BaseUser
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    protected $id;

	#[OneToMany(targetEntity: Project::class, mappedBy: "user")]
	private $userProject;

	#[OneToMany(targetEntity: Invitation::class, mappedBy: "user")]
	private $userInvitation;

	#[OneToMany(targetEntity: Project::class, mappedBy: "user", nullable: true)]
	private $userManager;

	#[OneToMany(targetEntity: ChatMessage::class, mappedBy: "user")]
	private $userChatMessage;

	#[OneToMany(targetEntity: ChatFile::class, mappedBy: "user")]
	private $userChatFile;

    #[ORM\Column(type: "boolean", nullable: false)]
    public $isadmin = false;

    public function __construct()
    {
        parent::__construct();
        
		$this->userProject 		= new ArrayCollection();
		$this->userInvitation 	= new ArrayCollection();
		$this->userManager 		= new ArrayCollection();
		$this->userChatMessage 	= new ArrayCollection();
		$this->userChatFile 	= new ArrayCollection();
    }
	
	/**
     * Add userProject
     *
     * @param App\Entity\Project $userProject
     *
     * @return userProject
     */
    public function userProject($userProject)
    {
        $this->userProject = $userProject;

        return $this;
    }

    /**
     * Remove userProject
     *
     * @param App\Entity\Project $userProject
     */
    public function removeUserProject($userProject)
    {
        $this->userProject->removeElement($userProject);
    }

    /**
     * Get userProject
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserProject()
    {
        return $this->userProject;
    }

	/**
     * Add userInvitation
     *
     * @param App\Entity\Invitation $userInvitation
     *
     * @return userInvitation
     */
    public function userInvitation($userInvitation)
    {
        $this->userInvitation = $userInvitation;

        return $this;
    }

    /**
     * Remove userInvitation
     *
     * @param App\Entity\Invitation $userInvitation
     */
    public function removeUserInvitation($userInvitation)
    {
        $this->userInvitation->removeElement($userInvitation);
    }

    /**
     * Get userInvitation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserInvitation()
    {
        return $this->userInvitation;
    }

	/**
     * Add userManager
     *
     * @param App\Entity\Project $userManager
     *
     * @return userManager
     */
    public function userManager($userManager)
    {
        $this->userManager = $userManager;

        return $this;
    }

    /**
     * Remove userManager
     *
     * @param App\Entity\Project $userManager
     */
    public function removeUserManager($userManager)
    {
        $this->userManager->removeElement($userManager);
    }

    /**
     * Get userManager
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

	/**
     * Add userChatMessage
     *
     * @param App\Entity\ChatMessage $userChatMessage
     *
     * @return userChatMessage
     */
    public function userChatMessage($userChatMessage)
    {
        $this->userChatMessage = $userChatMessage;

        return $this;
    }

    /**
     * Remove userChatMessage
     *
     * @param App\Entity\ChatMessage $userChatMessage
     */
    public function removeUserChatMessage($userChatMessage)
    {
        $this->userChatMessage->removeElement($userChatMessage);
    }

    /**
     * Get userChatMessage
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserChatMessage()
    {
        return $this->userChatMessage;
    }
	
	/**
     * Add userChatFile
     *
     * @param App\Entity\ChatFile $userChatFile
     *
     * @return userChatFile
     */
    public function userChatFile($userChatFile)
    {
        $this->userChatFile = $userChatFile;

        return $this;
    }

    /**
     * Remove userChatFile
     *
     * @param App\Entity\ChatFile $userChatFile
     */
    public function removeUserChatFile($userChatFile)
    {
        $this->userChatFile->removeElement($userChatFile);
    }

    /**
     * Get userChatFile
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserChatFile()
    {
        return $this->userChatFile;
    }
	
	/**
     * Set isadmin
     *
     * @param string $isadmin
     * @return string
     */
    public function setIsadmin($isadmin)
    {
        $this->isadmin = $isadmin;

        return $this;
    }

    /**
     * Get isadmin
     *
     * @return string 
     */
    public function getIsadmin()
    {
        return $this->isadmin;
    }
}