<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Custody;
use App\Models\CustodyAssignment;
use App\Models\Employee;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustodyController extends Controller
{
    public function index()
    {
        $custodies = Custody::with('currentAssignment.employee')->latest()->get();
        $assignments = CustodyAssignment::with(['custody', 'employee'])->latest()->get();

        $stats = [
            'total' => $custodies->count(),
            'available' => $custodies->where('status', 'available')->count(),
            'assigned' => $custodies->where('status', 'assigned')->count(),
        ];

        return view('hr.custodies.index', compact('custodies', 'assignments', 'stats'));
    }

    public function create()
    {
        // Get types from settings (JSON array string or comma separated)
        $typesSetting = Setting::get('custody_types', 'سيارة,جهاز محمول,معدة,أخرى');
        $types = array_map('trim', explode(',', $typesSetting));

        return view('hr.custodies.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:custodies',
            'name' => 'required|string',
            'type' => 'required|string',
            'details' => 'nullable|string',
            'attachments.*' => 'image|max:5120', // max 5MB per image
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            $files = is_array($files) ? $files : [$files];
            foreach (array_slice($files, 0, 10) as $file) {
                $attachments[] = \App\Services\SupabaseStorageService::upload('attachments', $file, 'custodies');
            }
        }

        Custody::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'type' => $data['type'],
            'details' => $data['details'],
            'attachments' => $attachments,
            'status' => 'available'
        ]);

        return redirect()->route('custodies.index')->with('success', 'تم إضافة العهدة بنجاح.');
    }

    public function edit(Custody $custody)
    {
        $typesSetting = Setting::get('custody_types', 'سيارة,جهاز محمول,معدة,أخرى');
        $types = array_map('trim', explode(',', $typesSetting));

        return view('hr.custodies.edit', compact('custody', 'types'));
    }

    public function update(Request $request, Custody $custody)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:custodies,code,' . $custody->id,
            'name' => 'required|string',
            'type' => 'required|string',
            'details' => 'nullable|string',
            'attachments.*' => 'image|max:5120',
        ]);

        $attachments = $custody->attachments ?? [];
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            $files = is_array($files) ? $files : [$files];
            foreach (array_slice($files, 0, 10) as $file) {
                $attachments[] = \App\Services\SupabaseStorageService::upload('attachments', $file, 'custodies');
            }
        }

        $custody->update([
            'code' => $data['code'],
            'name' => $data['name'],
            'type' => $data['type'],
            'details' => $data['details'],
            'attachments' => $attachments,
        ]);

        return redirect()->route('custodies.index')->with('success', 'تم تعديل العهدة بنجاح.');
    }

    public function assignForm()
    {
        $employees = Employee::where('is_active', true)->orderBy('full_name_ar')->get();
        $custodies = Custody::where('status', 'available')->get();

        return view('hr.custodies.assign', compact('employees', 'custodies'));
    }

    public function assignStore(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'custody_id' => 'required|exists:custodies,id',
            'assigned_at' => 'required|date',
            'assignment_report' => 'nullable|string',
            'signature' => 'required|string', // Base64 data url from canvas
        ]);

        $custody = Custody::findOrFail($data['custody_id']);
        if ($custody->status !== 'available') {
            return back()->withErrors(['custody_id' => 'هذه العهدة غير متاحة حالياً.']);
        }

        CustodyAssignment::create([
            'custody_id' => $data['custody_id'],
            'employee_id' => $data['employee_id'],
            'assigned_at' => $data['assigned_at'],
            'assignment_report' => $data['assignment_report'],
            'assignment_signature' => $data['signature'],
        ]);

        $custody->update(['status' => 'assigned']);

        return redirect()->route('custodies.index')->with('success', 'تم ربط العهدة بالموظف بنجاح.');
    }

    public function returnForm(Request $request)
    {
        $custodyId = $request->get('custody_id');
        $assignments = CustodyAssignment::with(['custody', 'employee'])
            ->whereNull('returned_at')
            ->get();
            
        $selectedAssignment = $custodyId ? $assignments->where('custody_id', '==', $custodyId)->first() : null;

        return view('hr.custodies.return', compact('assignments', 'selectedAssignment'));
    }

    public function returnStore(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => 'required|exists:custody_assignments,id',
            'returned_at' => 'required|date',
            'return_notes' => 'nullable|string',
            'return_signature' => 'required|string',
            'attachments.*' => 'image|max:5120',
        ]);

        $assignment = CustodyAssignment::findOrFail($data['assignment_id']);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            $files = is_array($files) ? $files : [$files];
            foreach (array_slice($files, 0, 10) as $file) {
                $attachments[] = \App\Services\SupabaseStorageService::upload('attachments', $file, 'custodies/returns');
            }
        }

        $assignment->update([
            'returned_at' => $data['returned_at'],
            'return_notes' => $data['return_notes'],
            'return_signature' => $data['return_signature'],
            'return_attachments' => $attachments,
        ]);

        $assignment->custody->update(['status' => 'available']);

        return redirect()->route('custodies.index')->with('success', 'تم فك ارتباط العهدة واسترجاعها بنجاح.');
    }

    public function destroy(Custody $custody)
    {
        // منع حذف العهدة إذا كانت مرتبطة بموظف
        if ($custody->status === 'assigned') {
            return back()->with('error', 'لا يمكن حذف عهدة مرتبطة بموظف. يرجى فك الارتباط أولاً.');
        }

        // حذف الصور المرفقة من التخزين
        if ($custody->attachments) {
            foreach ($custody->attachments as $path) {
                \App\Services\SupabaseStorageService::delete($path);
            }
        }

        $custody->delete();

        return redirect()->route('custodies.index')->with('success', 'تم حذف العهدة بنجاح.');
    }
}

