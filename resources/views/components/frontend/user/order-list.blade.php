@props(['items'])
<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if($items->count() > 0)
                    @foreach($items as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ dateFormat($order->created_at) }}</td>
                            <td>{{ currencyformat($order->total) }}</td>
                            <td>
                                @php
                                    $status = paymentStatusBadge($order->status);
                                @endphp
                                <span class="badge rounded-pill {{ $status['class'] }}">
                                    <i class="fas {{ $status['icon'] }} me-1"></i>
                                    {{ $status['text'] }}
                                </span>

                            </td>
                            <td>
                                <a href="{{ route('profile.orders.show', $order->id) }}"
                                    class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">
                            <p class="text-center p-3">No orders found.</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    {{-- Pagination Links --}}
    @if(method_exists($items, 'links'))
        <div class="p-3">
            {{ $items->links() }}
        </div>
    @endif
</div>