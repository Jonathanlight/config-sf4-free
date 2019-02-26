<?php

namespace App\Services;

class TypeEmail
{
    public function checkFormatEmail($email)
    {
        $check = false;
        $MailAuth = [
            '@gmail.com',
            '@yahoo.fr',
            '@hotmail.fr',
            '@hotmail.com',
            '@outlook.fr',
            '@live.fr',
            '@live.com',
            '@outlook.com',
            '@orange.fr',
            '@sfr.fr',
            '@icloud.com',
            '@me.com',
            '@mac.com',
            '@labanquepostale.fr',
            '@laposte.net',
            '@wanadoo.fr'
        ];

        foreach ($MailAuth as $value) {
            if (strchr($email, $value)) {
                $check = true;
            }
        }

        if ($check == false) {
            return false;
        } else {
            return true;
        }
    }
}
