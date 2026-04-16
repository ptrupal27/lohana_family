<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class FamilyMemberRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'date_of_birth' => $this->normalizeDateValue($this->input('date_of_birth')),
        ]);
    }

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

    private function normalizeDateValue(mixed $date): mixed
    {
        if (! is_string($date) || $date === '') {
            return $date;
        }

        $normalizedDate = trim(str_replace('/', '-', $date));
        $formats = ['Y-m-d', 'd-m-Y', 'm-d-Y'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $normalizedDate)->format('Y-m-d');
            } catch (\Throwable $exception) {
                // Try next format.
            }
        }

        return $date;
    }
}
