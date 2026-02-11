@extends('layouts.admin')

@section('content')
    @php
        $title = isset($attribute) && $attribute->exists ? 'Edit Product Attribute' : 'Create Product Attribute';
    @endphp

    <div class="card card-primary card-outline mb-4">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left-circle me-1"></i> Back To List
            </a>
        </div>

        <div class="card-body">
            <form
                action="{{ isset($attribute) && $attribute->exists ? route('admin.product-attributes.update', $attribute->id) : route('admin.product-attributes.store') }}"
                method="POST">
                @csrf

                @if(isset($attribute) && $attribute->exists)
                    @method('PUT')
                @endif

                <div class="row">

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name', $attribute->name ?? '') }}"
                            class="form-control" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $attribute->slug ?? '') }}"
                            class="form-control">
                    </div>

                    <div class="mb-3 col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" id="status" name="status" value="1" class="form-check-input" {{ old('status', $attribute->status ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($attribute) && $attribute->exists ? 'Update' : 'Create' }}
                </button>
            </form>
        </div>
    </div>
@endsection

@include('components.admin.select2')