@extends('layouts.admin')

@section('content')
    @php
        $title = 'Orders List';
        $breadcrumbs = [
            'Home' => route('admin.dashboard'),
            'Orders' => ''
        ];
    @endphp

    <div class="card card-primary card-outline mb-4">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>
                Add Order</a>
        </div>

        <div class="card-body">
            <table id="dataTable" class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order No.</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Order Status</th>
                        <!-- <th>Payment Status</th> -->
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@push('styles')
    <!-- DataTables CSS -->
    <link href="{{ asset('backend/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('backend/js/jquery-3.6.0.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function () {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route("admin.orders.index") !!}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'order_number', name: 'order_no' },
                    { data: 'customer', name: 'user.name', orderable: false, searchable: false },
                    { data: 'total', name: 'total' },
                    { data: 'created', name: 'created_at' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                   // { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush