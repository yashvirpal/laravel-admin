@extends('layouts.admin')

@section('content')
    @php
        $title = isset($coupon) && $coupon->exists ? 'Edit Coupon' : 'Create Coupon';
    @endphp
   
    <div class="card card-primary card-outline mb-4">
        <div class="card-header d-flex justify-content-end align-items-center">
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-arrow-left-circle me-1"></i> Back To List
            </a>
        </div>

        <div class="card-body">
            <form
                action="{{ isset($coupon) && $coupon->exists ? route('admin.coupons.update', $coupon->id) : route('admin.coupons.store') }}"
                method="POST">
                @csrf
                @if(isset($coupon) && $coupon->exists)
                    @method('PUT')
                @endif

                <div class="row">
                    {{-- Title --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control"
                            value="{{ old('title', $coupon->title ?? '') }}" required>
                    </div>

                    {{-- Code --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $coupon->code ?? '') }}"
                            required>
                    </div>

                    {{-- Start / End --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Start At</label>
                        <input type="text" name="starts_at" class="form-control datetime"
                            value="{{ old('starts_at', isset($coupon->starts_at) ? $coupon->starts_at->timezone(config('app.timezone'))->format('Y-m-d H:i') : '') }}">

                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Valid Until</label>
                        <input type="text" name="expires_at" class="form-control datetime"
                            value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at->timezone(config('app.timezone'))->format('Y-m-d H:i') : '') }}">

                    </div>

                    {{-- Status --}}
                    <div class="mb-3 col-md-6">
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="status" value="0" />
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ old('status', $coupon->status ?? true) ? 'checked' : '' }} />
                            <label class="form-check-label" for="statusSwitch">Active</label>
                        </div>
                    </div>
                </div>

                {{-- --------------------- RULES --------------------- --}}
                <h5 class="mt-4">Rules</h5>
                <table class="table table-bordered" id="rules-table">
                    <thead>
                        <tr>
                            <th>Condition</th>
                            <th>Product / Category</th>
                            <th>Min Qty / Min Value</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(old('rules', $coupon->rules ?? []))
                            @foreach(old('rules', $coupon->rules ?? []) as $i => $rule)
                                <tr>
                                    <td>
                                        <select name="rules[{{ $i }}][condition]" class="form-select rule-condition">
                                            @foreach(['product', 'category', 'cart_subtotal', 'cart_quantity'] as $cond)
                                                <option value="{{ $cond }}" @selected(($rule['condition'] ?? $rule->condition ?? '') == $cond)>{{ ucfirst($cond) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="rules[{{ $i }}][product_id]" class="form-select product-select">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" @selected(($rule['product_id'] ?? $rule->product_id ?? '') == $product->id)>{{ $product->title }}</option>
                                            @endforeach
                                        </select>
                                        <select name="rules[{{ $i }}][category_id]" class="form-select category-select mt-1">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" @selected(($rule['category_id'] ?? $rule->category_id ?? '') == $category->id)>{{ $category->title }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="rules[{{ $i }}][min_value]" class="form-control mb-1"
                                            placeholder="Min Value" value="{{ $rule['min_value'] ?? $rule->min_value ?? '' }}">
                                        <input type="number" name="rules[{{ $i }}][min_qty]" class="form-control"
                                            placeholder="Min Qty" value="{{ $rule['min_qty'] ?? $rule->min_qty ?? '' }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove-rule">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <button type="button" class="btn btn-success mb-3" id="add-rule">Add Rule</button>

                {{-- --------------------- ACTIONS --------------------- --}}
                <h5 class="mt-4">Actions</h5>
                <table class="table table-bordered" id="actions-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Product</th>
                            <th>Value / Quantity</th>
                            <th>Buy / Get Qty (BOGO)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(old('actions', $coupon->actions ?? []))
                            @foreach(old('actions', $coupon->actions ?? []) as $i => $action)
                                <tr>
                                    <td>
                                        <select name="actions[{{ $i }}][action]" class="form-select action-type">
                                            @foreach(['fixed_discount', 'percentage_discount', 'free_product', 'discount_product', 'bogo'] as $a)
                                                <option value="{{ $a }}" @selected(($action['action'] ?? $action->action ?? '') == $a)>
                                                    {{ ucfirst(str_replace('_', ' ', $a)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="actions[{{ $i }}][product_id]" class="form-select">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" @selected(($action['product_id'] ?? $action->product_id ?? '') == $product->id)>{{ $product->title }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="actions[{{ $i }}][value]" class="form-control mb-1"
                                            placeholder="Value" value="{{ $action['value'] ?? $action->value ?? '' }}">
                                        <input type="number" name="actions[{{ $i }}][quantity]" class="form-control"
                                            placeholder="Quantity" value="{{ $action['quantity'] ?? $action->quantity ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" name="actions[{{ $i }}][buy_qty]" class="form-control mb-1"
                                            placeholder="Buy Qty" value="{{ $action['buy_qty'] ?? $action->buy_qty ?? '' }}">
                                        <input type="number" name="actions[{{ $i }}][get_qty]" class="form-control"
                                            placeholder="Get Qty" value="{{ $action['get_qty'] ?? $action->get_qty ?? '' }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger remove-action">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <button type="button" class="btn btn-success mb-3" id="add-action">Add Action</button>

                {{-- Submit --}}
                <div class="mt-4">
                    <button type="submit"
                        class="btn btn-primary">{{ isset($coupon) && $coupon->exists ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @include('components.admin.datetimepicker')
@endsection

@push('scripts')
    @php
        $ruleIndex = is_array(old('rules')) ? count(old('rules')) : ($coupon->rules->count() ?? 0);
        $actionIndex = is_array(old('actions')) ? count(old('actions')) : ($coupon->actions->count() ?? 0);
    @endphp

    <script>
        let ruleIndex = {{ $ruleIndex }};
        let actionIndex = {{ $actionIndex }};

        // Add Rule
        $('#add-rule').click(function () {
            let row = `<tr>
                            <td>
                                <select name="rules[${ruleIndex}][condition]" class="form-select rule-condition">
                                    <option value="product">Product</option>
                                    <option value="category">Category</option>
                                    <option value="cart_subtotal">Cart Subtotal</option>
                                    <option value="cart_quantity">Cart Quantity</option>
                                </select>
                            </td>
                            <td>
                                <select name="rules[${ruleIndex}][product_id]" class="form-select product-select">
                                    <option value="">Select Product</option>
                                    @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach
                                </select>
                                <select name="rules[${ruleIndex}][category_id]" class="form-select category-select mt-1">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->title }}</option>@endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="rules[${ruleIndex}][min_value]" class="form-control mb-1" placeholder="Min Value">
                                <input type="number" name="rules[${ruleIndex}][min_qty]" class="form-control" placeholder="Min Qty">
                            </td>
                            <td><button type="button" class="btn btn-danger remove-rule">Remove</button></td>
                        </tr>`;
            $('#rules-table tbody').append(row);
            ruleIndex++;
        });

        // Remove Rule
        $(document).on('click', '.remove-rule', function () {
            $(this).closest('tr').remove();
        });

        // Add Action
        $('#add-action').click(function () {
            let row = `<tr>
                            <td>
                                <select name="actions[${actionIndex}][action]" class="form-select action-type">
                                    <option value="fixed_discount">Fixed Discount</option>
                                    <option value="percentage_discount">Percentage Discount</option>
                                    <option value="free_product">Free Product</option>
                                    <option value="discount_product">Discount Product</option>
                                    <option value="bogo">BOGO</option>
                                </select>
                            </td>
                            <td>
                                <select name="actions[${actionIndex}][product_id]" class="form-select">
                                    <option value="">Select Product</option>
                                    @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="actions[${actionIndex}][value]" class="form-control mb-1" placeholder="Value">
                                <input type="number" name="actions[${actionIndex}][quantity]" class="form-control" placeholder="Quantity">
                            </td>
                            <td>
                                <input type="number" name="actions[${actionIndex}][buy_qty]" class="form-control mb-1" placeholder="Buy Qty">
                                <input type="number" name="actions[${actionIndex}][get_qty]" class="form-control" placeholder="Get Qty">
                            </td>
                            <td><button type="button" class="btn btn-danger remove-action">Remove</button></td>
                        </tr>`;
            $('#actions-table tbody').append(row);
            actionIndex++;
        });

        // Remove Action
        $(document).on('click', '.remove-action', function () {
            $(this).closest('tr').remove();
        });
    </script>
@endpush