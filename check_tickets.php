<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$start = '2026-07-06 20:13:38';
$end = '2026-07-07 13:32:31';

$count = \App\Models\Ticket::whereBetween('created_at', [$start, $end])->count();
echo "Count for period $start to $end: $count tickets\n";

$allInRange = \App\Models\Ticket::whereBetween('created_at', [$start, $end])->get(['id', 'created_at', 'status', 'is_daily_closed']);
echo "List of tickets:\n";
foreach ($allInRange as $t) {
    echo "ID: $t->id | Created: $t->created_at | Status: $t->status | DailyClosed: $t->is_daily_closed\n";
}
