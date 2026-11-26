<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mock temporal de deuda predial (RF-37)
    |--------------------------------------------------------------------------
    |
    | TODO(Adrian): eliminar este mock cuando `EstadoCuentaPredialService::adeuda()`
    | esté disponible. Se usa solo para demostrar la alerta de mora mientras el
    | servicio real no existe; por defecto está apagado.
    |
    */

    'mock_deuda' => (bool) env('SOLVENCIA_MOCK_DEUDA', false),

    'mock_anios' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('SOLVENCIA_MOCK_ANIOS', '')),
    ))),

];
