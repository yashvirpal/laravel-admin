@props([
    'fullWidth' => false, // default
])
@php
    $colClass = $fullWidth ? 'col-md-12' : 'col-md-6';
@endphp
<div class="customised-form">
    {!!  !$fullWidth ? "<h4>For Energize, We require these details</h4>" : "" !!}
    <form>
        <div class="row">
            <div class="{{ $colClass }}">
                <div class="input-group">
                    <label>Name</label>
                    <input type="text" name="">
                </div>
            </div>
            <div class="{{ $colClass }}">
                <div class="input-group">
                    <label>Date Of Birth</label>
                    <input type="text" name="">
                </div>
            </div>
            <div class="{{ $colClass }}">
                <div class="input-group">
                    <label>Problem</label>
                    <input type="text" name="">
                </div>
            </div>
            <div class="{{ $colClass }}">
                <div class="input-group">
                    <label>Specific Problem</label>
                    <textarea></textarea>
                </div>
            </div>
            <!-- <div class="{{ $colClass }}">
                <div class="input-group">
                    <button>Submit</button>
                </div>
            </div> -->
        </div>
    </form>
</div>