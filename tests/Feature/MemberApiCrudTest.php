<?php

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('members can be created through the api', function () {
    $response = $this->postJson('/api/members', memberPayload());

    $response
        ->assertCreated()
        ->assertJsonPath('data.member_no', 'GLS-S-001')
        ->assertJsonPath('data.first_name', 'Trupal');

    $this->assertDatabaseHas('members', [
        'member_no' => 'GLS-S-001',
        'first_name' => 'Trupal',
        'is_main' => true,
    ]);
});

test('members can be shown through the api', function () {
    $member = createMainMember([
        'member_no' => 'GLS-S-001',
    ]);

    createFamilyMember($member, [
        'member_no' => 'GLS-S-001-1',
        'first_name' => 'Raj',
    ]);

    $this->getJson('/api/members/GLS-S-001')
        ->assertSuccessful()
        ->assertJsonPath('data.member_no', 'GLS-S-001')
        ->assertJsonPath('data.family_members.0.member_no', 'GLS-S-001-1');
});

test('members can be updated through the api', function () {
    $member = createMainMember([
        'member_no' => 'GLS-S-001',
    ]);

    $response = $this->putJson('/api/members/GLS-S-001', memberPayload([
        'first_name' => 'Updated',
        'mobile' => '9999999999',
        'email' => 'updated@example.com',
    ]));

    $response
        ->assertSuccessful()
        ->assertJsonPath('data.first_name', 'Updated')
        ->assertJsonPath('data.mobile', '9999999999');

    $this->assertDatabaseHas('members', [
        'id' => $member->id,
        'first_name' => 'Updated',
        'mobile' => '9999999999',
    ]);
});

test('members can be deleted through the api', function () {
    createMainMember([
        'member_no' => 'GLS-S-001',
    ]);

    $this->deleteJson('/api/members/GLS-S-001')
        ->assertSuccessful()
        ->assertJsonPath('message', 'સભ્ય સફળતાપૂર્વક કાઢી નાખવામાં આવ્યા છે.');

    $this->assertDatabaseMissing('members', [
        'member_no' => 'GLS-S-001',
    ]);
});

test('family members can be created and shown through the api', function () {
    $member = createMainMember([
        'member_no' => 'GLS-S-001',
    ]);

    $storeResponse = $this->postJson("/api/members/{$member->member_no}/family-members", familyPayload([
        'first_name' => 'Raj',
        'mobile' => '9090909091',
        'member_no' => 'GLS-S-001-1',
    ]));

    $storeResponse
        ->assertCreated()
        ->assertJsonPath('data.member_no', 'GLS-S-001-1')
        ->assertJsonPath('data.first_name', 'Raj');

    $this->getJson("/api/members/{$member->member_no}/family-members/GLS-S-001-1")
        ->assertSuccessful()
        ->assertJsonPath('data.member_no', 'GLS-S-001-1')
        ->assertJsonPath('data.parent_id', $member->id);
});

test('family members can be updated and deleted through the api', function () {
    $member = createMainMember([
        'member_no' => 'GLS-S-001',
    ]);

    $familyMember = createFamilyMember($member, [
        'member_no' => 'GLS-S-001-1',
        'first_name' => 'Raj',
    ]);

    $this->putJson("/api/members/{$member->member_no}/family-members/{$familyMember->member_no}", familyPayload([
        'member_no' => 'GLS-S-001-1',
        'first_name' => 'Regina',
        'mobile' => '9090909092',
    ]))
        ->assertSuccessful()
        ->assertJsonPath('data.first_name', 'Regina')
        ->assertJsonPath('data.mobile', '9090909092');

    $this->deleteJson("/api/members/{$member->member_no}/family-members/{$familyMember->member_no}")
        ->assertSuccessful()
        ->assertJsonPath('message', 'પરિવારના સભ્ય સફળતાપૂર્વક કાઢી નાખવામાં આવ્યા છે.');

    $this->assertDatabaseMissing('members', [
        'member_no' => 'GLS-S-001-1',
    ]);
});

function memberPayload(array $overrides = []): array
{
    return array_merge([
        'member_no' => 'GLS-S-001',
        'first_name' => 'Trupal',
        'middle_name' => 'Ashvin',
        'mother_name' => 'Paul Bernard',
        'last_name' => 'Patel',
        'gender' => 'Male',
        'address' => 'SURAT (M CORP.) (PART) C-27 PARMAHANS NEAR TRIKAMNAGAR Provident placeat',
        'district' => 'Surat',
        'sub_district' => 'Surat',
        'city_village' => 'Surat (M Corp.) (Part)',
        'pincode' => '395010',
        'mobile' => '9090909090',
        'email' => 'trupal@example.com',
        'date_of_birth' => '1995-02-08',
        'occupation' => 'CA',
        'hometown' => 'Surat (M Corp.) (Part)',
    ], $overrides);
}

function familyPayload(array $overrides = []): array
{
    return array_merge([
        'member_no' => 'GLS-S-001-1',
        'first_name' => 'Raj',
        'middle_name' => 'Trupal',
        'mother_name' => 'Paul Bernard',
        'last_name' => 'Patel',
        'gender' => 'Male',
        'address' => 'Family address line',
        'district' => 'Surat',
        'sub_district' => 'Surat',
        'city_village' => 'Surat',
        'pincode' => '395010',
        'mobile' => '9090909091',
        'email' => 'raj@example.com',
        'date_of_birth' => '2015-03-18',
        'occupation' => 'Student',
        'hometown' => 'Surat',
        'relation' => 'Son',
    ], $overrides);
}

function createMainMember(array $overrides = []): Member
{
    return Member::create(array_merge(memberPayload(), [
        'member_no' => 'GLS-S-999',
        'is_main' => true,
        'parent_id' => null,
    ], $overrides));
}

function createFamilyMember(Member $member, array $overrides = []): Member
{
    return Member::create(array_merge(familyPayload(), [
        'member_no' => $member->member_no.'-1',
        'is_main' => false,
        'parent_id' => $member->id,
    ], $overrides));
}
