<?php

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Sanctum::actingAs(User::factory()->create(), ['*']);
});

test('members can be created through the api', function () {
    $response = $this->postJson('/api/members', memberPayload());

    $response
        ->assertCreated()
        ->assertJsonPath('data.member_no', 'MM0001')
        ->assertJsonPath('data.first_name', 'Trupal');

    $this->assertDatabaseHas('members', [
        'member_no' => 'MM0001',
        'first_name' => 'Trupal',
        'is_main' => true,
    ]);
});

test('members can be shown through the api', function () {
    $member = createMainMember([
        'member_no' => 'MM0001',
    ]);

    createFamilyMember($member, [
        'member_no' => 'MM0001-01',
        'first_name' => 'Raj',
    ]);

    $this->getJson('/api/members/MM0001')
        ->assertSuccessful()
        ->assertJsonPath('data.member_no', 'MM0001')
        ->assertJsonPath('data.family_members.0.member_no', 'MM0001-01');
});

test('members can be updated through the api', function () {
    $member = createMainMember([
        'member_no' => 'MM0001',
    ]);

    $response = $this->putJson('/api/members/MM0001', memberPayload([
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
        'member_no' => 'MM0001',
    ]);

    $this->deleteJson('/api/members/MM0001')
        ->assertSuccessful()
        ->assertJsonPath('message', 'Member deleted successfully');

    $this->assertDatabaseMissing('members', [
        'member_no' => 'MM0001',
    ]);
});

test('family members can be created and shown through the api', function () {
    $member = createMainMember([
        'member_no' => 'MM0001',
    ]);

    $storeResponse = $this->postJson('/api/members/MM0001/family-members', familyPayload([
        'first_name' => 'Raj',
        'mobile' => '9090909091',
    ]));

    $storeResponse
        ->assertCreated()
        ->assertJsonPath('data.member_no', 'MM0001-01')
        ->assertJsonPath('data.first_name', 'Raj');

    $this->getJson('/api/members/MM0001/family-members/MM0001-01')
        ->assertSuccessful()
        ->assertJsonPath('data.member_no', 'MM0001-01')
        ->assertJsonPath('data.parent_id', $member->id);
});

test('family members can be updated and deleted through the api', function () {
    $member = createMainMember([
        'member_no' => 'MM0001',
    ]);

    createFamilyMember($member, [
        'member_no' => 'MM0001-01',
        'first_name' => 'Raj',
    ]);

    $this->putJson('/api/members/MM0001/family-members/MM0001-01', familyPayload([
        'first_name' => 'Regina',
        'mobile' => '9090909092',
    ]))
        ->assertSuccessful()
        ->assertJsonPath('data.first_name', 'Regina')
        ->assertJsonPath('data.mobile', '9090909092');

    $this->deleteJson('/api/members/MM0001/family-members/MM0001-01')
        ->assertSuccessful()
        ->assertJsonPath('message', 'Family member deleted successfully.');

    $this->assertDatabaseMissing('members', [
        'member_no' => 'MM0001-01',
    ]);
});

function memberPayload(array $overrides = []): array
{
    return array_merge([
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
        'member_no' => 'MM9999',
        'is_main' => true,
        'parent_id' => null,
    ], $overrides));
}

function createFamilyMember(Member $member, array $overrides = []): Member
{
    return Member::create(array_merge(familyPayload(), [
        'member_no' => $member->member_no.'-01',
        'is_main' => false,
        'parent_id' => $member->id,
    ], $overrides));
}
