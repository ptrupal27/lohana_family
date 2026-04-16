<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FamilyMemberRequest;
use App\Http\Requests\MemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Member::where('is_main', true)->with('children');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('member_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate($request->get('per_page', 15));

        return MemberResource::collection($members);
    }

    public function store(MemberRequest $request): JsonResponse
    {
        $data = $request->validated();

        $mainMember = DB::transaction(function () use ($request, $data) {
            $data['is_main'] = true;

            $data['member_no'] = Member::generateNextMainMemberNo();

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('members', 'public');
            }

            $member = Member::create(Arr::except($data, ['family']));

            if ($request->has('family')) {
                foreach ($request->family as $familyData) {
                    $familyData['is_main'] = false;
                    $familyData['parent_id'] = $member->id;
                    $familyData['member_no'] = $member->generateNextFamilyMemberNo();

                    Member::create($familyData);
                }
            }

            return $member->load('children');
        });

        return (new MemberResource($mainMember))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Member $member): MemberResource
    {
        $member->load('children');

        return new MemberResource($member);
    }

    public function update(MemberRequest $request, Member $member): MemberResource
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $member) {
            if ($request->hasFile('photo')) {
                if ($member->photo) {
                    Storage::disk('public')->delete($member->photo);
                }
                $data['photo'] = $request->file('photo')->store('members', 'public');
            }

            $member->update(Arr::except($data, ['family', 'id']));

            if ($request->has('family')) {
                foreach ($request->family as $familyData) {
                    if (isset($familyData['id'])) {
                        $child = Member::findOrFail($familyData['id']);
                        $child->update(Arr::except($familyData, ['id']));
                    } else {
                        $familyData['is_main'] = false;
                        $familyData['parent_id'] = $member->id;
                        $familyData['member_no'] = $member->generateNextFamilyMemberNo();

                        Member::create($familyData);
                    }
                }
            }
        });

        return new MemberResource($member->fresh('children'));
    }

    public function destroy(Member $member): JsonResponse
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return response()->json(['message' => 'Member deleted successfully']);
    }

    public function storeFamilyMember(FamilyMemberRequest $request, Member $member): JsonResponse
    {
        abort_unless($member->is_main, 404);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        $data['member_no'] = $member->generateNextFamilyMemberNo();
        $data['is_main'] = false;
        $data['parent_id'] = $member->id;
        $data['family_no'] = $member->family_no;

        $familyMember = Member::create($data);

        return (new MemberResource($familyMember))
            ->response()
            ->setStatusCode(201);
    }

    public function showFamilyMember(Member $member, Member $familyMember): MemberResource
    {
        $this->ensureFamilyMemberBelongsToMember($member, $familyMember);

        return new MemberResource($familyMember);
    }

    public function updateFamilyMember(FamilyMemberRequest $request, Member $member, Member $familyMember): MemberResource
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

    public function destroyFamilyMember(Member $member, Member $familyMember): JsonResponse
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
