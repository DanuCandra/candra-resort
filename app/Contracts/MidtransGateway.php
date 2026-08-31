<?php

namespace App\Contracts;

// Mendefinisikan kontrak untuk pembuatan token pembayaran Midtrans.
interface MidtransGateway
{
    public function createSnapToken(array $parameters): string;
}
