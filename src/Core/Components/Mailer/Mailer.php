<?php

namespace App\Core\Components\Mailer;

use App\Core\Classes\TwigEnvironment;
use App\Core\Components\Flash\FlashesBag;
use App\Core\Form\FormErrorBag;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Transport;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Loader\FilesystemLoader;
use \Symfony\Component\Mailer\Mailer as SymfonyMailer;

class Mailer
{

    private SymfonyMailer $mailer;


    public function __construct()
    {
        // Twig init
        $twig = new TwigEnvironment();

        // Mailer init
        $messageListener = new MessageListener(null, new BodyRenderer($twig));

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($messageListener);

        $transport = Transport::fromDsn(config('mail.dsn'), $eventDispatcher);
        $this->mailer = new SymfonyMailer($transport, null, $eventDispatcher);
    }


    /**
     *
     * Shorthand to send a templated mail with project base template
     *
     * @param string $to
     * @param string $subject
     * @param string $template
     * @param array $templateArgs
     * @param string|null $from
     *
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendMail(
        string $to,
        string $subject,
        string $template,
        array  $templateArgs = [],
        string $from = null
    )
    {
        $from = $from === null ? config('mail.default.sender') : $from;

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($templateArgs);

        $this->mailer->send($email);
    }


}
