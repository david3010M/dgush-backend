<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $percentageDiscount = 0;
        if ($this->liquidacion == true) {
            $percentageDiscount = ($this->price1 - $this->priceLiquidacion) * 100 / $this->price1;
        } else if ($this->status == 'onsale') {
            $percentageDiscount = ($this->price1 - $this->priceOferta) * 100 / $this->price1;
        }
        $percentageDiscount = round($percentageDiscount);
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'description'        => $this->description,
            'detailweb'          => $this->detailweb,
            'price1'             => $this->price1,
            'price2'             => $this->price2,
            'price12'            => $this->price12,
            'priceOferta'        => $this->priceOferta,
            'priceLiquidacion'   => $this->priceLiquidacion,
            'percentageDiscount' => $percentageDiscount != 0 ? $percentageDiscount . '%' : null,
            'score'              => $this->score,
            'status'             => $this->status,
            'liquidacion'        => $this->liquidacion,
            'subcategory_id'     => $this->subcategory_id,
            'image'              => $this->image ? (new ImageResource($this->image))->url : url('images/placeholder.svg'),
            'status_server'      => $this->status_server,
            'currency'           => $this->currency,
            'server_id'          => $this->sever_id,
        ];
    }
}
