<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'student_id' => $this->student->id ?? "DELETED",
            'student_name' => $this->student->name ?? "DELETED",
            'description' => $this->description,
            'endpoint' => $this->endpoint,
            'action' => $this->action,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
