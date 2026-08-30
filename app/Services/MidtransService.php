<?php

namespace App\Services;

use App\Contracts\MidtransGateway;
use Midtrans\Config;
use Midtrans\Snap;
use RuntimeException;

class MidtransService implements MidtransGateway
{
    public function createSnapToken(array $parameters): string
    {
        $this->configure();

        return Snap::getSnapToken($parameters);
    }

    private function configure(): void
    {
        $serverKey = config('services.midtrans.server_key');
        if (! $serverKey) {
            throw new RuntimeException('Konfigurasi MIDTRANS_SERVER_KEY belum tersedia.');
        }

        Config::$serverKey = $serverKey;
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}
