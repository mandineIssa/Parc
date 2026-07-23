<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class EquipmentLabelController extends Controller
{
    public function pdf(Equipment $equipment)
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => 4,
            'imageBase64' => true,
        ]);
        $qrDataUri = (new QRCode($options))->render($equipment->numero_serie);

        $pdf = Pdf::loadView('equipment.label-pdf', compact('equipment', 'qrDataUri'))
            ->setPaper([0, 0, 226.77, 113.39], 'portrait');

        return $pdf->download('etiquette-'.$equipment->numero_serie.'.pdf');
    }
}
