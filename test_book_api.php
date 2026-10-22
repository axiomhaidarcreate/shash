<?php
// Test the booking API endpoint
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find a customer
$customer = \App\Models\Customer::find(13);
if (!$customer) {
    echo "Customer not found!\n";
    exit;
}
echo "Customer: {$customer->name} ({$customer->phone})\n";

// Find an active event
$event = \App\Models\Event::where('is_active', true)->first();
if (!$event) {
    echo "No active event found!\n";
    exit;
}
echo "Event: {$event->title} (ID: {$event->id})\n";

// Get tables
$tables = \App\Models\Table::with('chairs')->where('section', 'صالة الحفلات')->first();
if (!$tables) {
    echo "No tables found in 'صالة الحفلات'!\n";
    $sections = \App\Models\Table::distinct()->pluck('section');
    echo "Available sections: " . json_encode($sections, JSON_UNESCAPED_UNICODE) . "\n";
    
    // Try getting all tables
    $allTables = \App\Models\Table::all();
    echo "Total tables: " . $allTables->count() . "\n";
    foreach ($allTables->take(5) as $t) {
        echo "  - Table #{$t->number}, section: {$t->section}\n";
    }
    exit;
}
echo "Table: {$tables->number} (ID: {$tables->id})\n";

$chair = $tables->chairs->first();
if (!$chair) {
    echo "No chairs found!\n";
    exit;
}
echo "Chair: {$chair->number} (ID: {$chair->id})\n";

// Test if token works with Sanctum - try to authenticate
$token = $customer->createToken('test_booking')->plainTextToken;
echo "\nToken created: {$token}\n";

// Test authentication by resolving the user from the token
$parts = explode('|', $token);
$tokenId = $parts[0];
$tokenHash = hash('sha256', $parts[1]);

$accessToken = \Laravel\Sanctum\PersonalAccessToken::where('id', $tokenId)->first();
if ($accessToken) {
    echo "Token found in DB - tokenable_type: {$accessToken->tokenable_type}, tokenable_id: {$accessToken->tokenable_id}\n";
    $user = $accessToken->tokenable;
    echo "Resolved user: " . ($user ? $user->name : 'NULL') . "\n";
} else {
    echo "Token NOT found in DB!\n";
}

// Now try calling the book endpoint via HTTP
echo "\n--- Testing POST /api/events/{$event->id}/book ---\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8001/api/events/{$event->id}/book");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'gender' => 'ذكر',
    'table_id' => $tables->id,
    'chair_id' => $chair->id,
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Response: {$response}\n";
