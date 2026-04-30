<?php
namespace App\Services;

use App\Jobs\FetchCategoryJob;
use App\Jobs\FetchColorJob;
use App\Jobs\FetchDepartmentJob;
use App\Jobs\FetchDistrictJob;
use App\Jobs\FetchProductJob;
use App\Jobs\FetchProvincesJob;
use App\Jobs\FetchSedeJob;
use App\Jobs\FetchSizeJob;
use App\Jobs\FetchSubcategoryJob;
use App\Jobs\FetchZoneJob;
use App\Models\Category;
use App\Models\Color;
use App\Models\Department;
use App\Models\District;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Province;
use App\Models\Sede;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\SizeGuide;
use App\Models\Zone;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Api360Service
{

    public function sincronizarDatos360($uuid)
    {
        try {
            Bus::batch([
                new FetchCategoryJob($uuid),
                new FetchSubcategoryJob($uuid),
                new FetchColorJob($uuid),
                new FetchSizeJob($uuid),
                new FetchProductJob($uuid),
                new FetchSedeJob($uuid), //revisar
                new FetchZoneJob($uuid),

                new FetchDepartmentJob($uuid),
                new FetchProvincesJob($uuid),
                new FetchDistrictJob($uuid),

            ])
                ->name('Sincronización de Datos 360')
                ->onConnection('sync') // 🔹 Forzar ejecución inmediata
                ->dispatch();

            Log::info("Sincronización 360 iniciada con éxito.");
        } catch (\Throwable $e) {
            Log::error('Error en sincronización de datos 360', [
                'message' => $e->getMessage(),
                'uuid' => $uuid,
            ]);
        }
    }

    public function fetch_zones(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'zones',
            'zones',
            Zone::class,
            Zone::getfields360,
            $uuid,
            []// Relación dinámica
        );
    }

    public function fetch_departments(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'districts',
            'departments',
            Department::class,
            Department::getfields360,
            $uuid,
            []// Relación dinámica
        );
    }

    public function fetch_provinces(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'districts',
            'provinces',
            Province::class,
            Province::getfields360,
            $uuid,
            ['department_id' => Department::class]// Relación dinámica
        );
    }
    public function fetch_districts(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'districts',
            'districts',
            District::class,
            District::getfields360,
            $uuid,
            ['province_id' => Province::class]// Relación dinámica
        );
    }
    public function fetch_sedes(?string $uuid = '')
    {
        return $this->fetchDataAndSync(
            'branches',
            'branches',
            Sede::class,
            Sede::getfields360,
            $uuid,
            ['district_id' => District::class]// Relación dinámica
        );
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
            $endpoint = "https://sistema.360sys.com.pe/api/online-store/" . $endpoint;
            $authorizationUiid = !empty($authorizationUiid) ? $authorizationUiid : env('APP_UUID');

            $response = Http::withHeaders(['Authorization' => $authorizationUiid])->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();

                $items = $data['data'][$dataKey] ?? [];

                if (isset($items['id'])) {
                    $items = [$items];
                }

                if (!empty($items)) {
                    foreach ($items as $item) {

                        $processedFields = [];
                        foreach ($fields as $dbField => $apiField) {
                            $processedFields[$dbField] = $item[$apiField] ?? null;
                        }

                        foreach ($relations as $field => $relatedModel) {

                            if (isset($item[$field])) {
                                $relatedInstance = $relatedModel::where('server_id', $item[$field])->first();
                                $processedFields[$field] = $relatedInstance?->id ?? null;
                            }
                        }
                        $modelClass::updateOrCreate(
                            ['server_id' => $item['id']],
                            array_merge($processedFields, ['server_id' => $item['id']])
                        );

                    }

                    return [
                        'status' => true,
                        'message' => "Datos sincronizados correctamente para el modelo {$modelClass}.",
                        'data' => $data['data'],
                    ];
                }

                return [
                    'status' => false,
                    'message' => "No se encontraron datos en la clave '{$dataKey}'.",
                    'data' => [],
                ];
            }

            return [
                'status' => false,
                'message' => 'La solicitud a la API no fue exitosa.',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
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
            $endpoint = "https://sistema.360sys.com.pe/api/online-store/" . $endpoint;
            $authorizationUiid = !empty($authorizationUiid) ? $authorizationUiid : env('APP_UUID');

            $response = Http::timeout(120) // segundos
                ->connectTimeout(30)
                ->withHeaders(['Authorization' => $authorizationUiid])
                ->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                $items = $data['data'][$dataKey] ?? [];

                if (isset($items['id'])) {
                    $items = [$items];
                }

                if (!empty($items)) {
                    foreach ($items as $item) {
                        $processedFields = [];
                        foreach ($fields as $dbField => $apiField) {
                            $processedFields[$dbField] = $item[$apiField] ?? null;
                        }

                        foreach ($relations as $field => $relatedModel) {

                            if (isset($item[$field])) {
                                $relatedInstance = $relatedModel::where('server_id', $item[$field])->first();
                                $processedFields['subcategory_id'] = $relatedInstance?->id ?? null;
                            }
                        }

                        // Procesar precios
                        $price1 = $price2 = $price12 = 0;
                        if (!empty($item['prices'])) {
                            foreach ($item['prices'] as $priceData) {
                                if ($priceData['quantity'] <= 2) {
                                    $price1 = $priceData['price'];
                                } elseif ($priceData['quantity'] < 12) {
                                    $price2 = $priceData['price'];
                                } elseif ($priceData['quantity'] >= 12) {
                                    $price12 = $priceData['price'];
                                }
                            }
                        }
                        $processedFields = array_merge($processedFields, [
                            'price1' => $price1,
                            'price2' => $price2,
                            'price12' => $price12,
                        ]);
                        $processedFields['price12'] = $price12;

                        $product = $modelClass::updateOrCreate(
                            ['server_id' => $item['id']],
                            array_merge($processedFields, ['server_id' => $item['id']])
                        );

                        // Procesar imágenes
                        foreach (['photo', 'photo2', 'photo3'] as $photoField) {
                            if (!empty($item[$photoField])) {
                                $imageName = basename($item[$photoField]);
                                Image::updateOrCreate(
                                    [
                                        'url' => $item[$photoField],
                                        'product_id' => $product->id,
                                    ],
                                    [
                                        'name' => $imageName,
                                    ]
                                );
                            }
                        }

                        // Procesar Guía de Tallas (Tarea 4)
                        if (!empty($item['size_guide'])) {
                            SizeGuide::updateOrCreate(
                                ['product_id' => $product->id],
                                [
                                    'name' => 'Guía de Tallas (360sys)',
                                    'route' => $item['size_guide']
                                ]
                            );
                        } else {
                            SizeGuide::where('product_id', $product->id)->delete();
                        }

                        if (array_key_exists('colors', $item)) {
                            $syncedProductDetailIds = [];

                            foreach ($item['colors'] as $color) {
                                $colorInstance = Color::where('server_id', $color['id'])->first();
                                if (!$colorInstance) {
                                    continue;
                                }

                                if ($colorInstance && isset($color['sizes'])) {
                                    $colorStatus = array_key_exists('status', $color)
                                        ? $this->normalizeApiStatus($color['status'])
                                        : $this->normalizeApiStatus($colorInstance->status ?? true);

                                    foreach ($color['sizes'] as $size) {
                                        $sizeInstance = Size::where('server_id', $size['id'])->first();

                                        if ($sizeInstance) {
                                            $sizeStatus = array_key_exists('status', $size)
                                                ? $this->normalizeApiStatus($size['status'])
                                                : $this->normalizeApiStatus($sizeInstance->status ?? true);

                                            $productDetail = ProductDetails::withTrashed()->updateOrCreate(
                                                [
                                                    'product_id' => $product->id,
                                                    'color_id' => $colorInstance->id,
                                                    'size_id' => $sizeInstance->id,
                                                ],
                                                [
                                                    'stock' => $size['stock'],
                                                    'status' => $colorStatus && $sizeStatus,
                                                ]
                                            );

                                            if ($productDetail->trashed()) {
                                                $productDetail->restore();
                                            }

                                            $syncedProductDetailIds[] = $productDetail->id;
                                        }
                                    }

                                }

                                if (!empty($color['images'])) {
                                    foreach ($color['images'] as $imageUrl) {
                                        $imageName = basename($imageUrl);
                                        Image::updateOrCreate(
                                            [
                                                'url' => $imageUrl,
                                                'product_id' => $product->id,
                                                'color_id' => $colorInstance->id,
                                            ],
                                            [
                                                'name' => $imageName,
                                            ]
                                        );
                                    }
                                }
                            }

                            $staleProductDetails = ProductDetails::where('product_id', $product->id);
                            if (!empty($syncedProductDetailIds)) {
                                $staleProductDetails->whereNotIn('id', $syncedProductDetailIds);
                            }
                            $staleProductDetails->delete();
                        }

                    }
                    return [
                        'status' => true,
                        'message' => "Datos sincronizados correctamente para el modelo {$modelClass}.",
                        'data' => $data['data'],
                    ];
                }

                return [
                    'status' => false,
                    'message' => "No se encontraron datos en la clave '{$dataKey}'.",
                    'data' => [],
                ];
            }

            return [
                'status' => false,
                'message' => 'La solicitud a la API no fue exitosa.',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error interno: ' . $e->getMessage(),
            ];
        }
    }

    public function updateStock(array $data)
    {
        $updatedDetails = [];

        foreach ($data['items'] as $item) {
            $product = Product::firstWhere('server_id', $item['product_id']);
            $color = Color::firstWhere('server_id', $item['color_id']);
            $size = Size::firstWhere('server_id', $item['size_id']);

            if ($product && $color && $size) {
                $values = ['stock' => $item['stock']];
                if (array_key_exists('status', $item)) {
                    $values['status'] = $this->normalizeApiStatus($item['status']);
                }

                $detail = ProductDetails::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'color_id' => $color->id,
                        'size_id' => $size->id,
                    ],
                    $values
                );
                $updatedDetails[] = $detail;
            }
        }

        return $updatedDetails;
    }

    public function update_stock_consultando_360(array $data, $authorizationUuid = '')
    {
        // Consultar el stock desde la API externa

        if (empty($authorizationUuid)) {
            $authorizationUuid = env('APP_UUID');
        }

        $endpoint = 'https://sistema.360sys.com.pe/api/online-store/products/' . $data['product_id'] . '/stock';

        $response = Http::withHeaders([
            'Authorization' => $authorizationUuid,
        ])->get($endpoint, [
            'color_id' => $data['color_id'],
            'size_id' => $data['size_id'],
        ]);

        // Verificar que la respuesta sea exitosa
        if ($response->successful()) {
            // Obtener el stock desde la respuesta
            $apiStock = $response->json()['stock']; // Suponiendo que el campo de stock es 'stock'

            // Obtener los detalles del producto, color y tamaño
            $product = Product::firstWhere('server_id', $data['product_id']);
            $color = Color::firstWhere('server_id', $data['color_id']);
            $size = Size::firstWhere('server_id', $data['size_id']);
            // Actualizar o crear el registro de ProductDetails
            return ProductDetails::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'color_id' => $color->id,
                    'size_id' => $size->id,
                ],
                ['stock' => $apiStock]// Usamos el stock consultado de la API
            );
        }

        // Si la API no responde correctamente
        return response()->json(['error' => 'Error al consultar el stock desde la API'], 500);
    }

    public function find_by_server_id(string $modelClass, $id)
    {
        $data = $modelClass::where('server_id', $id)->first();
        return $data
            ? ['status' => true, 'data' => $data]
            : ['status' => false, 'data' => [], 'model' => $modelClass, 'message' => "No se encontró server_id: {$id} en {$modelClass}"];
    }

    private function normalizeApiStatus($status): bool
    {
        if ($status === null) {
            return true;
        }

        return filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool) $status;
    }
}
