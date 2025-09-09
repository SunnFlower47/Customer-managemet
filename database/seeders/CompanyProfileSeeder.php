<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyProfile;

class CompanyProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyProfile::create([
            'nama_perusahaan' => 'BCM.net',
            'nama_lengkap_perusahaan' => 'Baraya Citra Mandiri',
            'inisial_perusahaan' => 'BCM',
            'alamat' => 'Jl. Contoh Alamat No. 123, Kota Contoh, Provinsi Contoh 12345',
            'nomor_kontak' => '+62 812-3456-7890',
            'whatsapp' => '+62 812-3456-7890',
            'email_support' => 'support@bcmnet.com',
            'website' => 'https://bcmnet.com',
            'deskripsi' => 'Penyedia layanan internet WiFi berkualitas tinggi dengan teknologi terdepan untuk kebutuhan bisnis dan rumah tangga.',
            'payment_code_prefix' => 'PAY',
        ]);
    }
}
