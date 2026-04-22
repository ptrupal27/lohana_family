@extends('layouts.app')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('members.show', $member) }}">{{ $member->first_name }} {{ $member->last_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">પરિવારના સભ્ય એડિટ કરો</li>
        </ol>
    </nav>
    <h2>પરિવારના સભ્ય એડિટ કરો ({{ $familyMember->first_name }})</h2>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('api.members.family-members.update', [$member, $familyMember]) }}" method="POST" enctype="multipart/form-data" data-api-form data-api-url="{{ route('api.members.family-members.update', [$member, $familyMember]) }}" data-api-method="PUT" data-redirect-url="{{ route('members.show', $member) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-12 mb-4">
                    <h5 class="text-success border-start border-4 border-success ps-3">પ્રાથમિક વિગતો</h5>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">પરિવાર નંબર</label>
                    <input type="text" name="family_no" class="form-control" value="{{ old('family_no', $familyMember->family_no) }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">સભ્ય નંબર <span class="text-danger">*</span></label>
                    <input type="text" name="member_no" class="form-control" value="{{ old('member_no', $familyMember->member_no) }}" required>
                </div>

                <div class="col-md-6 mb-3"></div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">નામ <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $familyMember->first_name) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">પિતા / પતિનું નામ <span class="text-danger">*</span></label>
                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $familyMember->middle_name) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">અટક <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $familyMember->last_name) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">સંબંધ (મુખ્ય સભ્ય સાથે) <span class="text-danger">*</span></label>
                    <input type="text" name="relation" class="form-control" value="{{ old('relation', $familyMember->relation) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">માતાનું નામ</label>
                    <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $familyMember->mother_name) }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">લિંગ <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select" required>
                        <option value="Male" {{ old('gender', $familyMember->gender) == 'Male' ? 'selected' : '' }}>પુરુષ</option>
                        <option value="Female" {{ old('gender', $familyMember->gender) == 'Female' ? 'selected' : '' }}>સ્ત્રી</option>
                        <option value="Other" {{ old('gender', $familyMember->gender) == 'Other' ? 'selected' : '' }}>અન્ય</option>
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">બ્લડ ગ્રુપ</label>
                    <select name="blood_group" class="form-select">
                        <option value="">પસંદ કરો</option>
                        <option value="A+" {{ old('blood_group', $familyMember->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('blood_group', $familyMember->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('blood_group', $familyMember->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('blood_group', $familyMember->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ old('blood_group', $familyMember->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('blood_group', $familyMember->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ old('blood_group', $familyMember->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('blood_group', $familyMember->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">જન્મ તારીખ <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $familyMember->date_of_birth) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">મોબાઇલ નંબર</label>
                    <input type="number" name="mobile" class="form-control" value="{{ old('mobile', $familyMember->mobile) }}" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
                </div>

                <div class="col-md-12 mb-4 mt-3">
                    <h5 class="text-primary border-start border-4 border-primary ps-3">સરનામું અને સંપર્ક</h5>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">સરનામું <span class="text-danger">*</span></label>
                    <textarea name="address" rows="1" class="form-control" required>{{ old('address', $familyMember->address) }}</textarea>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">જિલ્લો <span class="text-danger">*</span></label>
                    <input type="text" name="district" class="form-control" value="{{ old('district', $familyMember->district) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">તાલુકો <span class="text-danger">*</span></label>
                    <input type="text" name="sub_district" class="form-control" value="{{ old('sub_district', $familyMember->sub_district) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">શહેર / ગામ <span class="text-danger">*</span></label>
                    <input type="text" name="city_village" class="form-control" value="{{ old('city_village', $familyMember->city_village) }}" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">પિનકોડ <span class="text-danger">*</span></label>
                    <input type="number" name="pincode" class="form-control" value="{{ old('pincode', $familyMember->pincode) }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">વ્યવસાય</label>
                    <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $familyMember->occupation) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">ફોટો</label>
                    <div class="d-flex align-items-center">
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        @if($familyMember->photo)
                            <img src="{{ asset('storage/'.$familyMember->photo) }}" width="40" class="img-thumbnail ms-2 mb-1">
                        @endif
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">વતન</label>
                    <input type="text" name="hometown" class="form-control" value="{{ old('hometown', $familyMember->hometown) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">ફોટો</label>
                    @if($familyMember->photo)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $familyMember->photo) }}" alt="" class="img-thumbnail" width="80">
                        </div>
                    @endif
                    <input type="file" name="photo" class="form-control">
                </div>
            </div>

            <div class="mt-4 border-top pt-4">
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-none">અપડેટ કરો</button>
                <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary px-5 shadow-none">કેન્સલ</a>
            </div>
        </form>
    </div>
</div>
@endsection
