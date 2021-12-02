<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource {

    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'due_date'    => $this->due_date,
            'created_at'  => $this->created_at,
            'category'    => new CategoryResource($this->whenLoaded('category'))
        ];
    }
}
