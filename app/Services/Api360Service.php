<?php
namespace App\Services;

use App\Jobs\FetchCategoryJob;
use App\Jobs\FetchColorJob;
use App\Jobs\FetchProductJob;
use App\Jobs\FetchSizeJob;
use App\Jobs\FetchSubcategoryJob;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Api360Service
{
    public function sincronizarDatos360()
    {
        Bus::batch([
            new FetchCategoryJob(),
            new FetchSubcategoryJob(),
            new FetchColorJob(),
            new FetchSizeJob(),
            new FetchProductJob(),
        ])
            ->name('Sincronización de Datos 360')
            ->onQueue('default')
            ->dispatch();
        Log::info("Sincronización 360 iniciada con éxito.");
    }

    public function fetch_category(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'categories',
            'categories',
            Category::class,
            Category::getfields360,
            $uuid,
            []// Relación dinámica
        );
    }
    public function fetch_subcategory(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'categories',
            'categories',
            Subcategory::class,
            Subcategory::getfields360,
            $uuid,
            ['category_id' => Category::class]// Relación dinámica
        );
    }
    public function fetch_color(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'colors',
            'colors',
            Color::class,
            Color::getfields360,
            $uuid,
            []// Relación dinámica
        );
    }
    public function fetch_size(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'sizes',
            'sizes',
            Size::class,
            Size::getfields360,
            $uuid,
            []// Relación dinámica
        );
    }

    public function fetch_product(?string $uuid = '')
    {
        return $this->fetchDataAndSyncProducts(
            'products',
            'products',
            Product::class,
            Product::getfields360,
            $uuid,
            ['category_id' => Subcategory::class]// Relación dinámica
        );
    }
    //FUNCIÓN DINAMICA
    public function fetchDataAndSync(
        string $endpoint,          // Nombre de la ruta a solicitar
        string $dataKey,           // Nombre de la key para obtener la data
        string $modelClass,        // Nombre del modelo
        array $fields,             // Campos del modelo a sincronizar
        string $authorizationUiid, // Token de autorización
        array $relations = []      // Relaciones externas (campo => modelo)
    ) {
        try {
            $endpoint          = "https://sistema.360sys.com.pe/api/online-store/" . $endpoint;
            $authorizationUiid = ! empty($authorizationUiid) ? $authorizationUiid : '4807a98f-2a48-4c54-bd5a-0d330b202045';

            $response = Http::withHeaders(['Authorization' => $authorizationUiid])->get($endpoint);

            if ($response->successful()) {
                $data  = $response->json();
                $items = $data['data'][$dataKey] ?? [];

                if (isset($items['id'])) {
                    $items = [$items];
                }

                if (! empty($items)) {
                    foreach ($items as $item) {

                        $processedFields = [];
                        foreach ($fields as $dbField => $apiField) {
                            $processedFields[$dbField] = $item[$apiField] ?? null;
                        }

                        foreach ($relations as $field => $relatedModel) {

                            if (isset($item[$field])) {
                                $relatedInstance         = $relatedModel::where('server_id', $item[$field])->first();
                                $processedFields[$field] = $relatedInstance?->id ?? null;
                            }
                        }
                        $modelClass::updateOrCreate(
                            ['server_id' => $item['id']],
                            array_merge($processedFields, ['server_id' => $item['id']])
                        );

                    }

                    return [
                        'status'  => true,
                        'message' => "Datos sincronizados correctamente para el modelo {$modelClass}.",
                        'data'    => $data['data'],
                    ];
                }

                return [
                    'status'  => false,
                    'message' => "No se encontraron datos en la clave '{$dataKey}'.",
                    'data'    => [],
                ];
            }

            return [
                'status'  => false,
                'message' => 'La solicitud a la API no fue exitosa.',
                'data'    => [],
            ];
        } catch (\Exception $e) {
            return [
                'status'  => false,
                'message' => 'Error interno: ' . $e->getMessage(),
            ];
        }
    }

    //PERSONALIZADO A PRODUCTOS PARA LLEVAR MISMA ESTRUCTURA
    public function fetchDataAndSyncProducts(
        string $endpoint,
        string $dataKey,
        string $modelClass,
        array $fields,
        string $authorizationUiid,
        array $relations = []
    ) {
        try {
            $endpoint          = "https://sistema.360sys.com.pe/api/online-store/" . $endpoint;
            $authorizationUiid = ! empty($authorizationUiid) ? $authorizationUiid : '4807a98f-2a48-4c54-bd5a-0d330b202045';

            $response = Http::withHeaders(['Authorization' => $authorizationUiid])->get($endpoint);

            if ($response->successful()) {
                $data  = $response->json();
                $items = $data['data'][$dataKey] ?? [];

                if (isset($items['id'])) {
                    $items = [$items];
                }

                if (! empty($items)) {
                    foreach ($items as $item) {
                        $processedFields = [];
                        foreach ($fields as $dbField => $apiField) {
                            $processedFields[$dbField] = $item[$apiField] ?? null;
                        }

                        foreach ($relations as $field => $relatedModel) {
                       
                            if (isset($item[$field])) {
                                $relatedInstance         = $relatedModel::where('server_id', $item[$field])->first();
                                $processedFields['subcategory_id'] = $relatedInstance?->id ?? null;
                            }
                        }

                        $product = $modelClass::updateOrCreate(
                            ['server_id' => $item['id']],
                            array_merge($processedFields, ['server_id' => $item['id']])
                        );

                        if (isset($item['colors'])) {
                            foreach ($item['colors'] as $color) {
                                $colorInstance = Color::where('server_id', $color['id'])->first();

                                if ($colorInstance && isset($color['sizes'])) {
                                    foreach ($color['sizes'] as $size) {
                                        $sizeInstance = Size::where('server_id', $size['id'])->first();

                                        if ($sizeInstance) {
                                            ProductDetails::updateOrCreate(
                                                [
                                                    'product_id' => $product->id,
                                                    'color_id'   => $colorInstance->id,
                                                    'size_id'    => $sizeInstance->id,
                                                ],
                                                ['stock' => $size['stock']]
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    return [
                        'status'  => true,
                        'message' => "Datos sincronizados correctamente para el modelo {$modelClass}.",
                        'data'    => $data['data'],
                    ];
                }

                return [
                    'status'  => false,
                    'message' => "No se encontraron datos en la clave '{$dataKey}'.",
                    'data'    => [],
                ];
            }

            return [
                'status'  => false,
                'message' => 'La solicitud a la API no fue exitosa.',
                'data'    => [],
            ];
        } catch (\Exception $e) {
            return [
                'status'  => false,
                'message' => 'Error interno: ' . $e->getMessage(),
            ];
        }
    }

}
