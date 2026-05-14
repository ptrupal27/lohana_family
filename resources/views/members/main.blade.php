@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold text-maroon mb-0 text-center text-md-start">મુખ્ય સભ્યોની યાદી</h2>
    </div>
    <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
        <button type="button" class="btn btn-outline-maroon px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#labelConfigModal">
            <i class="bi bi-tag me-2"></i> લેબલ પ્રિન્ટ
        </button>
        <a href="{{ route('members.print.all', ['is_main' => 1]) }}" target="_blank" class="btn btn-outline-dark px-3 py-2 fw-bold">
            <i class="bi bi-printer me-2"></i> બધા પ્રિન્ટ
        </a>
        <a href="{{ route('members.create', ['return_page' => request('page'), 'from_main' => 1]) }}" class="btn btn-primary px-3 py-2 fw-bold">
            <i class="bi bi-person-plus-fill me-2"></i> નવો સભ્ય ઉમેરો
        </a>
    </div>
</div>



<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="p-4 bg-soft-maroon border-bottom">
            <form action="{{ route('members.main') }}" method="GET" class="row g-3">
                <div class="col-lg-3 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-geo-alt text-maroon"></i>
                        </span>
                        <select name="area" class="form-select border-start-0 ps-0" onchange="this.form.submit()">
                            <option value="">તમામ એરિયા</option>
                            @foreach($areas as $areaName)
                                <option value="{{ $areaName }}" {{ request('area') == $areaName ? 'selected' : '' }}>{{ $areaName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-maroon"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="શોધો..." value="{{ request('search') }}">
                        <button class="btn btn-maroon px-3" type="submit">શોધો</button>
                    </div>
                </div>
                <div class="col-lg-5 col-md-3">
                    <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-2">
                        @if(request('search') || request('area') || request('columns'))
                            <a href="{{ route('members.main') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 shadow-sm">
                                <i class="bi bi-x-circle"></i> ફિલ્ટર ક્લિયર
                            </a>
                        @endif

                        <div class="dropdown">
                            <button class="btn btn-outline-maroon btn-sm dropdown-toggle d-flex align-items-center gap-2 shadow-sm" type="button" id="columnPickerDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <i class="bi bi-layout-three-columns"></i> કોલમ
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" aria-labelledby="columnPickerDropdown" style="width: 300px; max-width: 90vw; border-radius: 12px; overflow: hidden;">
                                <div class="px-3 py-2 border-bottom bg-light d-flex justify-content-between align-items-center">
                                    <span class="text-maroon fw-bold small">કોલમ પસંદ કરો</span>
                                    <button type="button" id="resetColumnsBtn" class="btn btn-sm btn-link text-maroon p-0 text-decoration-none fw-bold" style="font-size: 0.75rem;">ડિફોલ્ટ</button>
                                </div>
                                <div class="p-3 dropdown-menu-scrollable shadow-inner">
                                    <div class="row g-2">
                                        @php
                                            $columns = [
                                                'member_no' => 'સભ્ય નં.',
                                                'full_name' => 'નામ',
                                                'family_no' => 'પરિવાર નં.',
                                                'mobile' => 'મોબાઇલ',
                                                'alternate_mobile' => 'બીજો મોબાઇલ',
                                                'city_village' => 'શહેર / ગામ',
                                                'mother_name' => 'માતાનું નામ',
                                                'gender' => 'લિંગ',
                                                'blood_group' => 'બ્લડ ગ્રુપ',
                                                'occupation' => 'વ્યવસાય',
                                                'hometown' => 'વતન',
                                                'address' => 'સરનામું',
                                                'area' => 'એરિયા',
                                                'date_of_birth' => 'જન્મ તારીખ',
                                                'children_count' => 'પરિવારની સંખ્યા',
                                            ];
                                            $defaultColumns = ['member_no', 'full_name', 'family_no', 'mobile', 'area', 'children_count'];
                                        @endphp
                                        @foreach($columns as $key => $label)
                                            <div class="col-6">
                                                <div class="form-check custom-check">
                                                    <input class="form-check-input column-checkbox" type="checkbox" value="{{ $key }}" id="col_{{ $key }}" {{ in_array($key, $defaultColumns) ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100 cursor-pointer" for="col_{{ $key }}">{{ $label }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('members.export.excel', array_merge(request()->all(), ['is_main' => 1])) }}" id="excelExportBtn" class="btn btn-success btn-sm d-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        <a href="{{ route('members.print.all', array_merge(request()->all(), ['is_main' => 1])) }}" id="pdfExportBtn" target="_blank" class="btn btn-danger btn-sm d-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive table-sticky-header">
            <table class="table table-hover align-middle mb-0 py-2">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 48px;">
                            <input type="checkbox" id="selectAllMembers">
                        </th>
                        <th class="col-member_no">સભ્ય નં.</th>
                        <th class="col-full_name">નામ</th>
                        <th class="col-family_no">પરિવાર નં.</th>
                        <th class="col-mobile">મોબાઇલ નંબર</th>
                        <th class="col-alternate_mobile d-none">બીજો મોબાઇલ</th>
                        <th class="col-city_village d-none">શહેર / ગામ</th>
                        <th class="col-mother_name d-none">માતાનું નામ</th>
                        <th class="col-gender d-none">લિંગ</th>
                        <th class="col-blood_group d-none">બ્લડ ગ્રુપ</th>
                        <th class="col-occupation d-none">વ્યવસાય</th>
                        <th class="col-hometown d-none">વતન</th>
                        <th class="col-address d-none">સરનામું</th>
                        <th class="col-area">એરિયા</th>
                        <th class="col-date_of_birth d-none">જન્મ તારીખ</th>
                        <th class="text-center col-children_count">પરિવાર સંખ્યા</th>
                        <th class="text-end pe-4">એક્શન</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" class="member-select-checkbox" value="{{ $member->member_no }}">
                            </td>
                            <td class="col-member_no">
                                <span class="badge bg-soft-maroon text-maroon border px-3">{{ $member->member_no }}</span>
                            </td>
                            <td class="col-full_name">
                                <div class="fw-bold text-maroon">{{ $member->full_name }}</div>
                            </td>
                            <td class="col-family_no">{{ $member->family_no }}</td>
                            <td class="col-mobile">{{ $member->mobile }}</td>
                            <td class="col-alternate_mobile d-none">{{ $member->alternate_mobile ?? '-' }}</td>
                            <td class="col-city_village d-none">{{ $member->city_village }}</td>
                            <td class="col-mother_name d-none">{{ $member->mother_name }}</td>
                            <td class="col-gender d-none">{{ $member->gender }}</td>
                            <td class="col-blood_group d-none">{{ $member->blood_group ?? '-' }}</td>
                            <td class="col-occupation d-none">{{ $member->occupation }}</td>
                            <td class="col-hometown d-none">{{ $member->hometown }}</td>
                            <td class="col-address d-none">{{ $member->address }}</td>
                            <td class="col-area">{{ $member->area }}</td>
                            <td class="col-date_of_birth d-none">{{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center col-children_count">
                                <span class="badge bg-light text-dark border px-3">{{ $member->children_count + 1 }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('members.show', [$member, 'return_page' => request('page'), 'from_main' => 1]) }}" class="btn btn-icon btn-view" title="જુઓ">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('members.edit', [$member, 'return_page' => request('page'), 'from_main' => 1]) }}" class="btn btn-icon btn-edit" title="એડિટ">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('api.members.destroy', $member) }}" method="POST" class="d-inline" data-api-delete-form data-api-url="{{ route('api.members.destroy', $member) }}" data-reload="true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-delete" title="ડિલીટ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="17" class="text-center py-5 text-muted">કોઈ મુખ્ય સભ્યો મળી આવ્યા નથી.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($members->hasPages())
            <div class="card-footer bg-white border-0 py-3 border-top">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="text-muted small">
                        Showing <strong>{{ $members->firstItem() }}</strong> to <strong>{{ $members->lastItem() }}</strong> of <strong>{{ $members->total() }}</strong> results
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="pagination-wrapper">
                            {{ $members->links('pagination::bootstrap-5') }}
                        </div>
                        <div class="goto-page d-flex align-items-center gap-2 ms-md-3 border-start ps-md-3">
                            <span class="small text-muted">Page</span>
                            <input type="number" id="gotoPageInput" class="form-control form-control-sm text-center" style="width: 60px;" min="1" max="{{ $members->lastPage() }}" value="{{ $members->currentPage() }}">
                            <button type="button" onclick="goToPage()" class="btn btn-sm btn-maroon fw-bold">Go</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function goToPage() {
        const page = document.getElementById('gotoPageInput').value;
        if (page && page >= 1 && page <= {{ $members->lastPage() }}) {
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        }
    }
    
    document.getElementById('gotoPageInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            goToPage();
        }
    });
</script>
@endpush

<!-- Label Configuration Modal (Copied from dashboard) -->
<div class="modal fade" id="labelConfigModal" tabindex="-1" aria-labelledby="labelConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-maroon text-white border-0">
                <h5 class="modal-title fw-bold" id="labelConfigModalLabel">લેબલ પ્રિન્ટ સેટિંગ્સ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="labelPrintForm" action="{{ route('members.print.labels') }}" method="GET" target="_blank">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">પહોળાઈ (Width) (mm)</label>
                            <input type="number" name="width" class="form-control" value="80" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">ઊંચાઈ (Height) (mm)</label>
                            <input type="number" name="height" class="form-control" value="50" required>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="group_by_family" id="groupByFamily" value="1">
                                <label class="form-check-label fw-bold" for="groupByFamily">પરિવાર મુજબ ગ્રુપ કરો</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">ક્યા સભ્યો માટે પ્રિન્ટ કરવું છે?</label>
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="print_type" id="printMainOnlyType" value="main_only" checked>
                                    <label class="form-check-label" for="printMainOnlyType">ફક્ત મુખ્ય સભ્યો</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="print_type" id="printAllType" value="all">
                                    <label class="form-check-label" for="printAllType">બધા સભ્યો</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="print_type" id="printSelectedType" value="selected">
                                    <label class="form-check-label" for="printSelectedType">પસંદ કરેલ સભ્યો</label>
                                </div>
                            </div>
                            <div id="selectedCountWarning" class="alert alert-warning mt-2 py-2 small d-none">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> તમે કોઈ સભ્ય પસંદ કર્યો નથી.
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <label class="form-label fw-bold mb-2">કઈ વિગતો છાપવી છે?</label>
                            <div class="row g-2">
                                @php
                                    $labelOptions = [
                                        'address' => 'સરનામું',
                                        'mobile' => 'મોબાઇલ',
                                        'alternate_mobile' => 'બીજો મોબાઇલ',
                                        'city_village' => 'શહેર / ગામ',
                                        'occupation' => 'વ્યવસાય',
                                        'hometown' => 'વતન',
                                    ];
                                @endphp
                                @foreach($labelOptions as $key => $label)
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="columns[]" value="{{ $key }}" id="label_col_{{ $key }}" checked>
                                            <label class="form-check-label" for="label_col_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div id="selectedMembersContainer"></div>
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">બંધ કરો</button>
                    <button type="submit" class="btn btn-maroon px-4 fw-bold">પ્રિન્ટ શરૂ કરો</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllMembers = document.getElementById('selectAllMembers');
        const memberCheckboxes = Array.from(document.querySelectorAll('.member-select-checkbox'));
        const labelPrintForm = document.getElementById('labelPrintForm');
        const selectedMembersContainer = document.getElementById('selectedMembersContainer');
        const printSelectedType = document.getElementById('printSelectedType');
        const selectedCountWarning = document.getElementById('selectedCountWarning');
        
        // Column Selection Logic
        const checkboxes = document.querySelectorAll('.column-checkbox');
        const excelBtn = document.getElementById('excelExportBtn');
        const pdfBtn = document.getElementById('pdfExportBtn');

        const baseUrlExcel = '{{ route("members.export.excel") }}';
        const baseUrlPdf = '{{ route("members.print.all") }}';

        function updateLinks() {
            const selected = Array.from(checkboxes)
                .filter(c => c.checked)
                .map(c => c.value);

            // Live toggle table columns
            const allPossibleKeys = Array.from(checkboxes).map(c => c.value);
            allPossibleKeys.forEach(key => {
                const elements = document.querySelectorAll(`.col-${key}`);
                if (selected.includes(key)) {
                    elements.forEach(el => el.classList.remove('d-none'));
                } else {
                    elements.forEach(el => el.classList.add('d-none'));
                }
            });

            // Get current params
            const pageParams = new URL(window.location.href).searchParams;
            
            pageParams.delete('columns[]');
            pageParams.delete('columns');
            pageParams.set('is_main', '1');

            selected.forEach(col => {
                pageParams.append('columns[]', col);
            });

            excelBtn.href = baseUrlExcel + '?' + pageParams.toString();
            pdfBtn.href = baseUrlPdf + '?' + pageParams.toString();
        }

        checkboxes.forEach(c => c.addEventListener('change', updateLinks));
        
        const resetBtn = document.getElementById('resetColumnsBtn');
        const defaultCols = @json($defaultColumns);
        
        resetBtn?.addEventListener('click', function() {
            checkboxes.forEach(c => {
                c.checked = defaultCols.includes(c.value);
            });
            updateLinks();
        });

        if (selectAllMembers) {
            selectAllMembers.addEventListener('change', function () {
                memberCheckboxes.forEach((checkbox) => {
                    checkbox.checked = selectAllMembers.checked;
                });
            });
        }

        memberCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', function () {
                const allChecked = memberCheckboxes.length > 0 && memberCheckboxes.every((item) => item.checked);
                if (selectAllMembers) {
                    selectAllMembers.checked = allChecked;
                }
            });
        });

        labelPrintForm.addEventListener('submit', function (e) {
            selectedMembersContainer.innerHTML = '';
            
            if (printSelectedType.checked) {
                const selectedCheckboxValues = memberCheckboxes
                    .filter((checkbox) => checkbox.checked)
                    .map(checkbox => checkbox.value);

                if (selectedCheckboxValues.length === 0) {
                    e.preventDefault();
                    selectedCountWarning.classList.remove('d-none');
                    return;
                }

                selectedCheckboxValues.forEach((value) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_members[]';
                    input.value = value;
                    selectedMembersContainer.appendChild(input);
                });
            }
            
            selectedCountWarning.classList.add('d-none');
            bootstrap.Modal.getInstance(document.getElementById('labelConfigModal')).hide();
        });

        document.querySelectorAll('input[name="print_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedCountWarning.classList.add('d-none');
            });
        });

        updateLinks(); // Initial call
    });
</script>
@endpush
@endsection
