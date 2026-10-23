<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API del chatbot de pedidos (/api/chatbot)
    |--------------------------------------------------------------------------
    |
    | El agente de WhatsApp consulta la BD de la tienda solo a través de estas
    | rutas de lectura. Se autentica con el header X-API-Key; sin api_key
    | configurada toda petición responde 401.
    |
    */

    'api_key' => env('CHATBOT_API_KEY'),

    // Peticiones por minuto por IP.
    'rate_limit' => (int) env('CHATBOT_RATE_LIMIT', 300),

    // Máximo de elementos en los parámetros array (ids, palabras, subcats_grupo).
    'max_items' => (int) env('CHATBOT_MAX_ITEMS', 50),

];
