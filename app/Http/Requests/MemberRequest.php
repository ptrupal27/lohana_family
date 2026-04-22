<?php

namespace App\Http\Requests;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $family = $this->input('family', []);

        if (is_array($family)) {
            $family = array_map(function (mixed $familyMember): mixed {
                if (! is_array($familyMember)) {
                    return $familyMember;
                }

                $familyMember['date_of_birth'] = $this->normalizeDateValue($familyMember['date_of_birth'] ?? null);

                return $familyMember;
            }, $family);
        }

        $this->merge([
            'date_of_birth' => $this->normalizeDateValue($this->input('date_of_birth')),
            'family' => $family,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $member = $this->route('member');
        // Handle both object and string (member_no) from route
        $memberModel = ($member instanceof Member)
            ? $member
            : Member::where('member_no', $member)->first();

        $memberId = $memberModel?->id;
        $isMain = $memberModel ? $memberModel->is_main : true; // Default to true for store()

        $rules = [
            'member_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('members', 'member_no')->ignore($memberId),
            ],
            'family_no' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string',
            'blood_group' => 'nullable|string|max:10',
            'address' => 'required|string',
            'district' => 'required|string|max:255',
            'sub_district' => 'required|string|max:255',
            'city_village' => 'required|string|max:255',
            'pincode' => 'required|numeric',
            'mobile' => ($isMain ? 'required' : 'nullable').'|digits:10',
            'alternate_mobile' => 'nullable|digits:10',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'required|date',
            'occupation' => 'nullable|string|max:255',
            'hometown' => 'nullable|string|max:255',
            'relation' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Family members dynamic fields
            'family' => 'nullable|array',
            'family.*.id' => 'nullable|numeric|exists:members,id',
            'family.*.family_no' => 'nullable|string|max:255',
            'family.*.first_name' => 'required|string|max:255',
            'family.*.middle_name' => 'required|string|max:255',
            'family.*.mother_name' => 'nullable|string|max:255',
            'family.*.last_name' => 'required|string|max:255',
            'family.*.gender' => 'required|string',
            'family.*.blood_group' => 'nullable|string|max:10',
            'family.*.address' => 'required|string',
            'family.*.district' => 'required|string|max:255',
            'family.*.sub_district' => 'required|string|max:255',
            'family.*.city_village' => 'required|string|max:255',
            'family.*.pincode' => 'required|numeric',
            'family.*.mobile' => 'nullable|digits:10',
            'family.*.alternate_mobile' => 'nullable|digits:10',
            'family.*.email' => 'nullable|email|max:255',
            'family.*.date_of_birth' => 'required|date',
            'family.*.occupation' => 'nullable|string|max:255',
            'family.*.hometown' => 'nullable|string|max:255',
            'family.*.relation' => 'required|string|max:255',
            'family.*.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Custom validation for family member numbers to allow ignore
        foreach ($this->input('family', []) as $index => $familyMember) {
            $childId = $familyMember['id'] ?? null;
            $rules["family.{$index}.member_no"] = [
                'required',
                'string',
                'max:255',
                Rule::unique('members', 'member_no')->ignore($childId),
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'member_no.required' => 'સભ્ય નંબર જરૂરી છે.',
            'member_no.unique' => 'આ સભ્ય નંબર પહેલેથી ઉપયોગમાં છે.',
            'first_name.required' => 'નામ જરૂરી છે.',
            'middle_name.required' => 'પિતા / પતિનું નામ જરૂરી છે.',
            'last_name.required' => 'અટક જરૂરી છે.',
            'gender.required' => 'લિંગ પસંદ કરવું જરૂરી છે.',
            'address.required' => 'સરનામું જરૂરી છે.',
            'district.required' => 'જિલ્લો જરૂરી છે.',
            'sub_district.required' => 'તાલુકો જરૂરી છે.',
            'city_village.required' => 'શહેર / ગામ જરૂરી છે.',
            'pincode.required' => 'પિનકોડ જરૂરી છે.',
            'mobile.required' => 'મોબાઇલ નંબર જરૂરી છે.',
            'mobile.digits' => 'મોબાઇલ નંબર ૧૦ અંકનો હોવો જોઈએ.',
            'alternate_mobile.digits' => 'વૈકલ્પિક મોબાઇલ નંબર ૧૦ અંકનો હોવો જોઈએ.',
            'family.*.mobile.digits' => 'પરિવારના સભ્યનો મોબાઇલ નંબર ૧૦ અંકનો હોવો જોઈએ.',
            'family.*.alternate_mobile.digits' => 'પરિવારના સભ્યનો વૈકલ્પિક મોબાઇલ નંબર ૧૦ અંકનો હોવો જોઈએ.',
            'photo.image' => 'ફાઇલ ફોટો હોવી જોઈએ.',
            'photo.max' => 'ફોટો 2MB થી મોટો ન હોવો જોઈએ.',
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
