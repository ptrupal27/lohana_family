<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member_no' => $this->member_no,
            'is_main' => (bool) $this->is_main,
            'parent_id' => $this->parent_id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'mother_name' => $this->mother_name,
            'full_name' => "{$this->first_name} {$this->middle_name} {$this->last_name}",
            'gender' => $this->gender,
            'mobile' => $this->mobile,
            'alternate_mobile' => $this->alternate_mobile,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'district' => $this->district,
            'sub_district' => $this->sub_district,
            'city_village' => $this->city_village,
            'pincode' => $this->pincode,
            'occupation' => $this->occupation,
            'hometown' => $this->hometown,
            'relation' => $this->relation,
            'photo_url' => $this->photo ? asset('storage/'.$this->photo) : null,
            'family_members' => MemberResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
