<?php

namespace App\Console\Commands;

use App\Models\PresupuestoProyecto;
use Illuminate\Console\Command;

class ReordenarItemsPresupuesto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:reordenar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $presupuestos = PresupuestoProyecto::with('presupuestoItems')->get();

        foreach ($presupuestos as $presupuesto) {
            foreach ($presupuesto->presupuestoItems as $index => $item) {
                $index_item = $index + 1;
                $item->num_item = $index_item;
                $item->orden = $index_item;
                $item->save();
            }
        }

        $this->info('Items actualizados.');
    }
}
