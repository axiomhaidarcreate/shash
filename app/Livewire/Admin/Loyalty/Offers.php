<?php

namespace App\Livewire\Admin\Loyalty;

use Livewire\Component;
use App\Models\LoyaltyOffer;
use App\Models\LoyaltyRewardTier;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Offers extends Component
{
    public $offers;
    
    public $offer_id, $name, $points_per_visit, $points_per_order, $points_per_riyal;
    
    public $isModalOpen = false;

    // Reward tiers: array of ['required_points' => x, 'reward_description' => y]
    public $rewardTiers = [];

    public function mount()
    {
        $this->loadOffers();
    }

    public function loadOffers()
    {
        $this->offers = LoyaltyOffer::with('rewardTiers')->get();
    }

    public function openModal()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->offer_id = null;
        $this->name = '';
        $this->points_per_visit = 0;
        $this->points_per_order = 0;
        $this->points_per_riyal = 0;
        $this->rewardTiers = [];
    }

    public function addTier()
    {
        $this->rewardTiers[] = ['required_points' => 0, 'reward_description' => ''];
    }

    public function removeTier($index)
    {
        unset($this->rewardTiers[$index]);
        $this->rewardTiers = array_values($this->rewardTiers);
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'points_per_visit' => 'numeric|min:0',
            'points_per_order' => 'numeric|min:0',
            'points_per_riyal' => 'numeric|min:0',
            'rewardTiers' => 'required|array|min:1',
            'rewardTiers.*.required_points' => 'required|numeric|min:1',
            'rewardTiers.*.reward_description' => 'required|string|max:255',
        ], [
            'rewardTiers.required' => 'يجب إضافة مستوى مكافأة واحد على الأقل.',
            'rewardTiers.min' => 'يجب إضافة مستوى مكافأة واحد على الأقل.',
            'rewardTiers.*.required_points.required' => 'يجب تحديد النقاط المطلوبة.',
            'rewardTiers.*.required_points.min' => 'يجب أن تكون النقاط المطلوبة أكبر من 0.',
            'rewardTiers.*.reward_description.required' => 'يجب كتابة وصف المكافأة.',
        ]);

        $offer = LoyaltyOffer::updateOrCreate(['id' => $this->offer_id], [
            'name' => $this->name,
            'points_per_visit' => $this->points_per_visit,
            'points_per_order' => $this->points_per_order,
            'points_per_riyal' => $this->points_per_riyal,
        ]);

        // Sync reward tiers
        $offer->rewardTiers()->delete();
        foreach ($this->rewardTiers as $index => $tier) {
            $offer->rewardTiers()->create([
                'required_points' => $tier['required_points'],
                'reward_description' => $tier['reward_description'],
                'sort_order' => $index + 1,
            ]);
        }

        $this->dispatch('notify', ['message' => 'تم حفظ العرض بنجاح', 'type' => 'success']);
        $this->closeModal();
        $this->loadOffers();
    }

    public function edit($id)
    {
        $offer = LoyaltyOffer::with('rewardTiers')->findOrFail($id);
        $this->offer_id = $offer->id;
        $this->name = $offer->name;
        $this->points_per_visit = $offer->points_per_visit;
        $this->points_per_order = $offer->points_per_order;
        $this->points_per_riyal = $offer->points_per_riyal;
        
        // Load existing tiers
        $this->rewardTiers = $offer->rewardTiers->map(function ($tier) {
            return [
                'required_points' => $tier->required_points,
                'reward_description' => $tier->reward_description,
            ];
        })->toArray();

        // If no tiers yet (old data), create one from legacy fields
        if (empty($this->rewardTiers) && $offer->required_points > 0 && $offer->reward_description) {
            $this->rewardTiers = [[
                'required_points' => $offer->required_points,
                'reward_description' => $offer->reward_description,
            ]];
        }
        
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        LoyaltyOffer::find($id)->delete();
        $this->dispatch('notify', ['message' => 'تم حذف العرض', 'type' => 'success']);
        $this->loadOffers();
    }

    public function render()
    {
        return view('livewire.admin.loyalty.offers');
    }
}
