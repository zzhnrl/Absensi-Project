<div class="row">
    <div class="col-12 col-md-3">
        <label>Role</label>
        <select class="form-control form-select" id="role-filter" name="role-filter">
            <option value="">-- Semua Role --</option>
            @foreach ($roles as $role)
                <option value="{{ $role->uuid }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<table id="datatable" class="table table-striped mt-4" style="width: 100%" data-role-id="{{ auth()->user()->userRole->role_id }}"></table>
