<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Envío a distritos (modo ENVIO)
    |--------------------------------------------------------------------------
    |
    | Activa el precio de envío (sendCost) y el costo de exceso (excess /
    | excessFactor) del envío a otros distritos. En false, ambos se exponen en
    | 0 al frontend y no se cobran en el pedido. Los valores sincronizados
    | desde 360Sys se siguen guardando en BD sin cambios.
    |
    */

    'district_enabled' => env('SHIPPING_DISTRICT_ENABLED', true),

];
