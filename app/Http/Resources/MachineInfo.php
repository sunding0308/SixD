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
            'hotel_address' => optional($this->installation)->hotel_address,
            'machine_name' => optional($this->installation)->machine_name,
            'machine_model' => optional($this->installation)->machine_model,
            'installation_date' => optional($this->installation)->installation_date,
            'production_date' => optional($this->installation)->production_date,
            'qr_code' => optional($this->installation)->qr_code
        ];
    }

    public function with($request){
        return [
          'status'=>'success'
        ];
    }
}
