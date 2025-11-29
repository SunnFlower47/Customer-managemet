<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

try {
    $customer = App\Models\Pelanggan::where('nama', 'Virus')->first();

    if (!$customer) {
        echo json_encode(['error' => 'Customer Virus not found']);
        exit(1);
    }

    $token = $customer->createToken('debug')->plainTextToken;

    echo json_encode([
        'success' => true,
        'token' => $token,
        'customer_id' => $customer->id,
        'customer_name' => $customer->nama
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit(1);
}
