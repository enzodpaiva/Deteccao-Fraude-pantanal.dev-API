<?php

namespace App\Bussiness\Services\Interfaces;

interface EmailValidationService
{
    public function validateEmail(string $email): bool;
}
