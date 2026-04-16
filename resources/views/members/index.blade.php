@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold text-maroon mb-0">તમામ સભ્યોની યાદી</h2>
    </div>
    <div class="d-flex gap-2">
        <button type="button" id="printSelectedBtn" class="btn btn-outline-secondary px-4 py-2 fw-bold d-none" disabled>
            <i class="bi bi-check2-square me-2"></i> પસંદ કરેલ પ્રિન્ટ
        </button>
        <a href="{{ route('members.print.all') }}" target="_blank" class="btn btn-outline-dark px-4 py-2 fw-bold">
            <i class="bi bi-printer me-2"></i> બધા પ્રિન્ટ
        </a>
        <a href="{{ route('members.create') }}" class="btn btn-primary px-4 py-2 fw-bold">
            <i class="bi bi-person-plus-fill me-2"></i> નવો સભ્ય ઉમેરો
        </a>
</div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="p-4 bg-soft-maroon border-bottom">
            <form action="{{ route('members.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-maroon"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="સભ્ય નંબર, નામ અથવા મોબાઈલ નંબર શોધો..." value="{{ request('search') }}">
                        <button class="btn btn-maroon px-4" type="submit">શોધો</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-2">
                        @if(request('search'))
                            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">ફિલ્ટર ક્લિયર કરો</a>
                        @endif
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-maroon dropdown-toggle d-flex align-items-center gap-2" type="button" id="columnPickerDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <i class="bi bi-layout-three-columns"></i> કોલમ પસંદ કરો
                            </button>
                            <div class="dropdown-menu p-3 shadow-lg border-0" aria-labelledby="columnPickerDropdown" style="min-width: 250px;">
                                <h6 class="dropdown-header px-0 mb-2 text-maroon fw-bold border-bottom pb-2">રિપોર્ટમાં કઈ કોલમ રાખવી છે?</h6>
                                @php
                                    $columns = [
                                        'member_no' => 'સભ્ય નં.',
                                        'full_name' => 'નામ',
                                        'family_no' => 'પરિવાર નં.',
                                        'mobile' => 'મોબાઇલ',
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
                                        'is_main' => 'સભ્ય પ્રકાર'
                                    ];
                                    $defaultColumns = ['member_no', 'full_name', 'mobile', 'city_village', 'children_count'];
                                @endphp
                                @foreach($columns as $key => $label)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input column-checkbox" type="checkbox" value="{{ $key }}" id="col_{{ $key }}" {{ in_array($key, $defaultColumns) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="col_{{ $key }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <a href="{{ route('members.export.excel', request()->all()) }}" id="excelExportBtn" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        <a href="{{ route('members.print.all', request()->all()) }}" id="pdfExportBtn" target="_blank" class="btn btn-danger d-flex align-items-center gap-2 shadow-sm">
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
                updateLinks(); // Initial call
            });
        </script>
        @endpush

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="membersTable">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 48px;">
                            <input type="checkbox" id="selectAllMembers">
                        </th>
                        <th class="ps-4 col-member_no">સભ્ય નં.</th>
                        <th class="col-full_name">નામ</th>
                        <th class="col-family_no d-none">પરિવાર નં.</th>
                        <th class="col-mobile">મોબાઇલ નંબર</th>
                        <th class="col-city_village">શહેર / ગામ</th>
                        <th class="col-mother_name d-none">માતાનું નામ</th>
                        <th class="col-gender d-none">લિંગ</th>
                        <th class="col-occupation d-none">વ્યવસાય</th>
                        <th class="col-hometown d-none">વતન</th>
                        <th class="col-address d-none">સરનામું</th>
                        <th class="col-district d-none">જિલ્લો</th>
                        <th class="col-sub_district d-none">તાલુકો</th>
                        <th class="col-date_of_birth d-none">જન્મ તારીખ</th>
                        <th class="text-center col-children_count">પરિવારની સંખ્યા</th>
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
                            <td class="col-city_village">{{ $member->city_village }}</td>
                            <td class="col-mother_name d-none">{{ $member->mother_name }}</td>
                            <td class="col-gender d-none">{{ $member->gender }}</td>
                            <td class="col-occupation d-none">{{ $member->occupation }}</td>
                            <td class="col-hometown d-none">{{ $member->hometown }}</td>
                            <td class="col-address d-none">{{ $member->address }}</td>
                            <td class="col-district d-none">{{ $member->district }}</td>
                            <td class="col-sub_district d-none">{{ $member->sub_district }}</td>
                            <td class="col-date_of_birth d-none">{{ $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center col-children_count">
                                <span class="text-dark px-3">{{ $member->children_count }}</span>
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
</script>
@endsection
