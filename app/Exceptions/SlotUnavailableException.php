<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a slot is requested but is no longer free — e.g. another patient
 * held it in the moment between rendering the results and submitting the form.
 */
class SlotUnavailableException extends RuntimeException
{
    public static function make(): self
    {
        return new self('Termin više nije slobodan.');
    }
}
