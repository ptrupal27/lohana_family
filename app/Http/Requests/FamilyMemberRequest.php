<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FamilyMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'gender' => 'required|string',
            'address' => 'required|string',
            'district' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'city_village' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'mobile' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'required|date',
            'occupation' => 'nullable|string|max:255',
            'hometown' => 'nullable|string|max:255',
            'relation' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'નામ જરૂરી છે.',
            'middle_name.required' => 'પિતા / પતિનું નામ જરૂરી છે.',
            'last_name.required' => 'અટક જરૂરી છે.',
            'gender.required' => 'લિંગ જરૂરી છે.',
            'address.required' => 'સરનામું જરૂરી છે.',
            'date_of_birth.required' => 'જન્મ તારીખ જરૂરી છે.',
            'relation.required' => 'સંબંધ જરૂરી છે.',
        ];
    }
}
