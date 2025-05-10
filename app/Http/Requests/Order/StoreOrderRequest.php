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
 *         "amount", "description", "email", "token", "mode", "scheduled_date", "cellphone", "email_address",
 *         "address", "customer_dni", "customer_first_name", "customer_last_name", "notes", "total",
 *         "payment_method", "shipping_cost", "products"
 *     },
 *     @OA\Property(property="amount", type="number", format="float", minimum=0.01, description="Charge amount"),
 *     @OA\Property(property="description", type="string", maxLength=255, description="Charge description"),
 *     @OA\Property(property="email", type="string", format="email", description="Email for charge"),
 *     @OA\Property(property="token", type="string", description="Payment token from Culqi"),

 *     @OA\Property(property="mode", type="string", enum={"RECOJO", "DELIVERY", "ENVIO"}, description="Order mode"),
 *     @OA\Property(property="scheduled_date", type="string", format="date", description="Scheduled delivery date"),
 *     @OA\Property(property="cellphone", type="string", pattern="^\d{9}$", description="Customer cellphone"),
 *     @OA\Property(property="email_address", type="string", format="email", description="Customer email"),
 *     @OA\Property(property="address", type="string", description="Delivery address"),
 *     @OA\Property(property="zone_id", type="integer", nullable=true, description="Zone ID for DELIVERY mode"),
 *     @OA\Property(property="district_id", type="integer", nullable=true, description="District ID for ENVIO mode"),
 *     @OA\Property(property="branch_id", type="integer", nullable=true, description="Branch ID for RECOJO mode"),

 *     @OA\Property(property="customer_dni", type="string", description="Customer DNI (8-12 digits)"),
 *     @OA\Property(property="customer_first_name", type="string", description="Customer first name"),
 *     @OA\Property(property="customer_last_name", type="string", description="Customer last name"),
 *     @OA\Property(property="notes", type="string", description="Order notes"),
 *     @OA\Property(property="total", type="number", format="float", minimum=0, description="Order total"),

 *     @OA\Property(property="payment_method", type="string", enum={"TARJETA", "BILLETERA DIGITAL"}, description="Payment method"),
 *     @OA\Property(property="payment_pos", type="string", enum={"IZIPAY", "NIUBIZ", "CULQI"}, nullable=true, description="POS system"),
 *     @OA\Property(property="payment_card_name", type="string", enum={"VISA", "MASTERCARD", "AMERICAN EXPRESS", "DINERS CLUB INTERNATIONAL"}, nullable=true, description="Card brand"),
 *     @OA\Property(property="payment_card_type", type="string", enum={"CREDITO", "DEBITO"}, nullable=true, description="Card type"),
 *     @OA\Property(property="payment_digitalwallet", type="string", enum={"YAPE"}, nullable=true, description="Digital wallet"),

 *     @OA\Property(property="shipping_cost", type="number", format="float", minimum=0, description="Shipping cost"),

 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         description="List of products",
 *         @OA\Items(
 *             type="object",
 *             required={"id", "quantity", "price"},
 *             @OA\Property(property="id", type="integer", description="Product server ID"),
 *             @OA\Property(property="color_id", type="integer", nullable=true, description="Color server ID"),
 *             @OA\Property(property="size_id", type="integer", nullable=true, description="Size server ID"),
 *             @OA\Property(property="quantity", type="integer", minimum=1, description="Quantity"),
 *             @OA\Property(property="price", type="number", format="float", minimum=0, description="Unit price"),
 *             @OA\Property(property="notes", type="string", nullable=true, description="Product notes")
 *         )
 *     )
 * )
 */

    public function rules()
    {
        return [
                                                                            // Validaciones para el cargo con Culqi
            'amount'                => ['required', 'numeric','min:600'], // Monto debe ser numérico y mayor que 0
            'description'           => ['required', 'string', 'max:255'],   // Descripción es obligatoria y con un límite de caracteres
            'email'                 => ['required', 'email'],               // Email debe ser válido
            'token'                 => ['required', 'string'],              // Token no debe estar vacío

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

            'customer_dni'          => ['required', 'digits_between:8,12'],
            'customer_first_name'   => ['required', 'string'],
            'customer_last_name'    => ['required', 'string'],

            'notes'                 => ['required', 'string'],
            'total'                 => ['required', 'numeric', 'min:0'],

            // Payment
            'payment_method'        => ['required', 'in:TARJETA,BILLETERA DIGITAL'],
            'payment_pos'           => ['nullable', 'in:IZIPAY,NIUBIZ,CULQI'],
            'payment_card_name'     => ['required_if:payment_method,TARJETA', 'in:VISA,MASTERCARD,AMERICAN EXPRESS,DINERS CLUB INTERNATIONAL'],
            'payment_card_type'     => ['required_if:payment_method,TARJETA', 'in:CREDITO,DEBITO'],
            'payment_digitalwallet' => ['nullable', 'in:YAPE'],

            'shipping_cost'         => ['required', 'numeric', 'min:0'],

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
            'amount.min'                    => 'El monto debe ser mayor a 0.',
            'description.required'          => 'La descripción es obligatoria.',
            'description.max'               => 'La descripción no debe superar los 255 caracteres.',
            'email.required'                => 'El correo electrónico es obligatorio.',
            'email.email'                   => 'El correo electrónico no tiene un formato válido.',
            'token.required'                => 'El token de pago es obligatorio.',

            // Pedido 360
            'mode.required'                 => 'El modo de entrega es obligatorio.',
            'mode.in'                       => 'El modo de entrega debe ser RECOJO, DELIVERY o ENVIO.',
            'scheduled_date.required'       => 'La fecha programada es obligatoria.',
            'scheduled_date.date'           => 'La fecha programada no tiene un formato válido.',
            'cellphone.required'            => 'El celular es obligatorio.',
            'cellphone.digits'              => 'El celular debe tener 9 dígitos.',
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
            'customer_dni.digits_between'   => 'El DNI debe tener entre 8 y 12 dígitos.',
            'customer_first_name.required'  => 'El nombre del cliente es obligatorio.',
            'customer_first_name.string'    => 'El nombre del cliente debe ser texto.',
            'customer_last_name.required'   => 'El apellido del cliente es obligatorio.',
            'customer_last_name.string'     => 'El apellido del cliente debe ser texto.',

            'notes.required'                => 'Las notas del pedido son obligatorias.',
            'notes.string'                  => 'Las notas deben ser texto.',
            'total.required'                => 'El total del pedido es obligatorio.',
            'total.numeric'                 => 'El total debe ser numérico.',
            'total.min'                     => 'El total no puede ser negativo.',

            'payment_method.required'       => 'El método de pago es obligatorio.',
            'payment_method.in'             => 'El método de pago no es válido.',
            'payment_pos.in'                => 'El POS seleccionado no es válido.',
            'payment_card_name.required_if' => 'La marca de la tarjeta es obligatoria.',
            'payment_card_name.in'          => 'La marca de la tarjeta no es válida.',
            'payment_card_type.required_if' => 'El tipo de tarjeta es obligatorio.',
            'payment_card_type.in'          => 'El tipo de tarjeta debe ser CRÉDITO o DÉBITO.',
            'payment_digitalwallet.in'      => 'La billetera digital no es válida.',

            'shipping_cost.required'        => 'El costo de envío es obligatorio.',
            'shipping_cost.numeric'         => 'El costo de envío debe ser numérico.',
            'shipping_cost.min'             => 'El costo de envío no puede ser negativo.',

            'products.required'             => 'Debe incluir al menos un producto.',
            'products.array'                => 'Los productos deben estar en formato de lista.',
            'products.min'                  => 'Debe incluir al menos un producto.',

            'products.*.id.required'        => 'El ID del producto es obligatorio.',
            'products.*.id.integer'         => 'El ID del producto debe ser numérico.',
            'products.*.id.exists'          => 'El producto seleccionado no existe.',
            'products.*.color_id.integer'   => 'El ID del color debe ser numérico.',
            'products.*.color_id.exists'    => 'El color seleccionado no existe.',
            'products.*.size_id.integer'    => 'El ID de la talla debe ser numérico.',
            'products.*.size_id.exists'     => 'La talla seleccionada no existe.',
            'products.*.quantity.required'  => 'La cantidad es obligatoria.',
            'products.*.quantity.integer'   => 'La cantidad debe ser numérica.',
            'products.*.quantity.min'       => 'La cantidad debe ser al menos 1.',
            'products.*.price.required'     => 'El precio es obligatorio.',
            'products.*.price.numeric'      => 'El precio debe ser numérico.',
            'products.*.price.min'          => 'El precio no puede ser negativo.',
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
