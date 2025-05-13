<?php
namespace App\Http\Requests\Order;

use App\Http\Requests\StoreRequest;
use Illuminate\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="ListOrders360Request",
 *     required={"start", "end"},
 *     @OA\Property(property="start", type="string", format="date", example="2025-05-01"),
 *     @OA\Property(property="end", type="string", format="date", example="2025-05-31")
 * )
 */
class ListOrders360Request extends StoreRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:start',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $start = strtotime($this->input('start'));
            $end   = strtotime($this->input('end'));

            if ($start && $end) {
                $maxDays = 31 * 24 * 60 * 60; // 1 mes en segundos
                if (($end - $start) > $maxDays) {
                    $validator->errors()->add('end', 'El rango de fechas no debe superar un mes.');
                }
            }
        });
    }

}
