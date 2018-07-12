<?php

namespace App\Mailer;

use App\Entity\Ad;
use Twig\Environment;

class Mailer
{
    private $twig;
    private $swift;
    const FROM = "notif@meilleurcoin.com";
    const ADMIN = "admin@meilleurcoin.com";

    public function __construct(Environment $twig, \Swift_Mailer $swift)
    {
        $this->twig = $twig;
        $this->swift = $swift;
    }


    public function sendNewAdWarning(Ad $ad)
    {
        $message = (new \Swift_Message('Nouvelle annonce!'))
            ->setFrom(self::FROM)
            ->setTo(self::ADMIN)
            ->setBody(
                $this->twig->render(
                    'emails/ad_create_warning.html.twig',
                    ['ad' => $ad]
                ),
                'text/html'
            )
        ;

        $this->swift->send($message);
    }

}