<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::where('is_main', true)->withCount('children');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('member_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate(10)->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(MemberRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            // Main Member Logic
            $data['is_main'] = true;

            // Generate Main Member Number
            $lastMainMember = Member::where('is_main', true)->latest('id')->first();
            $nextId = ($lastMainMember ? (int) str_replace('MM', '', $lastMainMember->member_no) : 0) + 1;
            $data['member_no'] = 'MM'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('members', 'public');
            }

            // EXCLUDE nested family data from main member creation
            $mainMember = Member::create(Arr::except($data, ['family']));

            // Family Members Logic
            if ($request->has('family')) {
                $subIndex = 1;
                foreach ($request->family as $familyData) {
                    $familyData['is_main'] = false;
                    $familyData['parent_id'] = $mainMember->id;
                    $familyData['member_no'] = $mainMember->member_no.'-'.str_pad($subIndex, 2, '0', STR_PAD_LEFT);

                    Member::create($familyData);
                    $subIndex++;
                }
            }
        });

        return redirect()->route('members.index')->with('success', 'સભ્ય સફળતાપૂર્વક ઉમેરવામાં આવ્યા છે.');
    }

    public function show(Member $member)
    {
        $member->load('children');

        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(MemberRequest $request, Member $member)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $member) {
            if ($request->hasFile('photo')) {
                if ($member->photo) {
                    Storage::disk('public')->delete($member->photo);
                }
                $data['photo'] = $request->file('photo')->store('members', 'public');
            }

            // EXCLUDE nested family data from main member update
            $member->update(Arr::except($data, ['family', 'id']));

            if ($request->has('family')) {
                // Get the starting index for NEW members based on existing children count
                $existingCount = $member->children()->count();
                $subIndex = $existingCount + 1;

                foreach ($request->family as $familyData) {
                    if (isset($familyData['id'])) {
                        // Update existing child
                        $child = Member::findOrFail($familyData['id']);
                        $child->update(Arr::except($familyData, ['id']));
                    } else {
                        // Create new child
                        $familyData['is_main'] = false;
                        $familyData['parent_id'] = $member->id;
                        $familyData['member_no'] = $member->member_no.'-'.str_pad($subIndex, 2, '0', STR_PAD_LEFT);

                        Member::create($familyData);
                        $subIndex++;
                    }
                }
            }
        });

        return redirect()->route('members.show', $member)->with('success', 'સભ્યની વિગતો સફળતાપૂર્વક અપડેટ કરવામાં આવી છે.');
    }

    public function destroy(Member $member)
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return redirect()->route('members.index')->with('success', 'સભ્ય સફળતાપૂર્વક કાઢી નાખવામાં આવ્યા છે.');
    }
}
