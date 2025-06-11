<div class="row mb-3">
  <div class="col-md-3">
    <label>Date Filter</label>
    <input 
      type="text" 
      class="flatpickr-daterange form-control" 
      placeholder="Pick date range" 
      id="cuti-date-filter"
    >
  </div>

  <div class="col-md-3">
    <label>Karyawan</label>
    <select id="karyawan-filter" class="form-control">
      <option value=''>-- Semua Data --</option>
      @foreach ($users as $user)
        <option value="{{ $user->uuid }}">{{ $user->userInformation->nama }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-3">
    <label>Status</label>
    <select id="status-filter" class="form-control">
      <option value=''>-- Semua Status --</option>
      @foreach ($status_cutis as $status)
        <option value="{{ $status->uuid }}">{{ $status->nama }}</option>
      @endforeach
    </select>
  </div>
</div>



<script>
  document.addEventListener("DOMContentLoaded", function () {
    const today = new Date().toISOString().split('T')[0];
    flatpickr("#cuti-date-filter", {
      mode: "range",
      dateFormat: "Y-m-d",
      defaultDate: [today, today]
    });
  });
</script>
