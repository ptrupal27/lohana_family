@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <h2 class="fw-bold text-maroon">ડેશબોર્ડ</h2>
        <p class="text-muted small">ટ્રસ્ટની વર્તમાન સ્થિતિનો સારાંશ</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card card-maroon shadow-sm border-0 overflow-hidden">
            <div class="card-body p-3 position-relative">
                <div class="position-absolute top-0 end-0 p-2 opacity-25">
                    <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                </div>
                <h6 class="fw-bold mb-1">કુલ સભ્યો</h6>
                <h3 class="fw-bold mb-0">{{ $totalMembers }}</h3>
                <div class="mt-1">
                    <small style="font-size: 0.75rem;" class="opacity-75">મેઈન રજીસ્ટર્ડ સભ્યો</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-gold shadow-sm border-0 overflow-hidden">
            <div class="card-body p-3 position-relative">
                <div class="position-absolute top-0 end-0 p-2 opacity-25">
                    <i class="bi bi-person-hearts" style="font-size: 2.5rem;"></i>
                </div>
                <h6 class="fw-bold mb-1">કુલ પરિવારના સભ્યો</h6>
                <h3 class="fw-bold mb-0">{{ $totalFamilyMembers }}</h3>
                <div class="mt-1">
                    <small style="font-size: 0.75rem;" class="opacity-75">પરિવારના સભ્યોની કુલ સંખ્યા</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-crimson shadow-sm border-0 overflow-hidden">
            <div class="card-body p-3 position-relative">
                <div class="position-absolute top-0 end-0 p-2 opacity-25">
                    <i class="bi bi-house-door-fill" style="font-size: 2.5rem;"></i>
                </div>
                <h6 class="fw-bold mb-1">કુલ પરિવારો</h6>
                <h3 class="fw-bold mb-0">{{ $totalMembers }}</h3>
                <div class="mt-1">
                    <small style="font-size: 0.75rem;" class="opacity-75">રજીસ્ટર્ડ કુટુંબોની સંખ્યા</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-maroon">સભ્યોની સર્ચ</h5>
                <form action="{{ route('dashboard') }}" method="GET" class="d-flex gap-2" style="width: 400px;">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control border-maroon" placeholder="નામ, મોબાઈલ કે સભ્ય નંબર શોધો..." value="{{ request('search') }}">
                        <button class="btn btn-maroon" type="submit">શોધો</button>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">સભ્ય નં.</th>
                                <th>નામ</th>
                                <th>મોબાઇલ</th>
                                <th>સરનામું</th>
                                <th class="text-center">કુલ સભ્યો</th>
                                <th>વ્યવસાય</th>
                                <th class="text-end pe-4">એક્શન</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMembers as $member)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-soft-maroon text-maroon border" style="font-size: 0.75rem;">{{ $member->member_no }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('members.show', $member) }}" class="fw-bold text-maroon text-decoration-none hover-underline" style="font-size: 0.9rem;">{{ $member->full_name }}</a>
                                    </td>
                                    <td style="font-size: 0.9rem;">{{ $member->mobile }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 180px; font-size: 0.85rem;" title="{{ $member->address }}">
                                            {{ $member->address }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border rounded-pill px-3" style="font-size: 0.75rem;">{{ $member->children_count + 1 }}</span>
                                    </td>
                                    <td style="font-size: 0.9rem;">{{ $member->occupation ?? '-' }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-maroon px-3 py-1" style="font-size: 0.85rem;">જુઓ</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">કોઈ સભ્યો મળ્યા નથી.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
