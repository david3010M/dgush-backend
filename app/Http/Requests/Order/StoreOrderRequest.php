<?php
namespace App\Http\Requests\Order;

use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }
/**
 * @OA\Schema(
 *     schema="360StoreOrderRequest",
 *     type="object",
 *     required={
 *         "amount", "description", "email", "token", "mode", "cellphone", "email_address",
 *         "address", "customer_dni", "customer_first_name", "customer_last_name",
 *         "payment_method", "products"
 *     },
 * 
 *     @OA\Property(property="amount", type="number", format="float", minimum=600, description="Monto total del cobro (mínimo 600)"),
 *     @OA\Property(property="description", type="string", maxLength=255, description="Descripción del cobro"),
 *     @OA\Property(property="email", type="string", format="email", description="Correo electrónico del cliente para la transacción"),
 *     @OA\Property(property="token", type="string", description="Token generado por Culqi"),

 *     @OA\Property(property="mode", type="string", enum={"RECOJO", "DELIVERY", "ENVIO"}, description="Modo del pedido"),
 *     @OA\Property(property="scheduled_date", type="string", format="date", nullable=true, description="Fecha programada (opcional)"),
 *     @OA\Property(property="cellphone", type="string", description="Número de celular del cliente"),
 *     @OA\Property(property="email_address", type="string", format="email", description="Correo electrónico de contacto"),
 *     @OA\Property(property="address", type="string", description="Dirección de entrega"),

 *     @OA\Property(property="zone_id", type="integer", nullable=true, description="ID de zona (requerido si mode=DELIVERY)"),
 *     @OA\Property(property="district_id", type="integer", nullable=true, description="ID de distrito (requerido si mode=ENVIO)"),
 *     @OA\Property(property="branch_id", type="integer", nullable=true, description="ID de sede (requerido si mode=RECOJO)"),

 *     @OA\Property(property="customer_dni", type="string", maxLength=8, description="DNI del cliente (máx. 8 caracteres)"),
 *     @OA\Property(property="customer_first_name", type="string", description="Nombres del cliente"),
 *     @OA\Property(property="customer_last_name", type="string", description="Apellidos del cliente"),
 *     @OA\Property(property="notes", type="string", nullable=true, description="Notas adicionales del pedido"),

 *     @OA\Property(property="payment_method", type="string", enum={"TARJETA", "BILLETERA DIGITAL"}, description="Método de pago"),
 *     @OA\Property(property="payment_card_name", type="string", enum={"VISA", "MASTERCARD", "AMERICAN EXPRESS", "DINERS CLUB INTERNATIONAL"}, nullable=true, description="Marca de tarjeta (si es pago con tarjeta)"),
 *     @OA\Property(property="payment_card_type", type="string", enum={"CREDITO", "DEBITO"}, nullable=true, description="Tipo de tarjeta (si es pago con tarjeta)"),
 *     @OA\Property(property="payment_digitalwallet", type="string", enum={"YAPE"}, nullable=true, description="Nombre de la billetera digital (si aplica)"),

 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         minItems=1,
 *         description="Lista de productos del pedido",
 *         @OA\Items(
 *             type="object",
 *             required={"id", "quantity", "price"},
 *             @OA\Property(property="id", type="integer", description="ID del producto"),
 *             @OA\Property(property="color_id", type="integer", nullable=true, description="ID del color del producto (opcional)"),
 *             @OA\Property(property="size_id", type="integer", nullable=true, description="ID de la talla del producto (opcional)"),
 *             @OA\Property(property="quantity", type="integer", minimum=1, description="Cantidad solicitada del producto"),
 *             @OA\Property(property="price", type="number", format="float", minimum=0, description="Precio unitario del producto"),
 *             @OA\Property(property="notes", type="string", nullable=true, description="Notas adicionales para el producto")
 *         )
 *     )
 * )
 */


    public function rules()
    {
        return [
                                                                           // Validaciones para el cargo con Culqi
            'amount'                => ['required', 'numeric', 'min:600'], // Monto debe ser numérico y mayor que 0
            'description'           => ['required', 'string', 'max:255'],  // Descripción es obligatoria y con un límite de caracteres
            'email'                 => ['required', 'email'],              // Email debe ser válido
            'token'                 => ['required', 'string'],             // Token no debe estar vacío

            //Validaciones para el POST pedido 360
            'mode'                  => ['required', 'in:RECOJO,DELIVERY,ENVIO'],
            'scheduled_date'        => ['nullable', 'date'],
            'cellphone'             => ['required'],
            'email_address'         => ['required', 'email'],
            'address'               => ['required'],

            'zone_id'               => [
                'required_if:mode,DELIVERY',
                'nullable',
                'integer',
                Rule::exists('zones', 'server_id'),
            ],
            'district_id'           => [
                'required_if:mode,ENVIO',
                'nullable',
                'integer',
                Rule::exists('district', 'server_id'),
            ],
            'branch_id'             => [
                'required_if:mode,RECOJO',
                'nullable',
                'integer',
                Rule::exists('sedes', 'server_id'),
            ],

            'customer_dni'          => ['required', 'max:8'],
            'customer_first_name'   => ['required', 'string'],
            'customer_last_name'    => ['required', 'string'],

            'notes'                 => ['nullable', 'string'],

            // Payment
            'payment_method'        => ['required', 'in:TARJETA,BILLETERA DIGITAL'],

            'payment_card_name'     => ['required_if:payment_method,TARJETA', 'in:VISA,MASTERCARD,AMERICAN EXPRESS,DINERS CLUB INTERNATIONAL'],
            'payment_card_type'     => ['required_if:payment_method,TARJETA', 'in:CREDITO,DEBITO'],
            'payment_digitalwallet' => ['nullable', 'in:YAPE'],

            //verificar el requerido para ambos ENVIO(distrito), Delivery(zona)
            // 'shipping_cost'         => ['nullable','required_if:mode,ENVIO,DELIVERY', 'numeric', 'min:0'],

            // Products
            'products'              => ['required', 'array', 'min:1'],
            'products.*.id'         => ['required', 'integer', Rule::exists('products', 'server_id')],
            'products.*.color_id'   => ['nullable', 'integer', Rule::exists('colors', 'server_id')],
            'products.*.size_id'    => ['nullable', 'integer', Rule::exists('sizes', 'server_id')],
            'products.*.quantity'   => ['required', 'integer', 'min:1'],
            'products.*.price'      => ['required', 'numeric', 'min:0'],
            'products.*.notes'      => ['nullable', 'string'],

        ];
    }

    public function messages()
    {
        return [
            // Culqi
            'amount.required'               => 'El monto es obligatorio.',
            'amount.numeric'                => 'El monto debe ser un valor numérico.',
            'amount.min'                    => 'El monto debe ser como mínimo de S/. 600.',
            'description.required'          => 'La descripción es obligatoria.',
            'description.max'               => 'La descripción no debe superar los 255 caracteres.',
            'email.required'                => 'El correo electrónico es obligatorio.',
            'email.email'                   => 'El correo electrónico no tiene un formato válido.',
            'token.required'                => 'El token de pago es obligatorio.',

            // Pedido 360
            'mode.required'                 => 'El modo de entrega es obligatorio.',
            'mode.in'                       => 'El modo de entrega debe ser RECOJO, DELIVERY o ENVIO.',
            'scheduled_date.date'           => 'La fecha programada no tiene un formato válido.',
            'cellphone.required'            => 'El celular es obligatorio.',
            'email_address.required'        => 'El correo de contacto es obligatorio.',
            'email_address.email'           => 'El correo de contacto no es válido.',
            'address.required'              => 'La dirección es obligatoria.',

            'zone_id.required_if'           => 'La zona es obligatoria para el modo DELIVERY.',
            'zone_id.integer'               => 'La zona debe ser un valor numérico.',
            'zone_id.exists'                => 'La zona seleccionada no es válida.',
            'district_id.required_if'       => 'El distrito es obligatorio para el modo ENVIO.',
            'district_id.integer'           => 'El distrito debe ser un valor numérico.',
            'district_id.exists'            => 'El distrito seleccionado no es válido.',
            'branch_id.required_if'         => 'La sede es obligatoria para el modo RECOJO.',
            'branch_id.integer'             => 'La sede debe ser un valor numérico.',
            'branch_id.exists'              => 'La sede seleccionada no es válida.',

            'customer_dni.required'         => 'El DNI del cliente es obligatorio.',
            'customer_dni.max'              => 'El DNI del cliente no debe tener más de 8 caracteres.',
            'customer_first_name.required'  => 'El nombre del cliente es obligatorio.',
            'customer_first_name.string'    => 'El nombre del cliente debe ser texto.',
            'customer_last_name.required'   => 'El apellido del cliente es obligatorio.',
            'customer_last_name.string'     => 'El apellido del cliente debe ser texto.',

            'notes.string'                  => 'Las notas del pedido deben ser texto.',

            // Payment
            'payment_method.required'       => 'El método de pago es obligatorio.',
            'payment_method.in'             => 'El método de pago debe ser TARJETA o BILLETERA DIGITAL.',
            'payment_card_name.required_if' => 'La marca de tarjeta es obligatoria si el método de pago es TARJETA.',
            'payment_card_name.in'          => 'La marca de tarjeta debe ser VISA, MASTERCARD, AMERICAN EXPRESS o DINERS CLUB INTERNATIONAL.',
            'payment_card_type.required_if' => 'El tipo de tarjeta es obligatorio si el método de pago es TARJETA.',
            'payment_card_type.in'          => 'El tipo de tarjeta debe ser CREDITO o DEBITO.',
            'payment_digitalwallet.in'      => 'La billetera digital debe ser YAPE.',

            // Products
            'products.required'             => 'Debe registrar al menos un producto.',
            'products.array'                => 'El formato de productos no es válido.',
            'products.min'                  => 'Debe registrar al menos un producto.',
            'products.*.id.required'        => 'El ID del producto es obligatorio.',
            'products.*.id.integer'         => 'El ID del producto debe ser un número.',
            'products.*.id.exists'          => 'El producto no existe.',
            'products.*.color_id.integer'   => 'El ID del color debe ser un número.',
            'products.*.color_id.exists'    => 'El color seleccionado no es válido.',
            'products.*.size_id.integer'    => 'El ID de la talla debe ser un número.',
            'products.*.size_id.exists'     => 'La talla seleccionada no es válida.',
            'products.*.quantity.required'  => 'La cantidad es obligatoria.',
            'products.*.quantity.integer'   => 'La cantidad debe ser un número entero.',
            'products.*.quantity.min'       => 'La cantidad debe ser al menos 1.',
            'products.*.price.required'     => 'El precio es obligatorio.',
            'products.*.price.numeric'      => 'El precio debe ser un valor numérico.',
            'products.*.price.min'          => 'El precio debe ser mayor o igual a 0.',
            'products.*.notes.string'       => 'Las notas del producto deben ser texto.',
        ];
    }

    public function prepareForValidation()
    {
        $data = $this->all();

        if (! isset($data['products']) || ! is_array($data['products'])) {
            return;
        }

        $validator = Validator::make($data, $this->rules());

        foreach ($data['products'] as $index => $item) {
            $detail = $this->api360Service->update_stock_consultando_360([
                'product_id' => $item['id'] ?? null,
                'color_id'   => $item['color_id'] ?? null,
                'size_id'    => $item['size_id'] ?? null,
            ], env('APP_UUID'));

            if (isset($detail->stock) && $detail->stock < ($item['quantity'] ?? 0)) {
                $validator->errors()->add("products.$index.quantity", 'No hay suficiente stock para el producto seleccionado.');
            }
        }

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

}
