<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryManDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $media = getSingleMedia($this, 'delivery_man_document', null);

        return [
            'id' => $this->id,
            'delivery_man_id' => $this->delivery_man_id,
            'document_id' => $this->document_id,
            'is_verified' => $this->is_verified,
            'document' => $this->whenLoaded('document', function () {
                return [
                    'id' => $this->document->id,
                    'name' => $this->document->name,
                    'status' => $this->document->status,
                    'is_required' => $this->document->is_required,
                ];
            }),
            'delivery_man' => $this->whenLoaded('delivery_man', function () {
                return [
                    'id' => $this->delivery_man->id,
                    'name' => $this->delivery_man->name,
                    'email' => $this->delivery_man->email,
                ];
            }),
            'document_name' => optional($this->document)->name,
            'delivery_man_name' => optional($this->delivery_man)->name,
            'document_file' => $media,
            'delivery_man_document' => $media, // For backward compatibility
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
