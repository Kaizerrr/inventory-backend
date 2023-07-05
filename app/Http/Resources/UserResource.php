<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->username,
            'email' => $this->email,
            'last_login_at' => $this->last_login_at,
            'logs' => LogResource::collection($this->latestLogs),
        ];
    }
}
