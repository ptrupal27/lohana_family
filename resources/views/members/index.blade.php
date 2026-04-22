@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <h2 class="fw-bold text-maroon mb-0 text-center text-md-start">તમામ સભ્યોની યાદી</h2>
    </div>
    <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
        <button type="button" id="printSelectedBtn" class="btn btn-outline-secondary px-3 py-2 fw-bold d-none" disabled>
            <i class="bi bi-check2-square me-2"></i> પસંદ કરેલ પ્રિન્ટ
        </button>
        <button type="button" class="btn btn-outline-maroon px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#labelConfigModal">
            <i class="bi bi-tag me-2"></i> લેબલ પ્રિન્ટ
        </button>
        <a href="{{ route('members.print.all') }}" target="_blank" class="btn btn-outline-dark px-3 py-2 fw-bold">
            <i class="bi bi-printer me-2"></i> બધા પ્રિન્ટ
        </a>
        <a href="{{ route('members.create') }}" class="btn btn-primary px-3 py-2 fw-bold">
            <i class="bi bi-person-plus-fill me-2"></i> નવો સભ્ય ઉમેરો
        </a>
    </div>
</div>


<!-- Label Configuration Modal -->
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
                            <div class="d-flex gap-3 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="print_type" id="printAllType" value="all" checked>
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

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="p-4 bg-soft-maroon border-bottom">
            <form action="{{ route('members.index') }}" method="GET" class="row g-3">
                <div class="col-lg-4 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-maroon"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="શોધો..." value="{{ request('search') }}">
                        <button class="btn btn-maroon px-3" type="submit">શોધો</button>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-2">
                        @if(request('search') || request('columns'))
                            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 shadow-sm">
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
                                                'district' => 'જિલ્લો',
                                                'sub_district' => 'તાલુકો',
                                                'date_of_birth' => 'જન્મ તારીખ',
                                                'children_count' => 'પરિવારની સંખ્યા',
                                                'is_main' => 'સભ્ય પ્રકાર'
                                            ];
                                            $defaultColumns = ['member_no', 'full_name', 'mobile', 'address'];
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

                        <a href="{{ route('members.export.excel', request()->all()) }}" id="excelExportBtn" class="btn btn-success btn-sm d-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        <a href="{{ route('members.print.all', request()->all()) }}" id="pdfExportBtn" target="_blank" class="btn btn-danger btn-sm d-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </form>

        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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

                    // Get current search and other params from the PAGE url
                    const pageParams = new URL(window.location.href).searchParams;
                    
                    // Clear any previous columns
                    pageParams.delete('columns[]');
                    pageParams.delete('columns');

                    // Add the currently selected columns
                    selected.forEach(col => {
                        pageParams.append('columns[]', col);
                    });

                    // Update both buttons
                    excelBtn.href = baseUrlExcel + '?' + pageParams.toString();
                    pdfBtn.href = baseUrlPdf + '?' + pageParams.toString();
                }

                checkboxes.forEach(c => c.addEventListener('change', updateLinks));
                
                // Reset to default functionality
                const resetBtn = document.getElementById('resetColumnsBtn');
                const defaultCols = @json($defaultColumns);
                
                resetBtn.addEventListener('click', function() {
                    checkboxes.forEach(c => {
                        c.checked = defaultCols.includes(c.value);
                    });
                    updateLinks();
                });

                updateLinks(); // Initial call
            });
        </script>
        @endpush

        <div class="table-responsive table-sticky-header shadow-sm rounded-bottom">
            <table class="table table-hover align-middle mb-0 py-2" id="membersTable">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 48px;">
                            <input type="checkbox" id="selectAllMembers">
                        </th>
                        <th class="ps-4 col-member_no">સભ્ય નં.</th>
                        <th class="col-full_name">નામ</th>
                        <th class="col-family_no d-none">પરિવાર નં.</th>
                        <th class="col-mobile">મોબાઇલ નંબર</th>
                        <th class="col-alternate_mobile d-none">બીજો મોબાઇલ</th>
                        <th class="col-city_village d-none">શહેર / ગામ</th>
                        <th class="col-mother_name d-none">માતાનું નામ</th>
                        <th class="col-gender d-none">લિંગ</th>
                        <th class="col-blood_group d-none">બ્લડ ગ્રુપ</th>
                        <th class="col-occupation d-none">વ્યવસાય</th>
                        <th class="col-hometown d-none">વતન</th>
                        <th class="col-address">સરનામું</th>
                        <th class="col-district d-none">જિલ્લો</th>
                        <th class="col-sub_district d-none">તાલુકો</th>
                        <th class="col-date_of_birth d-none">જન્મ તારીખ</th>
                        <th class="text-center col-children_count d-none">પરિવારની સંખ્યા</th>
                        <th class="col-is_main d-none">સભ્ય પ્રકાર</th>
                        <th class="text-end pe-4">એક્શન</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" class="member-select-checkbox" value="{{ $member->member_no }}">
                            </td>
                            <td class="ps-4 col-member_no">
                                <span class="badge bg-soft-maroon text-maroon border px-3">{{ $member->member_no }}</span>
                            </td>
                            <td class="col-full_name">
                                <div class="fw-bold {{ $member->is_main ? 'text-maroon' : 'text-dark' }}">{{ $member->full_name }}</div>
                            </td>
                            <td class="col-family_no d-none">{{ $member->family_no }}</td>
                            <td class="col-mobile">{{ $member->mobile }}</td>
                            <td class="col-alternate_mobile d-none">{{ $member->alternate_mobile ?? '-' }}</td>
                            <td class="col-city_village d-none">{{ $member->city_village }}</td>
                            <td class="col-mother_name d-none">{{ $member->mother_name }}</td>
                            <td class="col-gender d-none">{{ $member->gender }}</td>
                            <td class="col-blood_group d-none">{{ $member->blood_group ?? '-' }}</td>
                            <td class="col-occupation d-none">{{ $member->occupation }}</td>
                            <td class="col-hometown d-none">{{ $member->hometown }}</td>
                            <td class="col-address">{{ $member->address }}</td>
                            <td class="col-district d-none">{{ $member->district }}</td>
                            <td class="col-sub_district d-none">{{ $member->sub_district }}</td>
                            <td class="col-date_of_birth d-none">{{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center col-children_count d-none">
                                <span class="text-dark px-3">{{ $member->children_count + 1 }}</span>
                            </td>
                            <td class="col-is_main d-none">{{ $member->is_main ? 'મુખ્ય સભ્ય' : 'પરિવાર સભ્ય' }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('members.show', $member) }}" class="btn btn-icon btn-view" title="જુઓ">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-icon btn-edit" title="એડિટ">
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
                            <td colspan="17" class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">કોઈ સભ્યો મળી આવ્યા નથી.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($members->hasPages())
            <div class="p-4 border-top">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>

<form id="printSelectedForm" action="{{ route('members.print.all') }}" method="GET" target="_blank" class="d-none"></form>
<script>
    const selectAllMembers = document.getElementById('selectAllMembers');
    const memberCheckboxes = Array.from(document.querySelectorAll('.member-select-checkbox'));
    const printSelectedBtn = document.getElementById('printSelectedBtn');
    const printSelectedForm = document.getElementById('printSelectedForm');

    function refreshSelectedPrintState() {
        const selectedCount = memberCheckboxes.filter((checkbox) => checkbox.checked).length;
        printSelectedBtn.disabled = selectedCount === 0;
        printSelectedBtn.classList.toggle('d-none', selectedCount === 0);
    }

    if (selectAllMembers) {
        selectAllMembers.addEventListener('change', function () {
            memberCheckboxes.forEach((checkbox) => {
                checkbox.checked = selectAllMembers.checked;
            });
            refreshSelectedPrintState();
        });
    }

    memberCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            const allChecked = memberCheckboxes.length > 0 && memberCheckboxes.every((item) => item.checked);
            if (selectAllMembers) {
                selectAllMembers.checked = allChecked;
            }
            refreshSelectedPrintState();
        });
    });

    printSelectedBtn?.addEventListener('click', function () {
        printSelectedForm.innerHTML = '';

        memberCheckboxes
            .filter((checkbox) => checkbox.checked)
            .forEach((checkbox) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_members[]';
                input.value = checkbox.value;
                printSelectedForm.appendChild(input);
            });

        printSelectedForm.submit();
    });

    // Label Print Modal Logic
    const labelPrintForm = document.getElementById('labelPrintForm');
    const selectedMembersContainer = document.getElementById('selectedMembersContainer');
    const printSelectedType = document.getElementById('printSelectedType');
    const selectedCountWarning = document.getElementById('selectedCountWarning');

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
</script>
@endsection
