<div class="row">
    <div class="col-12 col-md-3">
        <label>Role</label>
        <br>
        <select class="group-filter form-control form-select" id="role-filter" name="role-filter">
            {!! each_option($roles, 'name', null) !!}
        </select>
    </div>
</div>
