<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MachineInfo extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'city' => getProvince(optional($this->installation)->hotel_address),
            'qr_code' => optional($this->installation)->qr_code
        ];
    }

    public function with($request){
        return [
          'status'=>'success'
        ];
    }
}
