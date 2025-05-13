<?php
namespace App\Http\Requests\Order;

use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class UpdateOrderRequest extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules()
    {
        return [
            // 'orders' debe ser un arreglo
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:order,server_id',  // Aseguramos que cada 'id' exista en la base de datos

            // Los campos de cada orden
            'orders.*.number'           => 'nullable',
            'orders.*.date'             => 'nullable',
            'orders.*.scheduled_date'   => 'nullable',
            'orders.*.payment_date'     => 'nullable',
            'orders.*.shipping_date'    => 'nullable',
            'orders.*.end_date'         => 'nullable',
            'orders.*.stage'            => 'nullable',
            'orders.*.status'           => 'nullable',
            'orders.*.mode'             => 'nullable',
            'orders.*.cellphone_number' => 'nullable',
            'orders.*.email_address'    => 'nullable',
            'orders.*.address'          => 'nullable',
            'orders.*.destiny'          => 'nullable',
            'orders.*.zone_id'          => 'nullable',
            'orders.*.district_id'      => 'nullable',
            'orders.*.branch_id'        => 'nullable',
            'orders.*.notes'            => 'nullable',
            'orders.*.total'            => 'nullable',
            'orders.*.currency'         => 'nullable',
            'orders.*.shipping_cost'    => 'nullable',
            'orders.*.invoices'           => 'nullable',

            // Campos relacionados con el cliente para cada orden
            'orders.*.customer'         => 'nullable',
            'orders.*.payments'         => 'nullable',
            'orders.*.products'         => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'orders.required'           => 'El campo orders es obligatorio.',
            'orders.array'              => 'El campo orders debe ser un arreglo.',
            'orders.*.id.required' => 'El ID de la orden es obligatorio.',
            'orders.*.id.exists'   => 'La orden con el ID especificado no existe en la base de datos.',
            'orders.*.number.string'     => 'El campo "number" debe ser una cadena de texto.',
            'orders.*.date.date'         => 'El campo "date" debe ser una fecha válida.',
            'orders.*.scheduled_date.date' => 'El campo "scheduled_date" debe ser una fecha válida.',
            'orders.*.payment_date.date' => 'El campo "payment_date" debe ser una fecha válida.',
            'orders.*.shipping_date.date' => 'El campo "shipping_date" debe ser una fecha válida.',
            'orders.*.end_date.date'     => 'El campo "end_date" debe ser una fecha válida.',
            'orders.*.stage.string'      => 'El campo "stage" debe ser una cadena de texto.',
            'orders.*.status.string'     => 'El campo "status" debe ser una cadena de texto.',
            'orders.*.mode.string'       => 'El campo "mode" debe ser una cadena de texto.',
            'orders.*.cellphone_number.string' => 'El campo "cellphone_number" debe ser una cadena de texto.',
            'orders.*.email_address.string' => 'El campo "email_address" debe ser una cadena de texto.',
            'orders.*.address.string'    => 'El campo "address" debe ser una cadena de texto.',
            'orders.*.destiny.string'    => 'El campo "destiny" debe ser una cadena de texto.',
            'orders.*.zone_id.integer'   => 'El campo "zone_id" debe ser un número entero.',
            'orders.*.district_id.integer' => 'El campo "district_id" debe ser un número entero.',
            'orders.*.branch_id.integer' => 'El campo "branch_id" debe ser un número entero.',
            'orders.*.notes.string'      => 'El campo "notes" debe ser una cadena de texto.',
            'orders.*.total.numeric'     => 'El campo "total" debe ser un valor numérico.',
            'orders.*.currency.string'   => 'El campo "currency" debe ser una cadena de texto.',
            'orders.*.shipping_cost.numeric' => 'El campo "shipping_cost" debe ser un valor numérico.',
            'orders.*.customer.string'   => 'El campo "customer" debe ser una cadena de texto.',
            'orders.*.payments'          => 'El campo "payments" debe ser válido.',  // No estamos especificando el tipo exacto, pero estamos validando que el valor esté presente
            'orders.*.products'          => 'El campo "products" debe ser válido.',  // Lo mismo con "products"
        ];
    }

}
