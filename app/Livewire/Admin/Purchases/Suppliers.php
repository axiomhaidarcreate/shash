<?php

namespace App\Livewire\Admin\Purchases;

use Livewire\Component;
use App\Models\Supplier;

class Suppliers extends Component
{
    public $search = '';
    public $name = '';
    public $phone = '';
    public $company = '';
    public $balance = 0;
    
    public $trade_name = '';
    public $contact_person = '';
    public $tax_number = '';
    public $national_id = '';
    public $city = '';
    public $address = '';
    public $website = '';
    
    public $isEditing = false;
    public $editId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'company' => 'nullable|string|max:255',
        'balance' => 'numeric',
        'trade_name' => 'nullable|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'tax_number' => 'nullable|string|max:255',
        'national_id' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'website' => 'nullable|string|max:255',
    ];

    public function resetFields()
    {
        $this->reset([
            'name', 'phone', 'company', 'balance', 'isEditing', 'editId',
            'trade_name', 'contact_person', 'tax_number', 'national_id', 'city', 'address', 'website'
        ]);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'company' => $this->company,
            'balance' => $this->balance,
            'trade_name' => $this->trade_name,
            'contact_person' => $this->contact_person,
            'tax_number' => $this->tax_number,
            'national_id' => $this->national_id,
            'city' => $this->city,
            'address' => $this->address,
            'website' => $this->website,
        ];

        if ($this->isEditing) {
            $supplier = Supplier::find($this->editId);
            $supplier->update($data);
            session()->flash('success', 'تم تحديث بيانات المورد بنجاح');
        } else {
            Supplier::create($data);
            session()->flash('success', 'تم إضافة المورد بنجاح');
        }

        $this->resetFields();
    }

    public function edit($id)
    {
        $supplier = Supplier::find($id);
        $this->editId = $supplier->id;
        $this->name = $supplier->name;
        $this->phone = $supplier->phone;
        $this->company = $supplier->company;
        $this->balance = $supplier->balance;
        $this->trade_name = $supplier->trade_name;
        $this->contact_person = $supplier->contact_person;
        $this->tax_number = $supplier->tax_number;
        $this->national_id = $supplier->national_id;
        $this->city = $supplier->city;
        $this->address = $supplier->address;
        $this->website = $supplier->website;
        $this->isEditing = true;
    }

    public function render()
    {
        $suppliers = Supplier::where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('company', 'like', '%' . $this->search . '%')
                             ->orderBy('created_at', 'desc')
                             ->get();
                             
        return view('livewire.admin.purchases.suppliers', [
            'suppliers' => $suppliers,
        ])->layout('components.layouts.admin', ['title' => 'إدارة الموردين']);
    }
}
