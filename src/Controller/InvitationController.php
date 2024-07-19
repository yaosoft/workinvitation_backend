<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
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

#[Route('/invitation')]
class InvitationController extends AbstractController
{
    public function send(Project $project, EntityManagerInterface $entityManager): Response
    {
		
        return $this->render('invitation/send.html.twig', [
            'project' => $project,
        ]);
    }
	
	public function sendInvitation( Request $request, EntityManagerInterface $entityManager, SymfonyMailerInterface $mailer )
    {
		$toEmail 	= $request->getPayload()->get( 'email' );
		$name		= $request->getPayload()->get( 'name' );
		$projectId	= $request->getPayload()->get( 'projectId' );
		$project 	= $entityManager->getRepository( Project::class )->findOneById( $projectId );
		$template 	= 'registration_email.txt.twig';
		$subject	= 'You received a Work Invitation!';
		$fromEmail	= Array();
		$fromEmail['address'] 		= 'info@diamta.com';
		$fromEmail['sender_name'] 	= 'Work Invitation';
		
		$projectLink 	= $project->getId();	// TODO		
		$partText 		= 'Click here to open the project and reply to the invitation by sending your proposal. <a href=' . $projectLink . '>' . $projectLink . '</a>';
		$htmlBody		= $project->getTitle() . '<br>' . $project->getDescription();
		$textBody		= $htmlBody;
		$message = ( new Email() )
            ->subject( $subject )
            ->from( new Address($fromEmail['address'], $fromEmail['sender_name']) )
            ->to( $toEmail )
            ->text( $textBody )
        ;

        if ( !empty( $htmlBody ) ) {
            $message->html( $htmlBody );
        }

		$rep = $mailer->send( $message );

		return $this->json(
            $rep,
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }
}
