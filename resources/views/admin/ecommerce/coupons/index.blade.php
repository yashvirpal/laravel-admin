@extends('layouts.admin')

@section('content')
    @php
        $title = 'Coupons';
        $breadcrumbs = ['Home' => route('admin.dashboard'), 'Coupons' => ''];
    @endphp

    <div class="card card-primary card-outline mb-4">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Coupon
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Valid From</th>
                            <th>Valid Until</th>
                            <th>Rules / Actions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('backend/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <style>
        .badge-rule {
            background-color: #17a2b8;
            margin: 2px;
            cursor: default;
        }

        .badge-action {
            background-color: #28a745;
            margin: 2px;
            cursor: default;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('backend/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/js/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(function () {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route("admin.coupons.index") !!}',
                    dataSrc: function (json) {
                        return json.data;
                    }
                },
                order: [[0, 'desc']],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'code', name: 'code' },
                    {
                        data: 'type', name: 'type', render: function (data) {
                            return data ? data.charAt(0).toUpperCase() + data.slice(1) : '';
                        }
                    },
                    {
                        data: 'value', name: 'value', render: function (data, type, row) {
                            if (!data) return '';
                            if (row.type === 'percentage') return data + '%';
                            return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(data);
                        }
                    },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    {
                        data: 'starts_at', name: 'starts_at', render: function (data) {
                            return data ? new Date(data).toLocaleString() : '';
                        }
                    },
                    {
                        data: 'expires_at', name: 'expires_at', render: function (data) {
                            return data ? new Date(data).toLocaleString() : '';
                        }
                    },
                    {
                        data: 'rules_actions', name: 'rules_actions', orderable: false, searchable: false, render: function (data, type, row) {
                            let html = '';

                            // Rules badges
                            if (data?.rules && Array.isArray(data.rules)) {
                                data.rules.forEach(r => {
                                    let text = r.condition;
                                    if (r.product_name) text += ` (${r.product_name})`;
                                    if (r.category_name) text += ` (${r.category_name})`;
                                    html += `<span class="badge badge-rule" title="${text}">${r.condition}</span>`;
                                });
                            }

                            // Actions badges
                            if (data?.actions && Array.isArray(data.actions)) {
                                data.actions.forEach(a => {
                                    let text = a.action;
                                    if (a.product_name) text += ` (${a.product_name})`;
                                    if (a.value) text += ` : ${a.value}`;
                                    if (a.quantity) text += ` x${a.quantity}`;
                                    html += `<span class="badge badge-action" title="${text}">${a.action}</span>`;
                                });
                            }

                            return html || '-';
                        }
                    },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ],
                drawCallback: function () {
                    $('[title]').tooltip({ placement: 'top' });
                }
            });
        });
    </script>
@endpush