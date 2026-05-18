<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Seuils de cycle de vie (âge = maintenant − date de référence)
    |--------------------------------------------------------------------------
    |
    | La date de référence est : date_mise_service si renseignée, sinon date_livraison.
    |
    | - recent          : âge < orange_years (vert)
    | - seuil_reference : orange_years ≤ âge < red_years (orange — vigilance)
    | - a_remplacer     : âge ≥ red_years (rouge — seuil critique)
    |
    */
    'orange_years' => (float) env('EQUIPMENT_RENEWAL_ORANGE_YEARS', 2),

    'red_years' => (float) env('EQUIPMENT_RENEWAL_RED_YEARS', 3),
];
