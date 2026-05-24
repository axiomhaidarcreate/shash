<?php

namespace App\Http\Controllers;

use App\Models\PrintJob;
use Illuminate\Http\Request;
use App\Models\Setting;

class PrintApiController extends Controller
{
    private function checkToken(Request $request)
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $token = $settings['print_api_token'] ?? '';
        
        if (empty($token) || $request->input('token') !== $token) {
            abort(401, 'Unauthorized');
        }
        return $settings;
    }

    public function getPendingJobs(Request $request)
    {
        $settings = $this->checkToken($request);

        $jobs = PrintJob::where('status', 'pending')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'printers' => [
                'cashier' => $settings['printer_cashier_ip'] ?? '',
                'kitchen' => $settings['printer_kitchen_ip'] ?? '',
                'shisha' => $settings['printer_shisha_ip'] ?? '',
            ],
            'port' => $settings['printer_port'] ?? '9100',
            'jobs' => $jobs
        ]);
    }

    public function markAsPrinted(Request $request, $id)
    {
        $this->checkToken($request);

        $job = PrintJob::find($id);
        if ($job) {
            $job->status = 'printed';
            $job->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Job not found'], 404);
    }

    public function updateStatus(Request $request)
    {
        $this->checkToken($request);

        $statuses = $request->input('statuses', []);
        
        foreach ($statuses as $type => $status) {
            if (in_array($type, ['cashier', 'kitchen', 'shisha'])) {
                Setting::updateOrCreate(
                    ['key' => "printer_{$type}_status"],
                    ['value' => $status]
                );
            }
        }

        return response()->json(['success' => true]);
    }
}
