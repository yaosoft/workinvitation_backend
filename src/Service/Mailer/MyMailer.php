<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Mailer;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use FOS\UserBundle\Mailer\MailerInterface;
/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class MyMailer implements MailerInterface
{
    private SymfonyMailerInterface $mailer;
    private UrlGeneratorInterface $router;
    private Environment $twig;
    private array $parameters;

    public function __construct(SymfonyMailerInterface $mailer, UrlGeneratorInterface $router, Environment $twig, /*array $parameters */)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        /* $this->parameters = $parameters; */
    }


    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        /* $template = $this->parameters['template']['confirmation']; */
		$template = 'registration_email.txt.twig';
        $url = $this->router->generate('fos_user_registration_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

		$fromEmail[ 'address' ] 	= 'info@diamta.com';
		$fromEmail[ 'sender_name' ] = 'Diamta Projects';

        $this->sendMessage($template, $context, $fromEmail, $user->getEmail());
    }


    public function sendResettingEmailMessage(UserInterface $user): void
    {
        /* $template = $this->parameters['template']['resetting']; */
		$template = 'reseting_email.txt.twig';
        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

		$fromEmail[ 'address' ] 	= 'info@diamta.com';
		$fromEmail[ 'sender_name' ] = 'Diamta Projects';

        $this->sendMessage($template, $context, $fromEmail, $user->getEmail());
    }

    /**
     * @param array<string, mixed>                        $context
     * @param array{address: string, sender_name: string} $fromEmail
     */
    private function sendMessage(string $templateName, array $context, array $fromEmail, string $toEmail): void
    {
        $template = $this->twig->load($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
// var_dump($textBody);
// die;
        $htmlBody = '';

        if ($template->hasBlock('body_html', $context)) {
            $htmlBody = $template->renderBlock('body_html', $context);
        }

        $message = (new Email())
            ->subject($subject)
            ->from(new Address($fromEmail['address'], $fromEmail['sender_name']))
            ->to($toEmail)
            ->text($textBody)
        ;

        if (!empty($htmlBody)) {
            $message->html($htmlBody);
        }
//var_dump($message);
//die;
        $rep = $this->mailer->send($message);

    }
}