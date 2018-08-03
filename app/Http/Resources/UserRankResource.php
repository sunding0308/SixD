<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRankResource extends JsonResource
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
            'machine_id' => $this->machine->machine_id,
            'user_id' => $this->user_id,
            'user_nickname' => $this->user_nickname,
            'rank' => $this->rank,
            'machine_rank' => $this->machine_rank,
            'city' => getProvince($this->machine->installation->hotel_address),
        ];
    }

    /**
     * 返回应该和资源一起返回的其他数据数组。
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 'success',
        ];
    }
}
