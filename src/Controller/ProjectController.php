<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectCategory;
use App\Entity\ProjectType;
use App\Entity\ProjectFile;
use App\Entity\ChatMessage;
use App\Entity\ChatFile;
use App\Entity\ChatItemCategory;
use App\Entity\User;
use App\Entity\Invitation;
use App\Form\ProjectTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\Uploader\FileUploader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/project')]
class ProjectController extends AbstractController
{
    public function index(EntityManagerInterface $entityManager): Response
    {
		$user 		= $this->getUser();
		$isadmin  	= $user->getIsadmin();
		$projects 	= '';
		if( !$isadmin ){	// an admin can see all projects
			$projects = $entityManager
            ->getRepository(Project::class)
            ->findByUser( $user );
		}
		else{
			$projects = $entityManager
            ->getRepository(Project::class)
			->findAll();	
		}
		
        return $this->render('Project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectTypeForm::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			
			// upload the File
			$projectFile = $form->get('path')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($projectFile) {
                $originalFilename = pathinfo($projectFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$projectFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $projectFile->move(
                        'uploads/files/projects',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $project->setPath($newFilename);
            }

			$user = $this->getUser();
			$project->setUser( $user );
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    public function show(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
		$user 			= $this->getUser();		// loged user
		$isadmin		= $user->getIsadmin();	// system admin
		$defaultAdminId = 8; // Todo: put the value in config file
		$defaultAdmin   = $entityManager->getRepository( User::class )	// user sent messages
								    ->findOneById( $defaultAdminId );
        $projectUser 	= $project->getUser();												// client
		$projectManager	= $project->getManager() ? $project->getManager() : $defaultAdmin;	// Project's manager
		$projectFile	= $project->getPath() ? $project->getPath() : '';
		$projectFileURL = $projectFile ? $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/files/projects/' . $projectFile : '';  // Todo: 

		if( $user == $projectUser || $isadmin ){
			return $this->render('Project/show.html.twig', [
				'project' 			=> $project,
				'projectManager' 	=> $projectManager,
				'messageReceiver' 	=> $user == $projectManager ? $projectUser : $projectManager,
				'projectFileURL'	=> $projectFileURL,
			]);
		}
		else{
			return new Response('This project is not yours');
		}
    }

    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			$user = $this->getUser();
			$project->setUser( $user );
			$entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }

    public function deleteChatMessage( ChatMessage $message, EntityManagerInterface $entityManager ): Response
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return new Response( 1, Response::HTTP_OK );
    }

    public function deleteChatFile( ChatFile $message, EntityManagerInterface $entityManager ): Response
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return new Response( 1, Response::HTTP_OK );
    }

	// Get all user messages and files for a project
	public function getMessages( Request $request, Project $project, EntityManagerInterface $entityManager ): Response
    {
		$user 	    = $this->getUser();
		$projectId	= $project->getId();
        $messages01 = $entityManager->getRepository( ChatMessage::class )	// user sent messages
								    ->findBy( Array( 'user' => $user, 'project' => $project ) );					
        $messages02 = $entityManager->getRepository( ChatMessage::class )	// user received messages
								    ->findBy( Array( 'receiver' => $user, 'project' => $project ) );
        $messages03 = $entityManager->getRepository( ChatFile::class )		// user sent files
								    ->findBy( Array( 'user' => $user, 'project' => $project ) );
        $messages04 = $entityManager->getRepository( ChatFile::class )		// user received files
								    ->findBy( Array( 'receiver' => $user, 'project' => $project ) );
		// Create message array
		$messages   = array_merge( $messages01, $messages02, $messages03, $messages04 );
		$name 	= $user->getUserName();
		$userid = $user->getId();
		$my 	= Array();
		$cancelable = 1200; // 1 hour
		if( !count( $messages ) ){
			return new Response( 0, Response::HTTP_OK );		// Todo: return a JSon
		}
		$todayTimestamp = strtotime( date("Y-m-d H:i:s")  );
		$todayDay 	= \date( 'Y-m-d', $todayTimestamp);
		// $countUnread = 0;
		foreach( $messages as $k => $v ){	
			$isReceiver = ( $userid == $v->getReceiver()->getId() ) ? true : false;
			$side 		= $isReceiver ? 'right' : 'left';
			$name 		= $v->getUser()->getUserName();
			
			if( !method_exists( $v, 'getPath' ) ){			// Messages
				$text				= $v->getChatMessage();
				$dateCreated 		= $v->getDateCreated()->format( 'Y-m-d H:i:s' );

				// message info
				$timestamp 			= strtotime( $dateCreated );
				$day 				= \date( 'Y-m-d', $timestamp);	// message day
				$hour				= \date( 'H:i', $timestamp);
				$displayDate 		= ( $day == $todayDay ) ? $hour : $day . ', ' . $hour;
				$canbedeleted		= $todayTimestamp - $timestamp < $cancelable ? 1 : 0;
				$category	= $v->getChatMessageCategory()->getTitle();
				$isAReply	= in_array( $category, [ 'B', 'C' ], );
				$repliedMessage		= Array();
				if( $isAReply ){  // replied message info
					if( $category == 'B' ){
						$repliedArr		= $v->getChatMessageResponse();
						$replied 		= '';
						foreach( $repliedArr as $a => $b ){
							$replied 	= $b;
						}

						if( is_object( $replied ) ){
							$replied_message 	= $replied->getChatMessage();
							$replied_name 		= $replied->getUser()->getUserName();
							$repliedMessage[ 'name' ] 				= $replied_name;
							$repliedMessage[ 'replied_message' ] 	= $replied_message;
							$repliedMessage[ 'category' ] 			= $category;
						}
					}
					else if ( $category == 'C' ){
						$repliedArr		= $v->getChatFileResponse();
						$replied 		= '';
						foreach( $repliedArr as $a => $b ){
							$replied 	= $b;
						}

						if( is_object( $replied ) ){
							// response message info
							$replied_name 		= $replied->getUser()->getUserName();
							$repliedMessage[ 'name' ] 				= $replied_name;
							$repliedMessage[ 'replied_fileSrc' ] 	= $replied->getPath();
							$repliedMessage[ 'replied_fileExt' ] 	= $replied->getExtension();
							$repliedMessage[ 'replied_fileName' ] 	= $replied->getName();
							$repliedMessage[ 'replied_fileSize' ] 	= $replied->getSize() / 1024 / 1024;
							$repliedMessage[ 'category' ] 			= $category;
						}
					}
				}
				array_push( $my, Array( 
					'dateCreated' 			=> $dateCreated,
					'canbedeleted'			=> $v->getUser() == $user ? $canbedeleted : 0,
					'message' 				=> $text, 
					'side'					=> $side, 
					'timestamp'				=> $timestamp, 
					'day'					=> $day, 
					'hour'					=> $hour, 
					'displayDate'			=> $displayDate,
					'name'					=> $name,
					'type'					=> 'text',

					'messageId'				=> $v->getId(),
					'messageUserName'	 	=> $v->getUser()->getUserName(),
					'messageUserId'			=> $v->getUser()->getId(),
					'messageReceiverName' 	=> $v->getReceiver()->getUserName(),
					'messageReceiverId'		=> $v->getReceiver()->getId(),

					'viewed'				=> $v->getViewed(),
					'isReceiver'			=> $isReceiver,
					'repliedMessage'		=> $repliedMessage
				));
			}
			else{												// File
				$dateCreated 	= $v->getDateCreated();

				$dateCreated 	= $v->getDateCreated()->format( 'Y-m-d H:i:s' );
				$timestamp 		= strtotime( $dateCreated );
				$day 			= \date( 'Y-m-d', $timestamp);	// message day
				$hour			= \date( 'H:i', $timestamp);
				$displayDate 	= ( $day == $todayDay ) ? $hour : $day . ', ' . $hour;
				$canbedeleted	= $todayTimestamp - $timestamp < $cancelable ? 1 : 0;
				$category		= $v->getChatFileCategory()->getTitle();
				$isAReply		= in_array( $category, [ 'D', 'E' ], );
				$repliedFile	= Array();				

				if( $isAReply ){
					if( $category == 'D' ){
						$repliedArr		= $v->getChatMessageResponse();
						$replied 		= '';

						foreach( $repliedArr as $a => $b ){
							$replied 	= $b;
						}
// echo '-------------- Replied: ' . $replied;
						if( is_object( $replied ) ){
							$replied_message 	= $replied->getChatMessage();
							$replied_name 		= $replied->getUser()->getUserName();
							$repliedFile[ 'name' ] 				= $replied_name;
							$repliedFile[ 'replied_message' ] 	= $replied_message;
							$repliedFile[ 'category' ] 			= $category;
						}
					}
					else if ( $category == 'E' ){
						$repliedArr		= $v->getChatFileResponse();
						$replied 		= '';

						foreach( $repliedArr as $a => $b ){
							$replied 	= $b;
						}

						if( is_object( $replied ) ){
							$replied_name 	= $replied->getUser()->getUserName();
							$repliedFile[ 'name' ] 				= $replied_name;
							$repliedFile[ 'replied_fileSrc' ] 	= $replied->getPath();
							$repliedFile[ 'replied_fileExt' ] 	= $replied->getExtension();
							$repliedFile[ 'replied_fileName' ] 	= $replied->getName();
							$repliedFile[ 'replied_fileSize' ] 	= $replied->getSize() / 1024 / 1024;
							$repliedFile[ 'category' ] 			= $category;
						}
					}
				}

				array_push( $my, Array( 
					'dateCreated' 			=> $dateCreated,
					'canbedeleted'			=> $v->getUser() == $user ? $canbedeleted : 0,
					'fileName' 				=> $v->getName(),
					'side'					=> $side, 
					'timestamp'				=> $timestamp, 
					'day'					=> $day, 
					'hour'					=> $hour, 
					'displayDate'			=> $displayDate,
					'name'					=> $name,
					'size'					=> ( $v->getSize() / 1024 / 1024 ),
					'fileExtension'			=> $v->getExtension(),
					'path'					=> $v->getPath(),
					'type'					=> 'file',

					'messageId'				=> $v->getId(),
					'messageUserName'	 	=> $v->getUser()->getUserName(),
					'messageUserId'			=> $v->getUser()->getId(),
					'messageReceiverName' 	=> $v->getReceiver()->getUserName(),
					'msgReceiverId'			=> $v->getReceiver()->getId(),
					
					'viewed'			=> $v->getViewed(),
					'isReceiver'		=> $isReceiver,
					'repliedFile'		=> $repliedFile	// TODO: deal with multiple file replying a message
				) );
			}
		}
		// Sort messages by dates from older to newer
		function dateCompare($element1, $element2) { // Comparison function  
			$datetime1 = $element1[ 'timestamp' ]; // Timestamp has index 3
			$datetime2 = $element2[ 'timestamp' ]; 
			return $datetime1 - $datetime2;
		}  
		usort( $my, 'App\Controller\dateCompare' ); // Sort the array  
		// $my = array_reverse( $my );

		return $this->json(
            $my,
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }
	
	// save chat message
    public function saveChatMessage(Request $request, EntityManagerInterface $entityManager)
    {
		// Create a message object
		$message 		= new ChatMessage();
		// Get posted data
		$messageTxt 		= $request->getPayload()->get( 'message_text' );
		$receiverId			= $request->getPayload()->get( 'receiver_id' );
		$projectId			= $request->getPayload()->get( 'project_id' );
		$replied_msg_id		= $request->getPayload()->get( 'replied_msg_id' );
		$replied_file_id	= $request->getPayload()->get( 'replied_file_id' );
		// Other message data
		$viewed	 = false;
		$user 	 = $this->getUser();
		// Get entities objects
		$receiver 	= $entityManager->getRepository( User::class )->findOneById( $receiverId );
		$project 	= $entityManager->getRepository( Project::class )->findOneById( $projectId );
		// Set message object
		$message->setReceiver( $receiver );
		$message->setProject( $project );
		$message->setChatMessage( $messageTxt );
		$message->setViewed( $viewed );
		$message->setUser( $user );
		$rep = '';
		$chatItemCategory = $entityManager->getRepository( ChatItemCategory::class );

		if( $replied_msg_id ){
			$replied_msg = $entityManager->getRepository( ChatMessage::class )->findOneById( $replied_msg_id );
			$message->addChatMessageResponse( Array( $replied_msg ) );	// Array() is required here
			$category = $chatItemCategory->findOneByTitle( 'B' );
			$message->setChatMessageCategory( $category );
		}
		else if( $replied_file_id ){
			$replied_file = $entityManager->getRepository( ChatFile::class )->findOneById( $replied_file_id );
			$message->addChatFileResponse( Array( $replied_file ) );
			$category = $chatItemCategory->findOneByTitle( 'C' );
			$message->setChatMessageCategory( $category );
		}
		else{
			$category = $chatItemCategory->findOneByTitle( 'A' );
			$message->setChatMessageCategory( $category );
		}
// var_dump( $rep->getChatMessageResponse()->getMessage() );
// die;
		$entityManager->persist( $message );

        $entityManager->flush();
		
		return new Response( 1, Response::HTTP_OK );

    }
	
	// save file
	public function saveChatFile( FileUploader $fileUploader, Request $request, Project $project, User $receiver, EntityManagerInterface $entityManager )
    {
		$user 	 			= $this->getUser();
		$file 				= new ChatFile();
		$uploadeds  		= $request->files->get('files');
		$replied_msg_id 	= $request->get('replied_msg_id');
		$replied_file_id	= $request->get('replied_file_id');
		
		$uploaded	= $uploadeds[0];
		$extension 	= $uploaded->getClientOriginalExtension();
		$fileName 	= $uploaded->getClientOriginalName();
		$size 		= $uploaded->getSize();
		// Move the file
		$path  	= $fileUploader->upload($uploaded); // Important: call this after $uploaded->getSize()
		
		$file->setPath( $path );		// File nameon the server
		$file->setName( $fileName );	// Original name
		$file->setSize( $size );
		$file->setExtension( $extension );
		$file->setViewed( false );
		$file->setReceiver( $receiver );
		$file->setProject( $project );
		$file->setUser( $user );
		$chatItemCategory = $entityManager->getRepository( ChatItemCategory::class );
		if( $replied_msg_id ){
			$replied_msg = $entityManager->getRepository( ChatMessage::class )->findOneById( $replied_msg_id );
			$file->addChatMessageResponse( Array( $replied_msg ) );
			$category = $chatItemCategory->findOneByTitle( 'D' );
			$file->setChatFileCategory( $category );
		}
		else if( $replied_file_id ){
			$replied_file = $entityManager->getRepository( ChatFile::class )->findOneById( $replied_file_id );
			$file->addChatFileResponse(  Array( $replied_file ) );
			$category = $chatItemCategory->findOneByTitle( 'E' );
			$file->setChatFileCategory( $category );
		}
		else{
			$category = $chatItemCategory->findOneByTitle( 'A' );
			$file->setChatFileCategory( $category );
		}

		$entityManager->persist( $file );
        $entityManager->flush();
		
		return new Response( 1, Response::HTTP_OK );

    }
	
	// Set all project messages as read
	public function setMessagesRead( Request $request, Project $project, EntityManagerInterface $entityManager ): Response
    {
		$user 	    = $this->getUser();
		$projectId	= $project->getId();
        $messages01 = $entityManager->getRepository( ChatMessage::class )	// user sent messages
								    ->findBy( Array( 'user' => $user, 'project' => $project ) );					
        $messages02 = $entityManager->getRepository( ChatMessage::class )	// user received messages
								    ->findBy( Array( 'receiver' => $user, 'project' => $project ) );
        $messages03 = $entityManager->getRepository( ChatFile::class )		// user sent files
								    ->findBy( Array( 'user' => $user, 'project' => $project ) );
        $messages04 = $entityManager->getRepository( ChatFile::class )		// user received files
								    ->findBy( Array( 'receiver' => $user, 'project' => $project ) );
		// Create message array
		$messages   = array_merge( $messages01, $messages02, $messages03, $messages04 );

		// 
		foreach( $messages as $k => $v ){
			$receiver = $v->getReceiver();
			if( $user == $receiver ){
				$v->setViewed( true );
				$entityManager->persist( $v );
				$entityManager->flush();
			}
		}
		
		return new Response( 1, Response::HTTP_OK );
	}

	// Get project's categories API
	public function getCategory(Request $request, EntityManagerInterface $entityManager )
	{
		$projectCategories = $entityManager
            ->getRepository(ProjectCategory::class)
			->findAll();
		
		$response = new JsonResponse();
		$response->setData( $projectCategories );
		$response->setStatusCode( 200, "User created" );
		return $response;
	}

	// Get project's types API
	public function getType(Request $request, EntityManagerInterface $entityManager )
	{
		$projectTypes = $entityManager
            ->getRepository(ProjectType::class)
			->findAll();

		$response = new JsonResponse();
		$response->setData( $projectTypes );
		$response->setStatusCode( 200, "User created" );
		return $response;
	}
	
	// save a project
	public function saveProject( FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager )
    {

		$uploadeds  	= $request->files->get('files') != null ? $request->files->get('files') : [];
		$userId 		= $request->getPayload()->get( 'userId' );
		$categoryId 	= $request->getPayload()->get( 'category' );
		$title 			= $request->getPayload()->get( 'title' );
		$description 	= $request->getPayload()->get( 'description' );
		$typeId			= $request->getPayload()->get( 'type' );
		$budget 		= $request->getPayload()->get( 'budget' );
		$length 		= $request->getPayload()->get( 'length' );
		$sendingDate 	= $request->getPayload()->get( 'sendingDate' );
				// message info
			
		// save the project
		$project = new Project;
		$user = $entityManager
            ->getRepository(User::class)->findOneById( $userId );
		$projectCategory = $entityManager
            ->getRepository(ProjectCategory::class)->findOneById( $categoryId );
		$projectType = $entityManager
            ->getRepository(ProjectType::class)->findOneById( $typeId );
		$project->setUser( $user );
		$project->setProjectCategory( $projectCategory );
		$project->setTitle( $title );
		$project->setDescription( $description );
		$project->setProjectType( $projectType );		
		$project->setBudget( $budget );	
		$project->setDuration( $length );
		$entityManager->persist( $project );
		$entityManager->flush();

		// save project's invitations
		$invitation_Json 	= $request->getPayload()->get( 'invitations' );
		$invitations		=  json_decode( $invitation_Json, true );

		foreach( $invitations as $k => $v ){
			$invitation = new Invitation;
			$invitation->setUser( $user );
			$invitation->setProject( $project );
			$invitation->setReceiverName( $v[ 'name' ] );
			$invitation->setReceiverEmail( $v[ 'email' ] );
			$invitation->setAttempts( 0 );
			$invitation->setStatus( 0 );
			$invitation->setDateSending( new \DateTime( $sendingDate ? $sendingDate : '' ) );

			$entityManager->persist( $invitation );
			$entityManager->flush();
		}

		// save project's  files
		foreach( $uploadeds as $k => $v ){
			$extension 	= $v->getClientOriginalExtension();
			$fileName 	= $v->getClientOriginalName();
			$size 		= $v->getSize();
			// Move the file
			$path  	= $fileUploader->upload( $v ); // Important: call this after $uploaded->getSize()
			$projectFile 	= new ProjectFile;
			$projectFile->setName( $fileName );	// Original name
			$projectFile->setSize( $size );
			$projectFile->setExtension( $extension );
			$projectFile->setProject( $project );

		
			$projectFile->setPath( $path );
		
			$entityManager->persist( $projectFile );
			$entityManager->flush();
		}
	
		$response = new JsonResponse();
		$response->setData( $userId );
		$response->setStatusCode( 200, "Project Saved" );
		return $response;

    }

	// Get sent project API
	public function getSentProjects( Request $request, EntityManagerInterface $entityManager )
	{
		$userId = $request->get('userId');
		$user = $entityManager
            ->getRepository(User::class)
			->findOneById( $userId );
		
		$projects = $entityManager
            ->getRepository(Project::class)
			->findby( Array( 'user' => $user ) );

		
		$response = new JsonResponse();
		$response->setData( $projects );
		$response->setStatusCode( 200, "User created" );
		return $response;
	}
}
