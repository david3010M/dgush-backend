<?php
namespace App\Http\Requests\Product;

use App\Http\Requests\UpdateRequest;

class ConsultaStockRequest extends UpdateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'color_id'   => 'required|exists:color,server_id',
            'size_id'    => 'required|exists:size,server_id',
        ];
    }
    public function messages()
    {
        return [

            'color_id.required'   => 'El campo color_id es obligatorio.',
            'color_id.exists'     => 'El color especificado no existe.',

            'size_id.required'    => 'El campo size_id es obligatorio.',
            'size_id.exists'      => 'La talla especificada no existe.',
        ];
    }

}
