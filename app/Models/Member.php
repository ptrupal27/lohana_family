<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    private const MAIN_MEMBER_PREFIX = 'GLS-S-';

    protected $fillable = [
        'member_no',
        'family_no',
        'is_main',
        'parent_id',
        'photo',
        'first_name',
        'middle_name',
        'mother_name',
        'last_name',
        'gender',
        'blood_group',
        'address',
        'district',
        'sub_district',
        'city_village',
        'pincode',
        'mobile',
        'alternate_mobile',
        'email',
        'date_of_birth',
        'occupation',
        'hometown',
        'relation',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Member::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'parent_id');
    }

    public function getRouteKeyName(): string
    {
        return 'member_no';
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    public static function generateNextMainMemberNo(): string
    {
        $lastMainMember = self::query()
            ->where('is_main', true)
            ->latest('id')
            ->first();

        $nextSequence = 1;
        if ($lastMainMember) {
            // Extract the family number part (GLS-S-001) and increment
            $familyNo = $lastMainMember->family_no;
            $nextSequence = self::extractNumericPart($familyNo) + 1;
        }

        $baseNo = self::MAIN_MEMBER_PREFIX.str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT);

        return $baseNo.'-1';
    }

    public function generateNextFamilyMemberNo(): string
    {
        $lastFamilyMember = $this->children()
            ->latest('id')
            ->first();

        $nextSequence = $lastFamilyMember
            ? self::extractNumericPart($lastFamilyMember->member_no) + 1
            : 2; // Start from 2 because main member is 1

        return $this->family_no.'-'.$nextSequence;
    }

    private static function extractNumericPart(?string $str): int
    {
        if ($str === null || $str === '') {
            return 0;
        }

        // We want the last numeric part.
        // For GLS-S-001-1, it should be 1 if we are looking for member sequence,
        // but for family number generation we might want the 001.
        // Let's make it smarter: find the last segment of digits.
        if (preg_match_all('/\d+/', $str, $matches)) {
            return (int) end($matches[0]);
        }

        return 0;
    }
}
