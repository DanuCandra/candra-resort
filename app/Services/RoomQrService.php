<?php

namespace App\Services;

use App\Models\Room;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;

class RoomQrService
{
    public function accessUrl(Room $room): string
    {
        $token = $this->ensureToken($room);

        return route('room-service.verify', ['qrToken' => $token]);
    }

    public function png(Room $room, int $size = 420): string
    {
        $qrCode = new QrCode(
            data: $this->accessUrl($room),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 16,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return (new PngWriter)->write($qrCode)->getString();
    }

    private function ensureToken(Room $room): string
    {
        if (filled($room->qr_token)) {
            return $room->qr_token;
        }

        $room->forceFill(['qr_token' => (string) Str::uuid()])->saveQuietly();

        return $room->qr_token;
    }
}
