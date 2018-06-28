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
            'data' => [
                'id' => $this->id,
                'machine_id' => $this->machine_id,
                'user_id' => $this->user_id,
                'user_nickname' => $this->user_nickname,
                'rank' => $this->rank,
                'machine_rank' => $this->machine_rank,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp
            ],
            'status' => 'success'
        ];
    }
}
