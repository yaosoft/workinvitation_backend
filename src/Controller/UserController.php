<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
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
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Address;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;



class UserController extends AbstractController
{
	private $fos_manager;
	private $passwordHasher;
	
    // constructor
    public function __construct(UserManagerInterface $fos_manager, UserPasswordHasherInterface $passwordHasher)  {
        $this->fos_manager = $fos_manager;
		$this->passwordHasher = $passwordHasher;
    }

	// Registration API
	public function registration(Request $request)
	{
		$email 		= $request->getPayload()->get('email');
		$username 	= $email;
		$password 	= $request->getPayload()->get('password');

		$email_exist 	= $this->fos_manager->findUserByEmail($email);
		// $username_exist = $this->fos_manager->findUserByUsername($username);

		if( $email_exist != Null /* || $username_exist */ ){
			$response = new JsonResponse();
			$response->setData( "Username " . $username . " already created");
			$response->setStatusCode(201, "User already exists");
			return $response;
		}

		$user = $this->fos_manager->createUser();
		$user->setUsername($username);
		$user->setEmail($email);
		// $user->setLocked(false); 
		$user->setEnabled(true); 
		$user->setPlainPassword($password);
		$this->fos_manager->updateUser($user, true);

		$response = new JsonResponse();
		$response->setData("User: ".$user->getUsername()." wurde erstellt");
		$response->setStatusCode(200, "User created");
		return $response;

	}
	
	// Login API

	
	public function login(Request $request, Security $security)
	{
		
		$email 		= $request->getPayload()->get('email');
		$username 	= $email;
		$plaintextPassword 	= $request->getPayload()->get('password');
 
		$user 	= $this->fos_manager->findUserByEmail($email);
		// $username_exist = $this->fos_manager->findUserByUsername($username);
		$response = new JsonResponse();
		if( $user == Null /* || $username_exist */ ){
			$response->setData( "Username " . $username . " not existing");
			$response->setStatusCode(201, "User not exists");
			return $response;
		}
		
		// get the password error if there is one
		$isPasswordValid = $this->passwordHasher->isPasswordValid( $user, $plaintextPassword );
		if( $isPasswordValid === true ){
			// $security->login($user);
			$response->setData( $user->getId() );
			$response->setStatusCode( 200, "User is loged in" );
		}
		else{
			$response->setData( "Bad password" );
			$response->setStatusCode( 201, "Bad password" );
		}
		
		return $response;
	}
	
	public function logout(Request $request, Security $security)
	{
		
		$id 	= $request->getPayload()->get('email');
		$user 	= $this->fos_manager->findUserById( $id );
		$response = new JsonResponse();
		if( $user == Null /* || $username_exist */ ){
			$response->setData( "Username " . $username . " not existing");
			$response->setStatusCode(201, "User not exists");
			return $response;
		}
		
		$response->setData( "Username " . $username . " loged out");
		$response->setStatusCode(200, "User loged out");
		$security->logout($user);
		
		return $response;
	}

}  