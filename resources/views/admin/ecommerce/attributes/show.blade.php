@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>{{ $productAttribute->name }} (Attribute)</h4>

            <a href="{{ route('admin.product-attribute-values.create', $productAttribute->id) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Attribute Value
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Attribute Details
            </div>

            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <th stylee="width: 150px;">ID</th>
                        <td>{{ $productAttribute->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $productAttribute->name }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{!! status_badge($productAttribute->status) !!}</td>
                    </tr>
                </table>
            </div>





        </div>

        <div class="card">
            <div class="card-header">
                Attribute Values
            </div>
            <div class="card-body">
                @if ($productAttribute->values->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productAttribute->values as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ status_badge($value->status) }}</td>
                                    <td>
                                        <a href="{{ route('admin.product-attribute-values.edit', [$productAttribute->id, $value->id]) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <form
                                            action="{{ route('admin.product-attribute-values.destroy', [$productAttribute->id, $value->id]) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No values found for this attribute.</p>
                @endif
            </div>
        </div>

    </div>
@endsection