<?php

namespace App\Http\Controllers;

use App\Http\Requests\FamilyMemberRequest;
use App\Models\Member;
use Illuminate\Support\Facades\Storage;

class FamilyMemberController extends Controller
{
    public function store(FamilyMemberRequest $request, Member $member)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        // Generate sequential member number
        $lastFamily = $member->children()->latest('id')->first();
        $nextSeq = 1;
        if ($lastFamily && $lastFamily->member_no) {
            $parts = explode('-', $lastFamily->member_no);
            $nextSeq = (int) end($parts) + 1;
        }
        $data['member_no'] = $member->member_no.'-'.str_pad($nextSeq, 2, '0', STR_PAD_LEFT);
        $data['is_main'] = false;
        $data['parent_id'] = $member->id;

        Member::create($data);

        return redirect()->route('members.show', $member)->with('success', 'પરિવારના સભ્ય સફળતાપૂર્વક ઉમેરવામાં આવ્યા છે.');
    }

    public function edit(Member $member, Member $familyMember)
    {
        $this->ensureFamilyMemberBelongsToMember($member, $familyMember);

        return view('family-members.edit', compact('member', 'familyMember'));
    }

    public function update(FamilyMemberRequest $request, Member $member, Member $familyMember)
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

        return redirect()->route('members.show', $member)->with('success', 'પરિવારના સભ્યની વિગતો અપડેટ કરવામાં આવી છે.');
    }

    public function destroy(Member $member, Member $familyMember)
    {
        $this->ensureFamilyMemberBelongsToMember($member, $familyMember);

        if ($familyMember->photo) {
            Storage::disk('public')->delete($familyMember->photo);
        }
        $familyMember->delete();

        return redirect()->route('members.show', $member)->with('success', 'પરિવારના સભ્યને કાઢી નાખવામાં આવ્યા છે.');
    }

    private function ensureFamilyMemberBelongsToMember(Member $member, Member $familyMember): void
    {
        abort_unless(
            $member->is_main && ! $familyMember->is_main && $familyMember->parent_id === $member->id,
            404
        );
    }
}
