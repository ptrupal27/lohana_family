<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FamilyMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FamilyMemberController extends Controller
{
    public function store(FamilyMemberRequest $request, Member $member): JsonResponse
    {
        abort_unless($member->is_main, 404);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        $lastFamilyMember = $member->children()->latest('id')->first();
        $nextSequence = 1;

        if ($lastFamilyMember?->member_no) {
            $parts = explode('-', $lastFamilyMember->member_no);
            $nextSequence = (int) end($parts) + 1;
        }

        $data['member_no'] = $member->member_no.'-'.str_pad($nextSequence, 2, '0', STR_PAD_LEFT);
        $data['is_main'] = false;
        $data['parent_id'] = $member->id;

        $familyMember = Member::create($data);

        return (new MemberResource($familyMember))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Member $member, Member $familyMember): MemberResource
    {
        $this->ensureFamilyMemberBelongsToMember($member, $familyMember);

        return new MemberResource($familyMember);
    }

    public function update(FamilyMemberRequest $request, Member $member, Member $familyMember): MemberResource
    {
        $this->ensureFamilyMemberBelongsToMember($member, $familyMember);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($familyMember->photo) {
                Storage::disk('public')->delete($familyMember->photo);
            }

            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        $familyMember->update($data);

        return new MemberResource($familyMember->fresh());
    }

    public function destroy(Member $member, Member $familyMember): JsonResponse
    {
        $this->ensureFamilyMemberBelongsToMember($member, $familyMember);

        if ($familyMember->photo) {
            Storage::disk('public')->delete($familyMember->photo);
        }

        $familyMember->delete();

        return response()->json([
            'message' => 'Family member deleted successfully.',
        ]);
    }

    private function ensureFamilyMemberBelongsToMember(Member $member, Member $familyMember): void
    {
        abort_unless(
            $member->is_main && ! $familyMember->is_main && $familyMember->parent_id === $member->id,
            404
        );
    }
}
