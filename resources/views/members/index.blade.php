@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold text-maroon mb-0">સભ્યોની યાદી</h2>
        <p class="text-muted">કુલ રજીસ્ટર્ડ મુખ્ય સભ્યો (પરિવારના વડા)</p>
    </div>
    <a href="{{ route('members.create') }}" class="btn btn-primary px-4 py-2 fw-bold">
        <i class="bi bi-person-plus-fill me-2"></i> નવો સભ્ય ઉમેરો
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="p-4 bg-soft-maroon border-bottom">
            <form action="{{ route('members.index') }}" method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-maroon"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="સભ્ય નંબર, નામ અથવા મોબાઈલ નંબર શોધો..." value="{{ request('search') }}">
                        <button class="btn btn-primary px-4" type="submit">શોધો</button>
                    </div>
                </div>
                <div class="col-md-4">
                    @if(request('search'))
                        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary w-100">ફિલ્ટર ક્લિયર કરો</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">સભ્ય નં.</th>
                        <th>નામ</th>
                        <th>મોબાઇલ નંબર</th>
                        <th>શહેર / ગામ</th>
                        <th class="text-center">પરિવારના સભ્યો</th>
                        <th class="text-end pe-4">એક્શન</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-soft-maroon text-maroon border px-3">{{ $member->member_no }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $member->full_name }}</div>
                                <small class="text-muted">{{ $member->occupation }}</small>
                            </td>
                            <td>{{ $member->mobile }}</td>
                            <td>{{ $member->city_village }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-3">{{ $member->children_count }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-primary" title="જુઓ">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-outline-secondary" title="એડિટ">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('members.destroy', $member) }}" method="POST" class="d-inline" onsubmit="return confirm('શું તમે ખરેખર આ સભ્યને કાઢી નાખવા માંગો છો?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="ડિલીટ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
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

<style>
    .text-maroon { color: var(--primary-maroon); }
    .bg-soft-maroon { background-color: rgba(128, 0, 0, 0.03); }
    .btn-outline-primary {
        color: var(--primary-maroon);
        border-color: var(--primary-maroon);
    }
    .btn-outline-primary:hover {
        background-color: var(--primary-maroon);
        color: white;
    }
</style>
@endsection
