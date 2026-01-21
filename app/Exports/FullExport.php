<?php

namespace App\Exports;

use App\Models\Equipment;
use App\Models\Stock;
use App\Models\Parc;
use App\Models\Maintenance;
use App\Models\HorsService;
use App\Models\Perdu;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullExport implements WithMultipleSheets
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function sheets(): array
    {
        $sheets = [
            new EquipmentExport($this->filters),
            new StockExport($this->filters),
            new ParcExport($this->filters),
            new MaintenanceExport($this->filters),
        ];
        
        return $sheets;
    }
}