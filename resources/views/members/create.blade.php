@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('members.index') }}">સભ્યોની યાદી</a></li>
                <li class="breadcrumb-item active" aria-current="page">નવો સભ્ય ઉમેરો</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-maroon">સભ્ય રજીસ્ટ્રેશન</h2>
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
                    <i class="bi bi-people-fill me-1"></i> પરિવાર ઉમેરો
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data" id="wizardForm" novalidate data-api-form data-api-url="{{ route('api.members.store') }}" data-redirect-template="{{ route('members.show', '__MEMBER__') }}">
        @csrf
        
        <div class="tab-content" id="memberTabsContent">
            <!-- Main Member Pane -->
            <div class="tab-pane fade show active p-5" id="main-pane" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-12">
                        <h4 class="text-maroon border-start border-5 border-maroon ps-3 fw-bold mb-4">મુખ્ય સભ્યની વિગતો</h4>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">નામ <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control form-control-lg bg-light" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">પિતા / પતિનું નામ <span class="text-danger">*</span></label>
                        <input type="text" name="middle_name" class="form-control form-control-lg bg-light" value="{{ old('middle_name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">અટક <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control form-control-lg bg-light" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">માતાનું નામ</label>
                        <input type="text" name="mother_name" class="form-control form-control-lg bg-light">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">લિંગ <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select form-select-lg bg-light" required>
                            <option value="">પસંદ કરો</option>
                            <option value="Male">પુરુષ</option>
                            <option value="Female">સ્ત્રી</option>
                            <option value="Other">અન્ય</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">જન્મ તારીખ <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control form-control-lg bg-light" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">મોબાઇલ નંબર <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control form-control-lg bg-light" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">ઇમેઇલ</label>
                        <input type="email" name="email" class="form-control form-control-lg bg-light">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">સરનામું <span class="text-danger">*</span></label>
                        <textarea name="address" id="main_address" rows="2" class="form-control form-control-lg bg-light" required></textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">જિલ્લો <span class="text-danger">*</span></label>
                        <input type="text" name="district" id="main_district" class="form-control form-control-lg bg-light" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">તાલુકો <span class="text-danger">*</span></label>
                        <input type="text" name="sub_district" id="main_sub_district" class="form-control form-control-lg bg-light" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">શહેર / ગામ <span class="text-danger">*</span></label>
                        <input type="text" name="city_village" id="main_city_village" class="form-control form-control-lg bg-light" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">પિનકોડ <span class="text-danger">*</span></label>
                        <input type="text" name="pincode" id="main_pincode" class="form-control form-control-lg bg-light" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">વ્યવસાય</label>
                        <input type="text" name="occupation" class="form-control form-control-lg bg-light">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">વતન</label>
                        <input type="text" name="hometown" class="form-control form-control-lg bg-light">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">ફોટો</label>
                        <input type="file" name="photo" class="form-control form-control-lg bg-light">
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-flex justify-content-between">
                    <a href="{{ route('members.index') }}" class="btn btn-lg btn-light px-5 fw-bold">કેન્સલ</a>
                    <div class="wizard-nav-btns">
                        <button type="button" class="btn btn-lg btn-maroon px-5 fw-bold next-btn">આગળ વધો <i class="bi bi-arrow-right ms-2"></i></button>
                        <button type="submit" class="btn btn-lg btn-success px-5 fw-bold submit-btn d-none">રજીસ્ટ્રેશન પૂર્ણ કરો</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="familyFormTemplate">
    <div class="row g-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center mb-2">
            <h4 class="text-success border-start border-5 border-success ps-3 fw-bold">પરિવારના સભ્ય - INDEX</h4>
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
            <input type="text" name="family[INDEX][last_name]" class="form-control form-control-lg bg-light" value="LAST_NAME" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">સંબંધ <span class="text-danger">*</span></label>
            <input type="text" name="family[INDEX][relation]" class="form-control form-control-lg bg-light" placeholder="જેમ કે પુત્ર, પત્ની..." required>
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
            <button type="submit" class="btn btn-lg btn-success px-5 fw-bold submit-btn d-none">રજીસ્ટ્રેશન પૂર્ણ કરો</button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script>
    let familyCount = 0;

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
        const mainLastNameInput = document.querySelector('input[name="last_name"]');
        const mainLastName = mainLastNameInput ? mainLastNameInput.value : '';

        for (let i = 0; i < count; i++) {
            familyCount++;
            const tabId = `family-tab-${familyCount}`;
            const paneId = `family-pane-${familyCount}`;

            const newTab = document.createElement('li');
            newTab.className = 'nav-item';
            newTab.role = 'presentation';
            newTab.innerHTML = `<button class="nav-link fw-bold" id="${tabId}" data-bs-toggle="tab" data-bs-target="#${paneId}" type="button" role="tab"><span class="step-num">${familyCount + 1}</span> સભ્ય ${familyCount}</button>`;
            tabList.appendChild(newTab);

            const newPane = document.createElement('div');
            newPane.className = 'tab-pane fade p-5';
            newPane.id = paneId;
            newPane.role = 'tabpanel';
            
            let html = template;
            html = html.replace(/INDEX/g, familyCount);
            html = html.replace(/TAB_ID/g, tabId);
            html = html.replace(/PANE_ID/g, paneId);
            html = html.replace(/LAST_NAME/g, mainLastName);
            
            newPane.innerHTML = html;
            tabContent.appendChild(newPane);
        }

        toggleFamilyInput();
        updateButtonVisibility();
        
        const firstNewTab = document.getElementById(`family-tab-${familyCount - count + 1}`);
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
