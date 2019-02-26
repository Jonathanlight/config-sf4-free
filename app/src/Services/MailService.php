<?php

namespace App\Services;

class MailService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * MailService constructor.
     *
     * @param \Swift_Mailer       $mailer
     * @param \Twig_Environment   $twig
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }
    /**
     * @param string      $content
     * @param string      $subject
     * @param array       $mailFrom
     * @param array       $mailTo
     */
    public function sendMail($subject = '', array $mailFrom, array $mailTo, $content): void
    {
        $to = $mailTo ?? [];
        $from = $mailFrom ?? [];

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($content, 'text/html');
        $this->mailer->send($message);
    }
}
