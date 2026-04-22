@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item small"><a href="{{ route('members.index') }}">સભ્યોની યાદી</a></li>
                <li class="breadcrumb-item small active" aria-current="page">સભ્યની વિગતો</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-maroon mb-0">સભ્યની વિગતો</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('members.print.single', $member) }}" target="_blank" class="btn btn-outline-dark px-3 py-2 fw-bold">
            <i class="bi bi-printer me-2"></i> પ્રિન્ટ
        </a>
        <a href="{{ route('members.edit', $member) }}" class="btn btn-primary px-3 py-2 fw-bold">
            <i class="bi bi-pencil me-2"></i> એડિટ કરો
        </a>
    </div>
</div>


@php
    $mainMemberForRoute = $member->is_main ? $member : ($member->parent ?? $member);
@endphp

<div class="row" data-member-show-page data-api-show-url="{{ route('api.members.show', $member) }}" data-family-edit-template="{{ route('family-members.edit', [$mainMemberForRoute, '__FAMILY__']) }}" data-family-delete-template="{{ route('api.members.family-members.destroy', [$mainMemberForRoute, '__FAMILY__']) }}" data-reload-url="{{ route('members.show', $member) }}">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center">
                <div data-member-photo-wrapper>
                @if($member->photo)
                    <div class="rounded-circle mx-auto mb-3 border p-1 shadow-sm overflow-hidden" style="width: 152px; height: 152px;">
                        <img src="{{ asset('storage/' . $member->photo) }}" alt="" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                    </div>
                @else
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                        <i class="bi bi-person" style="font-size: 5rem;"></i>
                    </div>
                @endif
                </div>
                <h4 class="fw-bold text-maroon" data-member-field="full_name">{{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }}</h4>
                <div class="mb-2">
                    <span class="badge bg-soft-maroon text-maroon border px-3 py-2 rounded-pill" data-member-field="member_no">{{ $member->member_no }}</span>
                </div>
                <p class="text-muted mb-1"><i class="bi bi-telephone me-2"></i><span data-member-field="mobile">{{ $member->mobile }}</span></p>
                @if($member->alternate_mobile)
                    <p class="text-muted small"><i class="bi bi-telephone-plus me-2"></i>{{ $member->alternate_mobile }} (બીજો)</p>
                @endif
                <hr>
                <div class="text-start">
                    <p class="mb-2"><strong>માતાનું નામ:</strong> {{ $member->mother_name ?? '-' }}</p>
                    <p class="mb-2"><strong>લિંગ:</strong> @if($member->gender == 'Male') પુરુષ @elseif($member->gender == 'Female') સ્ત્રી @else અન્ય @endif</p>
                    <p class="mb-2"><strong>બ્લડ ગ્રુપ:</strong> {{ $member->blood_group ?? '-' }}</p>
                    <p class="mb-2"><strong>જન્મ તારીખ:</strong> {{ \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') }}</p>
                    <p class="mb-2"><strong>વ્યવસાય:</strong> {{ $member->occupation ?? '-' }}</p>
                    <p class="mb-2"><strong>વતન:</strong> {{ $member->hometown ?? '-' }}</p>
                    <p class="mb-2"><strong>ઇમેઇલ:</strong> {{ $member->email ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h5 class="mb-0 fw-bold text-primary">સરનામું અને સંપર્ક</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small d-block">સરનામું</label>
                        <span class="fw-bold">{{ $member->address }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">શહેર / ગામ</label>
                        <span class="fw-bold">{{ $member->city_village }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">પિનકોડ</label>
                        <span class="fw-bold">{{ $member->pincode }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">તાલુકો</label>
                        <span class="fw-bold">{{ $member->sub_district }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small d-block">જિલ્લો</label>
                        <span class="fw-bold">{{ $member->district }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0 fw-bold text-success">પરિવારના સભ્યો</h5>
                @if($member->is_main)
                <button type="button" class="btn btn-sm btn-success px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addFamilyMemberModal">
                    <i class="bi bi-plus-lg me-1"></i> ઉમેરો
                </button>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>સભ્ય નં.</th>
                                <th>નામ</th>
                                <th>સંબંધ</th>
                                <th>જન્મ તારીખ</th>
                                <th>મોબાઈલ</th>
                                <th class="text-end">એક્શન</th>
                            </tr>
                        </thead>
                        <tbody data-member-family-rows>
                            @forelse($familyMembers as $family)
                                <tr class="{{ $family->id == $member->id ? 'table-soft-maroon border-start border-4 border-maroon' : '' }}">
                                    <td><span class="badge bg-soft-maroon text-maroon border small">{{ $family->member_no }}</span></td>
                                    <td class="fw-bold">{{ $family->first_name }} {{ $family->last_name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $family->is_main ? 'મુખ્ય સભ્ય' : $family->relation }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($family->date_of_birth)->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $family->mobile ?? '-' }}
                                        @if($family->alternate_mobile)
                                            <div class="small text-muted">{{ $family->alternate_mobile }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($family->is_main)
                                                <a href="{{ route('members.show', $family) }}" class="btn btn-icon btn-view" title="જુઓ">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('family-members.edit', [$family->parent_id ?? $member->id, $family]) }}" class="btn btn-icon btn-edit" title="એડિટ">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @if($member->is_main)
                                                <form action="{{ route('api.members.family-members.destroy', [$member, $family]) }}" method="POST" class="d-inline" data-api-delete-form data-api-url="{{ route('api.members.family-members.destroy', [$member, $family]) }}" data-reload="true">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-delete" title="ડિલીટ">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">કોઈ પરિવારના સભ્યો નથી.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($member->is_main)
<!-- Add Family Member Modal -->
<div class="modal fade" id="addFamilyMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('api.members.family-members.store', $member) }}" method="POST" enctype="multipart/form-data" data-api-form data-api-url="{{ route('api.members.family-members.store', $member) }}" data-reload="true">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">પરિવારના સભ્ય ઉમેરો</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-outline-primary float-end" onclick="copyAddressToModal()">મુખ્ય સભ્યનું સરનામું કોપી કરો</button>
                            <h6 class="fw-bold border-bottom pb-2">પ્રાથમિક વિગતો</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">પરિવાર નંબર</label>
                            <input type="text" name="family_no" id="modal_family_no" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">સભ્ય નંબર <span class="text-danger">*</span></label>
                            <input type="text" name="member_no" class="form-control" required>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">નામ <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">પિતા / પતિનું નામ <span class="text-danger">*</span></label>
                            <input type="text" name="middle_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">અટક <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ $member->last_name }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">સંબંધ <span class="text-danger">*</span></label>
                            <input type="text" name="relation" class="form-control" placeholder="જેમ કે પુત્ર, પત્ની..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">લિંગ <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="">પસંદ કરો</option>
                                <option value="Male">પુરુષ</option>
                                <option value="Female">સ્ત્રી</option>
                                <option value="Other">અન્ય</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">જન્મ તારીખ <span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" class="form-control" required>
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2">સરનામું અને સંપર્ક</h6>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">સરનામું <span class="text-danger">*</span></label>
                            <textarea name="address" id="modal_address" rows="1" class="form-control" required></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">જિલ્લો <span class="text-danger">*</span></label>
                            <input type="text" name="district" id="modal_district" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">તાલુકો <span class="text-danger">*</span></label>
                            <input type="text" name="sub_district" id="modal_sub_district" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">શહેર / ગામ <span class="text-danger">*</span></label>
                            <input type="text" name="city_village" id="modal_city_village" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">પિનકોડ <span class="text-danger">*</span></label>
                            <input type="number" name="pincode" id="modal_pincode" class="form-control" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">મોબાઈલ નંબર</label>
                            <input type="number" name="mobile" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">બીજો મોબાઈલ નંબર</label>
                            <input type="number" name="alternate_mobile" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">વ્યવસાય</label>
                            <input type="text" name="occupation" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">ફોટો</label>
                            <input type="file" name="photo" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">બંધ કરો</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold shadow-none">સાચવો</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function copyAddressToModal() {
        document.getElementById('modal_address').value = "{{ $member->address }}";
        document.getElementById('modal_district').value = "{{ $member->district }}";
        document.getElementById('modal_sub_district').value = "{{ $member->sub_district }}";
        document.getElementById('modal_city_village').value = "{{ $member->city_village }}";
        document.getElementById('modal_pincode').value = "{{ $member->pincode }}";
        document.getElementById('modal_family_no').value = "{{ $member->family_no }}";
    }
</script>
@endif
@endsection

