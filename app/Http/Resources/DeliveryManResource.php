<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryManResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $media = getSingleMedia($this->resource, 'profile_image', null);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'email' => $this->email,
            'username' => $this->username,
            'contact_number' => $this->contact_number,
            'user_type' => $this->user_type,
            'status' => $this->status,
            'status_label' => $this->status ? __('message.active') : __('message.inactive'),
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'country_id' => $this->country_id,
            'country' => $this->whenLoaded('country', function () {
                return [
                    'id' => $this->country->id,
                    'name' => $this->country->name,
                    'code' => $this->country->code,
                ];
            }),
            'city_id' => $this->city_id,
            'city' => $this->whenLoaded('city', function () {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->name,
                ];
            }),
            'profile_image' => $media,
            'is_verified_delivery_man' => (bool) $this->is_verified_delivery_man,
            'is_online' => (bool) $this->is_online,
            'is_available' => (bool) $this->is_available,
            'last_active_time' => $this->last_active_time,
            'uid' => $this->uid,
            'player_id' => $this->player_id,
            'fcm_token' => $this->fcm_token,
            'rating' => $this->rating,
            'is_verified_email' => $this->email_verified_at ? true : false,
            'is_verified_mobile' => $this->otp_verify_at ? true : false,
            'is_verified_document' => $this->document_verified_at ? true : false,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i:s') : null,
            'wallet' => $this->whenLoaded('userWallet', function () {
                return [
                    'id' => $this->userWallet->id ?? null,
                    'total_amount' => $this->userWallet->total_amount ?? 0,
                    'total_withdrawn' => $this->userWallet->total_withdrawn ?? 0,
                    'currency' => config('app.currency'),
                ];
            }),
            'bank_accounts' => $this->whenLoaded('userBankAccount', function () {
                return UserBankAccountResource::collection($this->userBankAccount);
            }),
            'documents' => $this->whenLoaded('deliveryManDocuments', function () {
                return DeliveryManDocumentResource::collection($this->deliveryManDocuments);
            }),
            'vehicle' => $this->whenLoaded('vehicle', function () {
                return [
                    'id' => $this->vehicle->id,
                    'name' => $this->vehicle->name,
                    'status' => $this->vehicle->status,
                ];
            }),
        ];
    }
}
