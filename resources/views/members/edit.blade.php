@extends('layouts.app')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('members.index') }}">સભ્યોની યાદી</a></li>
            <li class="breadcrumb-item"><a href="{{ route('members.show', $member) }}">સભ્યની વિગતો</a></li>
            <li class="breadcrumb-item active" aria-current="page">એડિટ કરો</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-maroon">સભ્ય વિગતો સુધારો ({{ $member->member_no }})</h2>
    </div>
</div>

<div class="card shadow-sm border-0 mb-5 overflow-hidden">
    <div class="card-header bg-white border-bottom-0 p-3 bg-soft-maroon">
        <div class="d-flex justify-content-between align-items-center">
            <ul class="nav nav-pills custom-wizard-tabs" id="memberTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="main-tab" data-bs-toggle="tab" data-bs-target="#main-pane" type="button" role="tab">
                        <span class="step-num">1</span> મુખ્ય સભ્ય
                    </button>
                </li>
                @foreach($member->children as $index => $child)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="family-tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#family-pane-{{ $index }}" type="button" role="tab">
                            <span class="step-num">{{ $index + 2 }}</span> સભ્ય {{ $index + 1 }}
                        </button>
                    </li>
                @endforeach
            </ul>
            <div class="d-flex align-items-center gap-2">
                <div id="familyCountSection" class="d-none animate__animated animate__fadeIn">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-maroon border-2 fw-bold">કેટલા સભ્યો ઉમેરવા છે?</span>
                        <input type="number" id="familyCountInput" class="form-control border-maroon border-2 fw-bold text-center" style="width: 60px;" value="1" min="1">
                        <button class="btn btn-maroon fw-bold" type="button" onclick="generateFamilyTabs()">ઉમેરો</button>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-maroon fw-bold" id="addFamilyBtn" onclick="toggleFamilyInput()">
                    <i class="bi bi-person-plus-fill me-1"></i> પરિવાર ઉમેરો
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data" id="wizardForm" novalidate data-api-form data-api-url="{{ route('api.members.update', $member) }}" data-api-method="PUT" data-redirect-template="{{ route('members.show', '__MEMBER__') }}">
        @csrf
        @method('PUT')
        
        <div class="tab-content" id="memberTabsContent">
            <!-- Main Member Pane -->
            <div class="tab-pane fade show active p-5" id="main-pane" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-12">
                        <h4 class="text-maroon border-start border-5 border-maroon ps-3 fw-bold mb-4">મુખ્ય સભ્યની વિગતો</h4>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">નામ <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control form-control-lg bg-light" value="{{ old('first_name', $member->first_name) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">પિતા / પતિનું નામ <span class="text-danger">*</span></label>
                        <input type="text" name="middle_name" class="form-control form-control-lg bg-light" value="{{ old('middle_name', $member->middle_name) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">અટક <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control form-control-lg bg-light" value="{{ old('last_name', $member->last_name) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">માતાનું નામ</label>
                        <input type="text" name="mother_name" class="form-control form-control-lg bg-light" value="{{ old('mother_name', $member->mother_name) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">લિંગ <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select form-select-lg bg-light" required>
                            <option value="Male" {{ old('gender', $member->gender) == 'Male' ? 'selected' : '' }}>પુરુષ</option>
                            <option value="Female" {{ old('gender', $member->gender) == 'Female' ? 'selected' : '' }}>સ્ત્રી</option>
                            <option value="Other" {{ old('gender', $member->gender) == 'Other' ? 'selected' : '' }}>અન્ય</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">જન્મ તારીખ <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control form-control-lg bg-light" value="{{ old('date_of_birth', $member->date_of_birth) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">મોબાઇલ નંબર <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control form-control-lg bg-light" value="{{ old('mobile', $member->mobile) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">ઇમેઇલ</label>
                        <input type="email" name="email" class="form-control form-control-lg bg-light" value="{{ old('email', $member->email) }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">સરનામું <span class="text-danger">*</span></label>
                        <textarea name="address" id="main_address" rows="2" class="form-control form-control-lg bg-light" required>{{ old('address', $member->address) }}</textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">જિલ્લો <span class="text-danger">*</span></label>
                        <input type="text" name="district" id="main_district" class="form-control form-control-lg bg-light" value="{{ old('district', $member->district) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">તાલુકો <span class="text-danger">*</span></label>
                        <input type="text" name="sub_district" id="main_sub_district" class="form-control form-control-lg bg-light" value="{{ old('sub_district', $member->sub_district) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">શહેર / ગામ <span class="text-danger">*</span></label>
                        <input type="text" name="city_village" id="main_city_village" class="form-control form-control-lg bg-light" value="{{ old('city_village', $member->city_village) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">પિનકોડ <span class="text-danger">*</span></label>
                        <input type="text" name="pincode" id="main_pincode" class="form-control form-control-lg bg-light" value="{{ old('pincode', $member->pincode) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">વ્યવસાય</label>
                        <input type="text" name="occupation" class="form-control form-control-lg bg-light" value="{{ old('occupation', $member->occupation) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">વતન</label>
                        <input type="text" name="hometown" class="form-control form-control-lg bg-light" value="{{ old('hometown', $member->hometown) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">ફોટો</label>
                        @if($member->photo)
                            <img src="{{ asset('storage/'.$member->photo) }}" width="40" class="img-thumbnail ms-2 mb-1">
                        @endif
                        <input type="file" name="photo" class="form-control form-control-lg bg-light">
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-flex justify-content-between">
                    <a href="{{ route('members.show', $member) }}" class="btn btn-lg btn-light px-5 fw-bold">કેન્સલ</a>
                    <div class="wizard-nav-btns">
                        <button type="button" class="btn btn-lg btn-maroon px-5 fw-bold next-btn">આગળ વધો <i class="bi bi-arrow-right ms-2"></i></button>
                        <button type="submit" class="btn btn-lg btn-success px-5 fw-bold submit-btn d-none">બધી વિગતો અપડેટ કરો</button>
                    </div>
                </div>
            </div>

            <!-- Existing Members Panes -->
            @foreach($member->children as $index => $child)
                <div class="tab-pane fade p-5" id="family-pane-{{ $index }}" role="tabpanel">
                    <input type="hidden" name="family[{{ $index }}][id]" value="{{ $child->id }}">
                    <div class="row g-4">
                        <div class="col-md-12 d-flex justify-content-between align-items-center mb-2">
                            <h4 class="text-maroon border-start border-5 border-maroon ps-3 fw-bold">પરિવારના સભ્યની વિગતો ({{ $child->member_no }})</h4>
                            <button type="button" class="btn btn-sm btn-outline-info fw-bold" onclick="copyMainAddress({{ $index }})">મુખ્ય સરનામું કોપી</button>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold">નામ <span class="text-danger">*</span></label>
                            <input type="text" name="family[{{ $index }}][first_name]" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.first_name', $child->first_name) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">પિતા / પતિનું નામ <span class="text-danger">*</span></label>
                            <input type="text" name="family[{{ $index }}][middle_name]" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.middle_name', $child->middle_name) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">અટક <span class="text-danger">*</span></label>
                            <input type="text" name="family[{{ $index }}][last_name]" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.last_name', $child->last_name) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">સંબંધ <span class="text-danger">*</span></label>
                            <input type="text" name="family[{{ $index }}][relation]" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.relation', $child->relation) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">લિંગ <span class="text-danger">*</span></label>
                            <select name="family[{{ $index }}][gender]" class="form-select form-select-lg bg-light" required>
                                <option value="Male" {{ old('family.'.$index.'.gender', $child->gender) == 'Male' ? 'selected' : '' }}>પુરુષ</option>
                                <option value="Female" {{ old('family.'.$index.'.gender', $child->gender) == 'Female' ? 'selected' : '' }}>સ્ત્રી</option>
                                <option value="Other" {{ old('family.'.$index.'.gender', $child->gender) == 'Other' ? 'selected' : '' }}>અન્ય</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">જન્મ તારીખ <span class="text-danger">*</span></label>
                            <input type="date" name="family[{{ $index }}][date_of_birth]" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.date_of_birth', $child->date_of_birth) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">મોબાઇલ નંબર</label>
                            <input type="text" name="family[{{ $index }}][mobile]" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.mobile', $child->mobile) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">વ્યવસાય</label>
                            <input type="text" name="family[{{ $index }}][occupation]" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.occupation', $child->occupation) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">સરનામું <span class="text-danger">*</span></label>
                            <textarea name="family[{{ $index }}][address]" id="address_{{ $index }}" rows="1" class="form-control form-control-lg bg-light" required>{{ old('family.'.$index.'.address', $child->address) }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">જિલ્લો <span class="text-danger">*</span></label>
                            <input type="text" name="family[{{ $index }}][district]" id="district_{{ $index }}" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.district', $child->district) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">તાલુકો <span class="text-danger">*</span></label>
                            <input type="text" name="family[{{ $index }}][sub_district]" id="sub_district_{{ $index }}" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.sub_district', $child->sub_district) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">શહેર / ગામ <span class="text-danger">*</span></label>
                            <input type="text" name="family[{{ $index }}][city_village]" id="city_village_{{ $index }}" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.city_village', $child->city_village) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">પિનકોડ <span class="text-danger">*</span></label>
                            <input type="text" name="family[{{ $index }}][pincode]" id="pincode_{{ $index }}" class="form-control form-control-lg bg-light" value="{{ old('family.'.$index.'.pincode', $child->pincode) }}" required>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-lg btn-light px-5 fw-bold prev-btn"><i class="bi bi-arrow-left me-2"></i> પાછળ</button>
                        <div class="wizard-nav-btns">
                            <button type="button" class="btn btn-lg btn-maroon px-5 fw-bold next-btn">આગળ વધો <i class="bi bi-arrow-right ms-2"></i></button>
                            <button type="submit" class="btn btn-lg btn-success px-5 fw-bold submit-btn d-none">બધી વિગતો અપડેટ કરો</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>

<template id="familyFormTemplate">
    <div class="row g-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center mb-2">
            <h4 class="text-success border-start border-5 border-success ps-3 fw-bold">પરિવારના સભ્ય - INDEX (નવો)</h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-info fw-bold" onclick="copyMainAddress(INDEX)">
                    <i class="bi bi-geo-alt-fill me-1"></i> મુખ્ય સરનામું કોપી
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger fw-bold" onclick="removeTab('TAB_ID', 'PANE_ID')">
                    <i class="bi bi-trash-fill me-1"></i> કાઢી નાખો
                </button>
            </div>
        </div>
        
        <div class="col-md-3">
            <label class="form-label fw-bold">નામ <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][first_name]" class="form-control form-control-lg bg-light" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">પિતા / પતિનું નામ <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][middle_name]" class="form-control form-control-lg bg-light" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">અટક <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][last_name]" class="form-control form-control-lg bg-light" value="{{ $member->last_name }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">સંબંધ <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][relation]" class="form-control form-control-lg bg-light" required>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">લિંગ <span class="text-danger">*</span></label>
            <select name="family[INDEX][gender]" class="form-select form-select-lg bg-light" required>
                <option value="">પસંદ કરો</option>
                <option value="Male">પુરુષ</option>
                <option value="Female">સ્ત્રી</option>
                <option value="Other">અન્ય</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">જન્મ તારીખ <span class="text-danger">*</span></label>
            <input type="date" name="family[INDEX][date_of_birth]" class="form-control form-control-lg bg-light" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">મોબાઇલ નંબર</label>
            <input type="text" name="family[INDEX][mobile]" class="form-control form-control-lg bg-light">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">વ્યવસાય</label>
            <input type="text" name="family[INDEX][occupation]" class="form-control form-control-lg bg-light">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-bold">સરનામું <span class="text-danger">*</span></label>
            <textarea name="family[INDEX][address]" id="address_INDEX" rows="1" class="form-control form-control-lg bg-light" required></textarea>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">જિલ્લો <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][district]" id="district_INDEX" class="form-control form-control-lg bg-light" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">તાલુકો <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][sub_district]" id="sub_district_INDEX" class="form-control form-control-lg bg-light" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">શહેર / ગામ <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][city_village]" id="city_village_INDEX" class="form-control form-control-lg bg-light" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">પિનકોડ <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][pincode]" id="pincode_INDEX" class="form-control form-control-lg bg-light" required>
        </div>
    </div>

    <div class="mt-5 pt-4 border-top d-flex justify-content-between">
        <button type="button" class="btn btn-lg btn-light px-5 fw-bold prev-btn"><i class="bi bi-arrow-left me-2"></i> પાછળ</button>
        <div class="wizard-nav-btns">
            <button type="button" class="btn btn-lg btn-maroon px-5 fw-bold next-btn">આગળ વધો <i class="bi bi-arrow-right ms-2"></i></button>
            <button type="submit" class="btn btn-lg btn-success px-5 fw-bold submit-btn d-none">બધી વિગતો અપડેટ કરો</button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script>
    let familyCount = {{ $member->children->count() }};

    document.addEventListener('click', function(e) {
        const nextBtn = e.target.closest('.next-btn');
        if (nextBtn) {
            const activeTab = document.querySelector('#memberTabs .nav-link.active');
            if (activeTab && activeTab.closest('li').nextElementSibling) {
                const nextTab = activeTab.closest('li').nextElementSibling.querySelector('.nav-link');
                if (nextTab) {
                    bootstrap.Tab.getOrCreateInstance(nextTab).show();
                }
            }
        }
        
        const prevBtn = e.target.closest('.prev-btn');
        if (prevBtn) {
            const activeTab = document.querySelector('#memberTabs .nav-link.active');
            if (activeTab && activeTab.closest('li').previousElementSibling) {
                const prevTab = activeTab.closest('li').previousElementSibling.querySelector('.nav-link');
                if (prevTab) {
                    bootstrap.Tab.getOrCreateInstance(prevTab).show();
                }
            }
        }
    });

    document.addEventListener('shown.bs.tab', function() {
        updateButtonVisibility();
    });

    function updateButtonVisibility() {
        const tabs = document.querySelectorAll('#memberTabs .nav-link');
        tabs.forEach((tab, index) => {
            const paneId = tab.getAttribute('data-bs-target');
            const pane = document.querySelector(paneId);
            if (!pane) return;
            
            const isLast = (index === tabs.length - 1);
            const nextBtn = pane.querySelector('.next-btn');
            const submitBtn = pane.querySelector('.submit-btn');
            
            if (isLast) {
                if (nextBtn) nextBtn.classList.add('d-none');
                if (submitBtn) submitBtn.classList.remove('d-none');
            } else {
                if (nextBtn) nextBtn.classList.remove('d-none');
                if (submitBtn) submitBtn.classList.add('d-none');
            }
        });
    }

    function toggleFamilyInput() {
        const section = document.getElementById('familyCountSection');
        const btn = document.getElementById('addFamilyBtn');
        if (section.classList.contains('d-none')) {
            section.classList.remove('d-none');
            btn.innerText = 'કેન્સલ';
        } else {
            section.classList.add('d-none');
            btn.innerText = 'પરિવાર ઉમેરો';
        }
    }

    function generateFamilyTabs() {
        const countInput = document.getElementById('familyCountInput');
        const count = parseInt(countInput.value) || 0;
        if (count < 1) return;

        const tabList = document.getElementById('memberTabs');
        const tabContent = document.getElementById('memberTabsContent');
        const template = document.getElementById('familyFormTemplate').innerHTML;

        for (let i = 0; i < count; i++) {
            const index = familyCount;
            const tabId = `family-tab-new-${index}`;
            const paneId = `family-pane-new-${index}`;

            const newTab = document.createElement('li');
            newTab.className = 'nav-item';
            newTab.role = 'presentation';
            newTab.innerHTML = `<button class="nav-link fw-bold" id="${tabId}" data-bs-toggle="tab" data-bs-target="#${paneId}" type="button" role="tab"><span class="step-num">${index + 2}</span> સભ્ય ${index + 1} (નવો)</button>`;
            tabList.appendChild(newTab);

            const newPane = document.createElement('div');
            newPane.className = 'tab-pane fade p-5';
            newPane.id = paneId;
            newPane.role = 'tabpanel';
            
            let html = template;
            html = html.replace(/INDEX/g, index);
            html = html.replace(/TAB_ID/g, tabId);
            html = html.replace(/PANE_ID/g, paneId);
            
            newPane.innerHTML = html;
            tabContent.appendChild(newPane);
            familyCount++;
        }

        toggleFamilyInput();
        updateButtonVisibility();
        
        const firstNewTab = document.querySelector(`#memberTabs li:nth-last-child(${count}) .nav-link`);
        if (firstNewTab) {
            bootstrap.Tab.getOrCreateInstance(firstNewTab).show();
        }
    }

    function removeTab(tabId, paneId) {
        const tab = document.getElementById(tabId);
        const pane = document.getElementById(paneId);
        if (tab) tab.closest('li').remove();
        if (pane) pane.remove();
        updateButtonVisibility();
        const mainTab = document.querySelector('#main-tab');
        if (mainTab) {
            bootstrap.Tab.getOrCreateInstance(mainTab).show();
        }
    }

    function copyMainAddress(index) {
        const fields = ['address', 'district', 'sub_district', 'city_village', 'pincode'];
        fields.forEach(field => {
            const mainField = document.getElementById(`main_${field}`);
            const familyField = document.getElementById(`${field}_${index}`);
            if (mainField && familyField) {
                familyField.value = mainField.value;
            }
        });
    }

    updateButtonVisibility();
</script>
<style>
    .custom-wizard-tabs { gap: 10px; }
    .custom-wizard-tabs .nav-link {
        color: #6c757d;
        background: #f8f9fa;
        border: 2px solid #eee;
        border-radius: 50px;
        padding: 10px 25px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .step-num {
        background: #dee2e6;
        color: #495057;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    .custom-wizard-tabs .nav-link.active {
        background: var(--primary-maroon) !important;
        color: white !important;
        border-color: var(--primary-maroon) !important;
        box-shadow: 0 4px 15px rgba(128, 0, 0, 0.2);
    }
    .custom-wizard-tabs .nav-link.active .step-num {
        background: white;
        color: var(--primary-maroon);
    }
    .bg-soft-maroon { background-color: rgba(128, 0, 0, 0.02); }
    .btn-maroon { background: var(--primary-maroon); color: white; border: none; }
    .btn-maroon:hover { background: #600000; color: white; }
    .btn-outline-maroon { color: var(--primary-maroon); border: 2px solid var(--primary-maroon); }
    .btn-outline-maroon:hover { background: var(--primary-maroon); color: white; }
    .form-control-lg, .form-select-lg { border: 2px solid #f1f1f1; }
    .form-control-lg:focus, .form-select-lg:focus { border-color: var(--primary-maroon); box-shadow: none; }
    .tab-pane { animation: slideUp 0.4s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush
