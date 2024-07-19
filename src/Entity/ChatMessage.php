<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: "chat_message")]
#[ORM\Entity]
class ChatMessage
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    public $id;

    /** Many chat message can have One message type. */
    #[ORM\ManyToOne(targetEntity: ChatItemCategory::class, inversedBy: "messageCategory")]
	#[ORM\JoinColumn(name: "chatMessageCategory_id", referencedColumnName: "id", nullable: false)]
	public $chatMessageCategory;

    /** Many chat messages have many chat response ( many to many self-reference ). */
	#[ORM\Column(type: "text", length: 60000, nullable: false)]
	public $chatMessage;

    /** Many chat messages have many chat response ( many to many self-reference ). */
	#[JoinTable(name: 'chat_message_chat_message')]
    #[JoinColumn(name: 'chat_message_source', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'chat_message_target', referencedColumnName: 'id')]
	#[ORM\ManyToMany(targetEntity: ChatMessage::class)]
	public $chatMessageResponse;

    /** Many chat messages have many chat file response ( many to many uni-directional )  */
	#[JoinTable(name: 'chat_message_chat_file')]
    #[JoinColumn(name: 'chat_message_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'chat_file_id', referencedColumnName: 'id')]
	#[ORM\ManyToMany(targetEntity: ChatFile::class)]
	public $chatFileResponse;

    #[ORM\Column(type: "boolean", nullable: false)]
    public $viewed;

    #[ORM\Column(type: "boolean", nullable: true)]
    public $alerted = false;

    #[ORM\Column(type: "datetime")]
    public $dateCreated;

    /** Many Messages has One User. */
	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: "userChatMessage")]
	#[ORM\JoinColumn(name:"user_id", referencedColumnName:"id", nullable: false)]
	public $user;

    /** Many Messages has One User ( receiver ). */
	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: "userChatMessage")]
	#[ORM\JoinColumn(name:"receiver_id", referencedColumnName:"id", nullable: false)]
	public $receiver;

    /** Many Messages has One Project. */
	#[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "projectChatMessage")]
	#[ORM\JoinColumn(name: "project_id", referencedColumnName: "id", nullable: false)]
	public $project;
	
    public function __construct()
    {
        // parent::__construct();
        // your own logic
		$this->dateCreated 			= new \DateTime();
		$this->chatMessageResponse 	= new ArrayCollection();
		$this->chatFileResponse		= new ArrayCollection();
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
     * @return Message
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
     * Set chatMessage
     *
     * @param string $chatMessage
     * @return string
     */
    public function setChatMessage($chatMessage)
    {
        $this->chatMessage = $chatMessage;

        return $this;
    }

    /**
     * Get chatMessage
     *
     * @return string 
     */
    public function getChatMessage()
    {
        return $this->chatMessage;
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
     * Set chatMessageCategory
     *
     * @param string $chatMessageCategory
     * @return string
     */
    public function setChatMessageCategory($chatMessageCategory)
    {
        $this->chatMessageCategory = $chatMessageCategory;

        return $this;
    }

    /**
     * Get chatMessageCategory
     *
     * @return string 
     */
    public function getChatMessageCategory()
    {
        return $this->chatMessageCategory;
    }
	
}