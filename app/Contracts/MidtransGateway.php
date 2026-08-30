<?php

namespace App\Contracts;

interface MidtransGateway
{
    public function createSnapToken(array $parameters): string;
}
