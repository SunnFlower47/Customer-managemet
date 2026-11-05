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
            'nama_perusahaan' => 'LumaISP',
            'nama_lengkap_perusahaan' => 'LumaISP Network',
            'inisial_perusahaan' => 'LUMA',
            'alamat' => 'Jl. Contoh Alamat No. 123, Kota Contoh, Provinsi Contoh 12345',
            'nomor_kontak' => '+62 812-3456-7890',
            'whatsapp' => '+62 812-3456-7890',
            'email_support' => 'support@lumaisp.com',
            'website' => 'https://lumaisp.com',
            'deskripsi' => 'Penyedia layanan internet WiFi & Fiber Optic berkualitas tinggi dengan teknologi terdepan untuk kebutuhan bisnis dan rumah tangga.',
            'payment_code_prefix' => 'PAY',
        ]);
    }
}
