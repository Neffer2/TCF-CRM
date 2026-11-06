<?php

namespace App\Http\Livewire;

use Livewire\Component;

class MaintenanceNotice extends Component
{
    public bool $showNotice = true;

    public function mount(): void
    {
        $this->showNotice = filter_var(env('MAINTENANCE_ALERT', false), FILTER_VALIDATE_BOOLEAN);
    }

    public function render()
    {
        return view('livewire.maintenance-notice');
    }


}
