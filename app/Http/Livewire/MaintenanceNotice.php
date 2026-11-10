<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class MaintenanceNotice extends Component
{
    public bool $showNotice = false;
    public ?int $endsAt = null; // timestamp en milisegundos para JS

    public function mount(): void
    {
        $enabled = filter_var(env('MAINTENANCE_ALERT', false), FILTER_VALIDATE_BOOLEAN);

        if (! $enabled) {
            return;
        }

        $until = env('MAINTENANCE_UNTIL');

        if ($until) {
            $end = Carbon::parse($until, config('app.timezone'));

            // Si ya pasó la hora, no se muestra
            if ($end->isPast()) {
                return;
            }

            $this->endsAt = $end->getTimestamp() * 1000;
        }

        $this->showNotice = true;
    }

    public function render()
    {
        return view('livewire.maintenance-notice');
    }


}
