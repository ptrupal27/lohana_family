<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'member_no',
        'is_main',
        'parent_id',
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

    public function children()
    {
        return $this->hasMany(Member::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Member::class, 'parent_id');
    }

    public function getRouteKeyName()
    {
        return 'member_no';
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }
}
