@extends('layouts.admin')

@section('content')
    @php
        $isEdit = isset($product) && $product->exists;
        $title = $isEdit ? 'Edit Product' : 'Create Product';
        $breadcrumbs = [
            'Home' => route('admin.dashboard'),
            'Products' => route('admin.products.index'),
            $title => ''
        ];
    @endphp

    <div class="card card-primary card-outline mb-4">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left-circle me-1"></i> Back To List
            </a>
        </div>

        <div class="card-body">
            <form action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
                method="POST" id="myForm" enctype="multipart/form-data">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="row">

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $product->title ?? '') }}"
                            class="form-control" required>
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}" class="form-control">
                        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>



                    {{-- Brand --}}
                    {{-- <div class="mb-3 col-md-6">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">-- Select Brand --</option>
                            @foreach($brands as $id => $name)
                            <option value="{{ $id }}" @selected(old('brand_id', $product->brand_id ?? '') == $id)>{{ $name
                                }}</option>
                            @endforeach
                        </select>
                        @error('brand_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div> --}}
                    <div id="simple_product_fields" class=" row mb-3 col-md-12">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                                class="form-control">
                            @error('sku') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}"
                                class="form-control">
                            @error('stock') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Regular Price</label>
                            <input type="number" step="0.01" name="regular_price"
                                value="{{ old('regular_price', $product->regular_price ?? 0) }}" class="form-control">
                            @error('regular_price') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Sale Price</label>
                            <input type="number" step="0.01" name="sale_price"
                                value="{{ old('sale_price', $product->sale_price ?? '') }}" class="form-control">
                            @error('sale_price') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control"
                            rows="2">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                        @error('short_description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                            rows="5">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Banner Image</label>
                        <input type="file" name="banner" class="form-control">
                        @if($isEdit && $product->banner)
                            <img src="{{ image_url('banner', $product->banner, 'small') }}" class="mt-2" width="60">
                        @endif
                        @error('banner') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Banner Alt</label>
                        <input type="text" name="banner_alt" value="{{ old('banner_alt', $product->banner_alt ?? '') }}"
                            class="form-control">
                        @error('banner_alt') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control">
                        @if($isEdit && $product->image)
                            <img src="{{ image_url('product', $product->image, 'small') }}" class="mt-2" width="60">
                        @endif
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Image Alt</label>
                        <input type="text" name="image_alt" value="{{ old('image_alt', $product->image_alt ?? '') }}"
                            class="form-control">
                        @error('image_alt') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label">Gallery Images</label>
                        <input type="file" name="gallery[]" class="form-control" multiple>
                        @error('gallery') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        @error('gallery.*') <small class="text-danger d-block">{{ $message }}</small> @enderror

                        @if($isEdit && $product->galleries->count())
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach($product->galleries as $img)
                                    <div class="text-center">
                                        <img src="{{ image_url('product_gallery', $img->image, 'small') }}" width="80"
                                            class="img-thumbnail mb-1">
                                        <div>
                                            <label class="small text-muted">
                                                <input type="checkbox" name="remove_gallery[]" value="{{ $img->id }}"> Remove
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @php
                        function renderCategoryOptions($categories, $selectedIds = [], $prefix = '', $parentId = null)
                        {
                            foreach ($categories as $category) {
                                $isSelected = in_array($category->id, $selectedIds) ? 'selected' : '';

                                // add data-parent ONLY for children
                                $dataParent = $parentId ? "data-parent='{$parentId}'" : '';

                                echo "<option value='{$category->id}' {$isSelected} {$dataParent}>
                                                                                                                                                                                                                                                                                                                                                                                                                                                        {$prefix}{$category->title}
                                                                                                                                                                                                                                                                                                                                                                                                                                                    </option>";

                                if ($category->children->isNotEmpty()) {
                                    renderCategoryOptions(
                                        $category->children,
                                        $selectedIds,
                                        $prefix . '— ',
                                        $category->id // 👈 pass current as parent
                                    );
                                }
                            }
                        }
                    @endphp

                    @php
                        $selectedCategories = old('product_category_ids', isset($product) ? $product->categories->pluck('id')->toArray() : []);
                    @endphp

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Categories</label>
                        <select name="product_category_ids[]" class="form-select select2" multiple>
                            @php renderCategoryOptions($categories, $selectedCategories); @endphp
                        </select>
                        @error('product_category_ids')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    <div class="mb-3 col-md-6">
                        <label class="form-label">Tags</label>
                        <select name="product_tag_ids[]" class="form-select select2" multiple>
                            @foreach($tags as $id => $tag)
                                <option value="{{ $id }}" @selected(in_array($id, old('product_tag_ids', $isEdit ? $product->tags->pluck('id')->toArray() : [])))>
                                    {{ $tag }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_tag_ids') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title ?? '') }}"
                            class="form-control">
                        @error('meta_title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords"
                            value="{{ old('meta_keywords', $product->meta_keywords ?? '') }}" class="form-control">
                        @error('meta_keywords') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control"
                            rows="3">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                        @error('meta_description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- <div class="mb-3 col-md-6">
                        <label class="form-label">SEO Image</label>
                        <input type="file" name="seo_image" class="form-control">
                        @if($isEdit && $product->seo_image)
                        <img src="{{ image_url('seo', $product->seo_image, 'small') }}" class="mt-2" width="60">
                        @endif
                        @error('seo_image') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Canonical URL</label>
                        <input type="text" name="canonical_url"
                            value="{{ old('canonical_url', $product->canonical_url ?? '') }}" class="form-control">
                        @error('canonical_url') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Custom Field</label>
                        <input type="text" name="custom_field"
                            value="{{ old('custom_field', $product->custom_field ?? '') }}" class="form-control">
                        @error('custom_field') <small class="text-danger">{{ $message }}</small> @enderror
                    </div> --}}

                    <div class="mb-3 col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" class="form-check-input" value="1" id="is_featured" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                            <label for="is_featured" class="form-check-label">Featured Product</label>
                        </div>
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_special" value="0">
                            <input type="checkbox" name="is_special" class="form-check-input" value="1" id="is_special" {{ old('is_special', $product->is_special ?? false) ? 'checked' : '' }} />
                            <label for="is_special" class="form-check-label">Special Product</label>
                        </div>
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ old('status', $product->status ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="statusSwitch">Active</label>
                        </div>
                    </div>

                    <div class="mb-3 col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="has_variants" value="0">
                            <input type="checkbox" name="has_variants" class="form-check-input" value="1" id="has_variants"
                                {{ old('has_variants', $product->has_variants ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_variants">This product has variants (Size / Color /
                                etc.)</label>
                        </div>
                    </div>
                    <hr>

                    <div id="variant_section" class="mb-3 col-md-12">
                        <h5>Product Attributes</h5>
                        <input type="hidden" id="product_id" value="{{ $product->id ?? '' }}">
                        <div class="row">
                            @foreach ($attributes as $attribute)
                                @php
                                    $selected = isset($product) ? $product->variants->flatMap->values->pluck('id')->toArray() : [];
                                @endphp
                                <div class="col-md-6 mb-3 d-flex flex-column">
                                    <label class="form-label fw-bold d-block" for="att_{{ $attribute->slug }}">{{ $attribute->name }}</label>
                                    <select class="form-select attribute-select select2 w-100" id="att_{{ $attribute->slug }}"
                                        name="attributes[{{ $attribute->id }}][]" multiple>
                                        @foreach($attribute->values as $v)
                                            <option value="{{ $v->id }}" @selected(in_array($v->id, $selected))>{{ $v->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-warning btn-sm" id="btn-generate">Generate Variants</button>
                        <hr>
                        <table class="table table-bordered" id="variant-table">
                            <thead>
                                <tr>
                                    <th>Variant</th>
                                    <th>SKU</th>
                                    <th>Regular Price</th>
                                    <th>Sale Price</th>
                                    <th>Stock</th>
                                    <th>Image</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants ?? [] as $variant)
                                    <tr data-id="{{ $variant->id }}">
                                        <td>{{ $variant->values->pluck('name')->join(' - ') }}</td>
                                        <td><input name="variants[{{ $variant->id }}][sku]" value="{{ $variant->sku }}"></td>
                                        <td><input name="variants[{{ $variant->id }}][regular_price]"
                                                value="{{ $variant->regular_price }}"></td>
                                        <td><input name="variants[{{ $variant->id }}][sale_price]"
                                                value="{{ $variant->sale_price }}"></td>
                                        <td><input name="variants[{{ $variant->id }}][stock]" value="{{ $variant->stock }}">
                                        <td><input type="file" name="variants[{{ $variant->id }}][image]"
                                                value="{{ $variant->image }}">
                                                @if($variant->image)
                                                    <img src="{{ image_url('product_variant', $variant->image, 'small') }}" class="mt-2" width="60"/>
                                                @endif
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-variant"
                                                data-id="{{ $variant->id }}">X</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                    <button type="submit" class="btn btn-primary btn-sm">
                        {{ $isEdit ? 'Update Product' : 'Create Product' }}
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('components.admin.select2')
@push('scripts')
    <script>
        function toggleVariantMode() {
            if ($('#has_variants').is(':checked')) {
                $('#simple_product_fields').hide(); $('#variant_section').show();
            } else {
                $('#simple_product_fields').show(); $('#variant_section').hide();
            }
        }
        document.querySelector('#has_variants').addEventListener('change', toggleVariantMode);
        toggleVariantMode(); // on load

    </script>
    <script>
        document.getElementById('btn-generate').addEventListener('click', function () {
            let selectedAttributes = [];

            // 1. Collect all selected attribute names and values
            document.querySelectorAll('.attribute-select').forEach(select => {
                let values = Array.from(select.selectedOptions).map(opt => ({
                    id: opt.value,
                    name: opt.text
                }));
                if (values.length > 0) {
                    selectedAttributes.push(values);
                }
            });

            if (selectedAttributes.length === 0) {
                alert("Please select at least one attribute value.");
                return;
            }

            // 2. Generate Cartesian Product (Combinations)
            let combinations = selectedAttributes.reduce((a, b) =>
                a.flatMap(d => b.map(e => [d, e].flat()))
            );

            let tbody = document.querySelector('#variant-table tbody');

            // Optional: If you want to keep existing rows that still match selections:
            // We can map existing rows by their combination string to prevent overwriting data.
            let existingRows = {};
            tbody.querySelectorAll('tr').forEach(tr => {
                let comboText = tr.cells[0].innerText.trim();
                existingRows[comboText] = tr;
            });

            tbody.innerHTML = ''; // Clear table to rebuild based on new selection

            combinations.forEach((combo, index) => {
                // Ensure combo is always an array even if only one attribute is selected
                let comboArray = Array.isArray(combo) ? combo : [combo];
                let comboNames = comboArray.map(c => c.name).join(' - ');
                let comboIds = comboArray.map(c => c.id).join(',');

                if (existingRows[comboNames]) {
                    // Re-append existing row if it matches the new combination
                    tbody.appendChild(existingRows[comboNames]);
                } else {
                    // Create New Row for new combinations (using a temporary index as key)
                    let tempId = 'new_' + Date.now() + index;
                    let newRow = `
                            <tr data-id="${tempId}">
                                <td>
                                    ${comboNames}
                                    <input type="hidden" name="variants[${tempId}][attribute_values]" value="${comboIds}">
                                </td>
                                <td><input name="variants[${tempId}][sku]" class="form-control" placeholder="SKU"></td>
                                <td><input name="variants[${tempId}][regular_price]" class="form-control" placeholder="0.00"></td>
                                <td><input name="variants[${tempId}][sale_price]" class="form-control" placeholder="0.00"></td>
                                <td><input name="variants[${tempId}][stock]" class="form-control" value="0"></td>
                                <td><input type="file" name="variants[${tempId}][image]" class="form-control"></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-variant">X</button></td>
                            </tr>`;
                    tbody.insertAdjacentHTML('beforeend', newRow);
                }
            });
        });

        // Event delegation for remove button
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-variant')) {
                e.target.closest('tr').remove();
            }
        });


    </script>



    <script>

        $('.select2').on('change', function () {
            const $select = $(this);
            let selected = $select.val() || [];

            // Loop through all selected child options
            $select.find('option:selected').each(function () {
                const parentId = $(this).data('parent');

                if (parentId && !selected.includes(parentId.toString())) {
                    selected.push(parentId.toString());
                }
            });

            // Update selection only if changed
            $select.val(selected).trigger('change.select2');
        });

    </script>


<script>
    document.getElementById('myForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.currentTarget;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Clear previous errors
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Disable button to prevent double clicks
    submitBtn.disabled = true;
  //  submitBtn.innerText = 'Saving...';

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                // Note: Do NOT set Content-Type header when using FormData; 
                // the browser sets it automatically with the boundary string.
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (response.status === 422) {
            // Handle Validation Errors
            console.log('Validation Errors:', result.errors);
            handleValidationErrors(result.errors);
        } else if (response.ok) {
            // Success!
            alert('Product saved successfully!');
            window.location.href = '/admin/products'; // Redirect if needed
        } else {
            throw new Error(result.message || 'Something went wrong');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message);
    } finally {
        submitBtn.disabled = false;
     //   submitBtn.innerText = 'Create Product';
    }
});

/**
 * Automatically maps Laravel validation errors to the inputs
 */
function handleValidationErrors(errors) {
    
    for (const [key, messages] of Object.entries(errors)) {
        // Convert dot notation (variants.1.sku) to HTML name attribute (variants[1][sku])
        let fieldName = key.replace(/\.(\w+)/g, '[$1]'); 
        let input = document.querySelector(`[name="${fieldName}"]`);

        if (input) {
            input.classList.add('is-invalid');
            let errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.innerText = messages[0];
            input.parentElement.appendChild(errorDiv);
        }
    }
}
    </script>


@endpush