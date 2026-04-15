<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'member_id',
        'member_number',
        'photo',
        'first_name',
        'middle_name',
        'mother_name',
        'last_name',
        'gender',
        'address',
        'district',
        'sub_district',
        'city_village',
        'pincode',
        'mobile',
        'email',
        'date_of_birth',
        'occupation',
        'hometown',
        'relation',
    ];

    /**
     * Get the member that owns the family member.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
