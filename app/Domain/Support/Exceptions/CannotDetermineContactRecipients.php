<?php

namespace App\Domain\Support\Exceptions;

use RuntimeException;

class CannotDetermineContactRecipients extends RuntimeException
{
    public static function create(): self
    {
        return new self('No pudimos determinar el correo receptor para los mensajes de contacto.');
    }
}


