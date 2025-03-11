<div class='row pl-3 pr-3 pt-3'>
    <div class="col-12 ">
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
        @endif
        @if(session('danger'))
        <div class="alert alert-danger">
            {{ session('danger') }}
        </div>
        @endif
        @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
        @endif
        @if(session('primary'))
        <div class="alert alert-primary">
            {{ session('primary') }}
        </div>
        @endif
        @if(session('light'))
        <div class="alert alert-light">
            {{ session('light') }}
        </div>
        @endif
    </div>
</div>
