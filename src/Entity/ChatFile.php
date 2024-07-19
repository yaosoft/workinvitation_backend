<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: "chat_file")]
#[ORM\Entity]
class ChatFile
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    public $id;

    /** Many chat file have One message type. */
    #[ORM\ManyToOne(targetEntity: ChatItemCategory::class, inversedBy: "chatFileCategory")]
	#[ORM\JoinColumn(name: "chatFileCategory_id", referencedColumnName: "id", nullable: true)]
	public $chatFileCategory;

    #[ORM\Column(type: "text", nullable: true)]
    public $chatFile;

    /** Many chat messages have many chat response ( many to many self-reference ). */
	#[ORM\ManyToMany(targetEntity: ChatFile::class)]
	#[JoinTable(name: 'chat_file_chat_file')]
    #[JoinColumn(name: 'chat_file_source', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'chat_file_target', referencedColumnName: 'id')]
	public $chatFileResponse;

    /** Many chat files have many chat message response ( many to many uni-directional )  */
	#[JoinTable(name: 'chat_file_chat_message')]
    #[JoinColumn(name: 'chat_file_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'chat_message_id', referencedColumnName: 'id')]
	#[ORM\ManyToMany(targetEntity: ChatMessage::class)]
	public $chatMessageResponse;

    #[ORM\Column(type: "boolean", nullable: false)]
    public $viewed;

    #[ORM\Column(type: "boolean", nullable: true)]
    public $alerted = false;

    #[ORM\Column(type: "text", nullable: false)]
    public $name;

    #[ORM\Column(type: "text", nullable: false)]
    public $size;

    #[ORM\Column(type: "text", nullable: false)]
    public $extension;

    #[ORM\Column(type: "datetime")]
    public $dateCreated;

    /** Many Files has One User. */
	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: "userChatFile")]
	#[ORM\JoinColumn(name:"user_id", referencedColumnName:"id", nullable: false)]
	public $user;

    /** Many Files has One Receiver. */
	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: "userChatFile")]
	#[ORM\JoinColumn(name:"receiver_id", referencedColumnName:"id", nullable: false)]
	public $receiver;

    /** Many Files has One Project. */
	#[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "projectFile")]
	#[ORM\JoinColumn(name: "project_id", referencedColumnName: "id", nullable: false)]
	public $project;

    public function __construct()
    {
        // parent::__construct();
        // your own logic
		$this->dateCreated 			= new \DateTime();
		$this->chatFileResponse		= new ArrayCollection();
		$this->chatMessageResponse	= new ArrayCollection();
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
     * Set user
     *
     * @param App\Entity\User $user
     *
     * @return Ads
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
     * Set alerted
     *
     * @param string $alerted
     * @return string
     */
    public function setAlerted($alerted)
    {
        $this->alerted = $alerted;

        return $this;
    }

    /**
     * Get alerted
     *
     * @return string 
     */
    public function getAlerted()
    {
        return $this->alerted;
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
     * Set chatFile
     *
     * @param string $chatFile
     * @return string
     */
    public function setPath($chatFile)
    {
        $this->chatFile = $chatFile;

        return $this;
    }

    /**
     * Get chatFile
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->chatFile;
    }
	

	/**
     * Set chatFile
     *
     * @param string $chatFile
     * @return string
     */
    public function setChatFile($chatFile)
    {
        $this->chatFile = $chatFile;

        return $this;
    }

    /**
     * Get chatFile
     *
     * @return string 
     */
    public function getChatFile()
    {
        return $this->chatFile;
    }

	/**
     * Add chatFileResponse
     *
     * @param App\Entity\ChatFile $chatFileResponse
     *
     * @return chatFileResponse
     */
    public function addChatFileResponse($chatFileResponse)
    {
        $this->chatFileResponse = $chatFileResponse;

        return $this;
    }

    /**
     * Remove chatFileResponse
     *
     * @param App\Entity\ChatFile $chatFileResponse
     */
    public function removeChatFileResponse($chatFileResponse)
    {
        $this->chatFileResponse->removeElement($chatFileResponse);
    }

    /**
     * Get chatFileResponse
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChatFileResponse()
    {
        return $this->chatFileResponse;
    }
	

	/**
     * Add chatMessageResponse
     *
     * @param App\Entity\ChatMessage $chatMessageResponse
     *
     * @return chatMessageResponse
     */
    public function addChatMessageResponse($chatMessageResponse)
    {
        $this->chatMessageResponse = $chatMessageResponse;

        return $this;
    }

    /**
     * Remove chatMessageResponse
     *
     * @param App\Entity\ChatMessage $chatMessageResponse
     */
    public function removeChatMessageResponse($chatMessageResponse)
    {
        $this->chatMessageResponse->removeElement($chatMessageResponse);
    }

    /**
     * Get chatMessageResponse
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChatMessageResponse()
    {
        return $this->chatMessageResponse;
    }

	/**
     * Set chatFileCategory
     *
     * @param string $chatFileCategory
     * @return string
     */
    public function setChatFileCategory($chatFileCategory)
    {
        $this->chatFileCategory = $chatFileCategory;

        return $this;
    }

    /**
     * Get chatFileCategory
     *
     * @return string 
     */
    public function getChatFileCategory()
    {
        return $this->chatFileCategory;
    }
}