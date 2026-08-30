<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = User::query()->where('employee_code', 'OWN-001')->value('id');
        $methods = [
            ['name' => 'Midtrans', 'code' => 'midtrans', 'type' => 'gateway', 'channel' => 'midtrans', 'gateway_method_code' => null, 'instructions' => 'Pembayaran online melalui halaman aman Midtrans.', 'is_online' => true, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Cash', 'code' => 'cash', 'type' => 'cash', 'channel' => 'manual', 'gateway_method_code' => null, 'instructions' => 'Pembayaran tunai kepada Receptionist.', 'is_online' => false, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Debit', 'code' => 'debit', 'type' => 'debit', 'channel' => 'manual', 'gateway_method_code' => null, 'instructions' => 'Pembayaran melalui mesin EDC hotel.', 'is_online' => false, 'is_active' => true, 'sort_order' => 3],
            ['name' => 'QRIS Manual', 'code' => 'qris-manual', 'type' => 'qris', 'channel' => 'manual', 'gateway_method_code' => null, 'instructions' => 'Tunjukkan bukti pembayaran QRIS kepada Receptionist.', 'is_online' => false, 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Bank Transfer', 'code' => 'bank-transfer', 'type' => 'bank_transfer', 'channel' => 'manual', 'gateway_method_code' => null, 'instructions' => 'Konfirmasi nomor referensi transfer kepada Receptionist.', 'is_online' => false, 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Credit Card', 'code' => 'credit-card', 'type' => 'card', 'channel' => 'manual', 'gateway_method_code' => null, 'instructions' => 'Pembayaran kartu kredit melalui mesin EDC hotel.', 'is_online' => false, 'is_active' => true, 'sort_order' => 6],
        ];

        foreach ($methods as $data) {
            $method = PaymentMethod::query()->withTrashed()->updateOrCreate(
                ['code' => $data['code']],
                [...$data, 'created_by' => $ownerId]
            );
            $method->restore();
        }
    }
}
