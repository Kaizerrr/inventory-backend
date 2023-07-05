<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'studentId' => $this->studentId,
            'fileNumber' => $this->fileNumber,
            'program' => $this->program,
            'storageLocation' => $this->storageLocation,
            'studentClass' => $this->studentClass,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
