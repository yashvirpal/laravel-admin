@extends('layouts.admin')

@section('content')
    @php
        $title = isset($value) && $value->exists
            ? 'Edit Product Attribute Value'
            : 'Create Product Attribute Value';
    @endphp

    <div class="card card-primary card-outline mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">{{ $title }}</h6>

            <a href="{{ route('admin.product-attributes.show', $product_attribute->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left-circle me-1"></i> Back To Values
            </a>
        </div>

        <div class="card-body">

            <form action="{{ isset($product_attribute_value) && $product_attribute_value->exists
        ? route('admin.product-attribute-values.update', [$product_attribute->id, $product_attribute_value->id])
        : route('admin.product-attribute-values.store', $product_attribute->id) 
                                    }}" method="POST">

                @csrf
                @if(isset($product_attribute_value) && $product_attribute_value->exists)
                    @method('PUT')
                @endif

                <div class="row">

                    <div class="mb-3 col-md-12">
                        <label class="form-label">Attribute</label>
                        <input type="text" class="form-control" value="{{ $product_attribute->name }}" disabled>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Value Name</label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $product_attribute_value->name ?? '') }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control"
                            value="{{ old('slug', $product_attribute_value->slug ?? '') }}">
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="form-check form-switch mt-4">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" id="status" name="status" value="1" class="form-check-input" {{ old('status', $product_attribute_value->status ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-2">
                    {{ isset($value) && $value->exists ? 'Update Value' : 'Create Value' }}
                </button>

            </form>
        </div>
    </div>
@endsection

@include('components.admin.select2')