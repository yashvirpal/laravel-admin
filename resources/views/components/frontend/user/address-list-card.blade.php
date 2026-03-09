@props(['item' => null])

<div class="card h-100 border shadow-sm rounded-4 card-hover">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <span class="badge rounded-pill px-3 py-2 small fw-semibold
            {{ $item->type === 'billing' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle'}}
            ">
                {{ ucfirst($item->type) }}
            </span>
            @if($item->is_default)
                <span
                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 small fw-semibold">
                    <i class="fas fa-check-circle me-1"></i> Default
                </span>
            @endif

        </div>
        <h5 class="fw-semibold mb-2">
            {{ $item->first_name }} {{ $item->last_name }}
        </h5>

        @if($item->company)
            <p class="mb-1">{{ $item->company }}</p>
        @endif
        <p class="text-muted mb-1">{{ $item->address_line1 }}</p>
        @if($item->address_line2)
            <p class="text-muted mb-1">{{ $item->address_line2 }}</p>
        @endif
        <p class="text-muted mb-1"> {{ $item->city }}, {{ $item->state }} </p>
        @if ($item->zip || $item->country)
            <p class="text-muted mb-2"> {{ $item->zip }}, {{ $item->country }} </p>
        @endif

        <p class="mb-3"> <strong>Phone:</strong> {{ $item->phone }} </p>
        <div class="d-flex justify-content-between align-items-center border-top pt-3">
            <a href="{{ route('profile.addresses.edit', $item) }}"
                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form class="delete-address-form" id="myForm{{ $item->id }}" action="{{ route('profile.addresses.destroy', $item) }}" method="POST"
                onsubmit="return confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>