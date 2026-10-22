<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Table;
use App\Models\Section;

class TableSelector extends Component
{
    public $tableSearch = '';
    public $selectedSection = null;
    public $selectedTableId = null;
    public $selectedChairId = null;
    public $filterSection = null;

    // Optional props for initial state
    public $initialTableId = null;
    public $excludeOccupied = false;

    public function mount($initialTableId = null, $excludeOccupied = false, $filterSection = null)
    {
        $this->initialTableId = $initialTableId;
        $this->selectedTableId = $initialTableId;
        $this->excludeOccupied = $excludeOccupied;
        $this->filterSection = $filterSection;
        if ($this->filterSection) {
            $this->selectedSection = $this->filterSection;
        }
    }

    public function selectSection($name)
    {
        $this->selectedSection = $name;
    }

    public function selectTable($id)
    {
        $this->selectedTableId = $id;
        $this->selectedChairId = null;
        $this->resetErrorBag('selectedTable');
        $this->resetErrorBag('selectedChair');
    }

    public function selectChair($chairId)
    {
        $chair = \App\Models\Chair::find($chairId);
        if ($chair && $chair->status !== 'empty') {
            $this->addError('selectedChair', 'هذا الكرسي مشغول حالياً.');
            return;
        }
        $this->selectedChairId = $chairId;
        $this->resetErrorBag('selectedChair');
    }

    public function confirmSelection()
    {
        if (!$this->selectedTableId) {
            $this->addError('selectedTable', 'يرجى اختيار طاولة أولاً.');
            return;
        }

        $table = Table::find($this->selectedTableId);
        $chair = $this->selectedChairId ? \App\Models\Chair::find($this->selectedChairId) : null;
        if ($table) {
            $tableNumberStr = $table->number;
            if ($chair) {
                $tableNumberStr .= " (كرسي: {$chair->number})";
            }
            $this->dispatch('tableSelected', tableId: $table->id, tableNumber: $tableNumberStr, chairId: $this->selectedChairId);
        }
    }

    public function cancelSelection()
    {
        $this->dispatch('tableSelectionCancelled');
    }

    public function render()
    {
        $sectionsQuery = Section::orderBy('name');
        if ($this->filterSection) {
            $sectionsQuery->where('name', $this->filterSection);
        }
        $sections = $sectionsQuery->get();
        
        $tablesQuery = Table::query()->with(['eventBookings' => function($q) {
            $q->whereIn('status', ['approved', 'pending'])
              ->whereHas('event', function($q2) {
                  $q2->whereDate('event_date', '>=', now()->toDateString());
              });
        }]);

        if ($this->tableSearch) {
            $tablesQuery->where('number', 'like', '%' . $this->tableSearch . '%');
        }
        
        if ($this->selectedSection !== null) {
            $tablesQuery->where('section', $this->selectedSection);
            $tables = $tablesQuery->orderBy('sort_order')->orderBy('id')->get();
        } else {
            $tables = collect();
        }

        return view('livewire.table-selector', [
            'sections' => $sections,
            'tables' => $tables,
        ]);
    }
}
