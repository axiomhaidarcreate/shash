<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\ContractTemplate;
use Illuminate\Http\Request;

class ContractTemplateController extends Controller
{
    public function index()
    {
        $templates = ContractTemplate::latest()->paginate(10);
        return view('hr.contract_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('hr.contract_templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        ContractTemplate::create($validated);

        return redirect()->route('contract_templates.index')->with('success', 'تم حفظ قالب العقد بنجاح');
    }

    public function show(ContractTemplate $contractTemplate)
    {
        return view('hr.contract_templates.show', compact('contractTemplate'));
    }

    public function edit(ContractTemplate $contractTemplate)
    {
        return view('hr.contract_templates.edit', compact('contractTemplate'));
    }

    public function update(Request $request, ContractTemplate $contractTemplate)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $contractTemplate->update($validated);

        return redirect()->route('contract_templates.index')->with('success', 'تم تحديث قالب العقد بنجاح');
    }

    public function destroy(ContractTemplate $contractTemplate)
    {
        $contractTemplate->delete();
        return redirect()->route('contract_templates.index')->with('success', 'تم حذف قالب العقد');
    }
}

