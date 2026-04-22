<?php

namespace App\Http\Controllers;

use App\Http\Requests\FamilyMemberRequest;
use App\Http\Requests\MemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query()
            ->with('parent:id,member_no,first_name,middle_name,last_name')
            ->withCount('children');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('member_no', 'like', "%{$search}%")
                    ->orWhere('family_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('alternate_mobile', 'like', "%{$search}%");
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

        $mainMember = DB::transaction(function () use ($request, $data) {
            // Main Member Logic
            $data['is_main'] = true;

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('members', 'public');
            }

            // EXCLUDE nested family data from main member creation
            $member = Member::create(Arr::except($data, ['family']));

            // Family Members Logic
            if ($request->has('family')) {
                foreach ($request->family as $index => $familyData) {
                    $familyData['is_main'] = false;
                    $familyData['parent_id'] = $member->id;
                    $familyData['family_no'] = $familyData['family_no'] ?? $member->family_no;

                    if ($request->hasFile("family.{$index}.photo")) {
                        $familyData['photo'] = $request->file("family.{$index}.photo")->store('members', 'public');
                    }

                    Member::create($familyData);
                }
            }

            return $member;
        });

        if ($request->expectsJson()) {
            return (new MemberResource($mainMember->load('children')))
                ->response()
                ->setStatusCode(201);
        }

        return redirect()->route('members.index')->with('success', 'સભ્ય સફળતાપૂર્વક ઉમેરવામાં આવ્યા છે.');
    }

    public function show(Member $member)
    {
        $mainMember = $member->is_main ? $member : $member->parent;

        if ($mainMember) {
            $mainMember->load('children');
            // Including main member and all children in the list
            $familyMembers = collect([$mainMember])->concat($mainMember->children);
        } else {
            $familyMembers = collect([$member]);
        }

        if (request()->expectsJson()) {
            return new MemberResource($member);
        }

        return view('members.show', compact('member', 'familyMembers'));
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
                foreach ($request->family as $index => $familyData) {
                    if ($request->hasFile("family.{$index}.photo")) {
                        $familyData['photo'] = $request->file("family.{$index}.photo")->store('members', 'public');
                    }

                    if (isset($familyData['id'])) {
                        // Update existing child
                        $child = Member::findOrFail($familyData['id']);
                        $child->update(Arr::except($familyData, ['id']));
                    } else {
                        $familyData['is_main'] = false;
                        $familyData['parent_id'] = $member->id;
                        $familyData['family_no'] = $familyData['family_no'] ?? $member->family_no;

                        Member::create($familyData);
                    }
                }
            }
        });

        if ($request->expectsJson()) {
            return new MemberResource($member->fresh('children'));
        }

        return redirect()->route('members.show', $member)->with('success', 'સભ્યની વિગતો સફળતાપૂર્વક અપડેટ કરવામાં આવી છે.');
    }

    public function destroy(Member $member)
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'સભ્ય સફળતાપૂર્વક કાઢી નાખવામાં આવ્યા છે.']);
        }

        return redirect()->route('members.index')->with('success', 'સભ્ય સફળતાપૂર્વક કાઢી નાખવામાં આવ્યા છે.');
    }

    public function editFamilyMember(Member $member, Member $familyMember)
    {
        $this->ensureFamilyMemberBelongsToMember($member, $familyMember);

        return view('family-members.edit', compact('member', 'familyMember'));
    }

    public function storeFamilyMember(FamilyMemberRequest $request, Member $member): JsonResponse
    {
        abort_unless($member->is_main, 404);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        $data['is_main'] = false;
        $data['parent_id'] = $member->id;
        $data['family_no'] = $data['family_no'] ?? $member->family_no;

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
            'message' => 'પરિવારના સભ્ય સફળતાપૂર્વક કાઢી નાખવામાં આવ્યા છે.',
        ]);
    }

    private function ensureFamilyMemberBelongsToMember(Member $member, Member $familyMember): void
    {
        abort_unless(
            $member->is_main && ! $familyMember->is_main && $familyMember->parent_id === $member->id,
            404
        );
    }

    public function printAll(Request $request)
    {
        $selectedMembers = $request->input('selected_members', []);

        $membersQuery = Member::query()
            ->with('parent:id,member_no')
            ->withCount('children')
            ->orderBy('member_no');

        if (is_array($selectedMembers) && count($selectedMembers) > 0) {
            $membersQuery->whereIn('member_no', $selectedMembers);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->get('search');
            $membersQuery->where(function ($q) use ($search) {
                $q->where('member_no', 'like', "%{$search}%")
                    ->orWhere('family_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('alternate_mobile', 'like', "%{$search}%");
            });
        }

        $members = $membersQuery->get();

        // Standardize default columns
        $defaultColumns = ['member_no', 'full_name', 'mobile', 'address'];

        // Robustly get columns (handles columns, columns[], etc.)
        $selectedColumns = $request->input('columns') ?: $request->input('columns_arr');

        if (! $selectedColumns || ! is_array($selectedColumns)) {
            $selectedColumns = $defaultColumns;
        }

        return view('members.print-all', compact('members', 'selectedColumns'));
    }

    public function printLabels(Request $request)
    {
        $selectedMembers = $request->input('selected_members', []);
        $width = $request->input('width', '80'); // Default 80mm
        $height = $request->input('height', '50'); // Default 50mm
        $groupByFamily = $request->input('group_by_family', false);

        $membersQuery = Member::query()
            ->with('parent:id,member_no')
            ->orderBy('family_no')
            ->orderBy('member_no');

        if (is_array($selectedMembers) && count($selectedMembers) > 0) {
            $membersQuery->whereIn('member_no', $selectedMembers);
        } elseif ($request->has('search') && $request->search != '') {
            $search = $request->get('search');
            $membersQuery->where(function ($q) use ($search) {
                $q->where('member_no', 'like', "%{$search}%")
                    ->orWhere('family_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('alternate_mobile', 'like', "%{$search}%");
            });
        }

        $members = $membersQuery->get();

        if ($groupByFamily) {
            $members = $members->groupBy('family_no');
        }

        $selectedColumns = $request->input('columns', ['mobile', 'city_village', 'address']);

        return view('members.print-labels', compact('members', 'width', 'height', 'groupByFamily', 'selectedColumns'));
    }

    public function printSingle(Member $member)
    {
        $member->load('children');

        return view('members.print-single', compact('member'));
    }

    public function exportExcel(Request $request)
    {
        $query = Member::query()->orderBy('member_no');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('member_no', 'like', "%{$search}%")
                    ->orWhere('family_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('alternate_mobile', 'like', "%{$search}%");
            });
        }

        $members = $query->get();

        // Define all possible columns and their labels
        $allColumns = [
            'member_no' => 'સભ્ય નં.',
            'full_name' => 'નામ',
            'family_no' => 'પરિવાર નં.',
            'mobile' => 'મોબાઇલ',
            'alternate_mobile' => 'અલ્ટરનેટ મોબાઇલ',
            'city_village' => 'શહેર / ગામ',
            'mother_name' => 'માતાનું નામ',
            'gender' => 'લિંગ',
            'occupation' => 'વ્યવસાય',
            'hometown' => 'વતન',
            'address' => 'સરનામું',
            'district' => 'જિલ્લો',
            'sub_district' => 'તાલુકો',
            'date_of_birth' => 'જન્મ તારીખ',
            'children_count' => 'પરિવારની સંખ્યા',
            'is_main' => 'સભ્ય પ્રકાર',
        ];

        $selectedKeys = $request->input('columns') ?: $request->input('columns_arr');
        if (! $selectedKeys || ! is_array($selectedKeys)) {
            $selectedKeys = array_keys($allColumns);
        }

        $headers = [];
        foreach ($selectedKeys as $key) {
            if (isset($allColumns[$key])) {
                $headers[] = $allColumns[$key];
            }
        }

        $callback = function () use ($members, $selectedKeys, $headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $headers);

            foreach ($members as $member) {
                $row = [];
                foreach ($selectedKeys as $key) {
                    if ($key === 'full_name') {
                        $row[] = $member->full_name;
                    } elseif ($key === 'children_count') {
                        $row[] = $member->children_count ?? $member->children()->count();
                    } else {
                        $row[] = $member->$key;
                    }
                }
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="members_list_'.now()->format('d_m_Y').'.csv"',
        ]);
    }
}
