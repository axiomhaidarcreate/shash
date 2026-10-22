<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "--- TOTAL TICKETS PER DAY IN DB ---\n";
$t6 = \App\Models\Ticket::whereDate('created_at', '2026-07-06')->count();
echo "Total tickets on 2026-07-06: $t6\n";

$t7 = \App\Models\Ticket::whereDate('created_at', '2026-07-07')->count();
echo "Total tickets on 2026-07-07: $t7\n\n";

echo "--- DELETED TICKETS (if SoftDeletes is used) ---\n";
// if soft deletes is on
if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(\App\Models\Ticket::class))) {
    $deleted = \App\Models\Ticket::onlyTrashed()->whereDate('created_at', '2026-07-06')->count();
    echo "Deleted tickets on 2026-07-06: $deleted\n";
    $deleted7 = \App\Models\Ticket::onlyTrashed()->whereDate('created_at', '2026-07-07')->count();
    echo "Deleted tickets on 2026-07-07: $deleted7\n";
} else {
    echo "SoftDeletes is NOT used on Ticket model.\n";
}

echo "\n--- ALL DAILY REPORTS FOR THESE TWO DAYS ---\n";
$reports = \App\Models\TicketDailyReport::whereDate('created_at', '2026-07-06')
    ->orWhereDate('created_at', '2026-07-07')
    ->get(['id', 'total_tickets', 'opened_at', 'closed_at']);
foreach ($reports as $r) {
    echo "Report #$r->id | Total: $r->total_tickets | Opened: $r->opened_at | Closed: $r->closed_at\n";
}
