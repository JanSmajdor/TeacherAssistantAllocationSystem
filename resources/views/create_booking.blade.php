@extends('layouts.master')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Create a Booking Request</div>
                <form method="POST" action="{{ route('create_booking') }}">
                    @csrf

                    <!-- Module Selection -->
                    <div class="card-body">
                        <h4>Module Information</h4>
                        <div class="mb-3">
                            <label for="module_id" class="form-label">Module:</label>
                            <select name="module_id" id="module_id" class="form-select" required>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- Booking Type -->
                    <div class="card-body">
                        <h4>Booking Details</h4>
                        <div class="mb-3">
                            <label for="booking_type" class="form-label">Booking Type:</label>
                            <select name="booking_type" id="booking_type" class="form-select" required>
                                <option value="Lecture">Lecture</option>
                                <option value="Lab">Lab</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Marking">Marking</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="num_tas_requested" class="form-label">Number of TAs Requested:</label>
                            <input type="number" name="num_tas_requested" id="num_tas_requested" class="form-control" min="1" value="1" required>
                        </div>
                    </div>

                    <hr>

                    <!-- Date and Time -->
                    <div class="card-body">
                        <h4>Date and Time of Booking</h4>
                        <div class="mb-3">
                            <label for="date_from" class="form-label">Start Date and Time:</label>
                            <input type="datetime-local" name="date_from" id="date_from" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_to" class="form-label">End Date and Time:</label>
                            <input type="datetime-local" name="date_to" id="date_to" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="repeat_weeks" class="form-label">Repeat for (Weeks):</label>
                            <input type="number" name="repeat_weeks" id="repeat_weeks" class="form-control" min="1" placeholder="Enter number of weeks (optional)">
                            <small class="text-muted">Leave blank if this booking is for a single occurance.</small>
                        </div>
                    </div>

                    <hr>

                    <!-- Site -->
                    <div class="card-body">
                        <h4>Site Information</h4>
                        <div class="mb-3">
                            <label for="site" class="form-label">Site:</label>
                            <select name="site" id="site" class="form-select" required>
                                <option value="Site 1">Site 1</option>
                                <option value="Site 2">Site 2</option>
                                <option value="Site 3">Site 3</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- Submit Button -->
                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-success">Submit Booking Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Flatpickr -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#date_from', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            minDate: "today",
        });

        flatpickr('#date_to', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            minDate: "today",
        });
    });
</script>
@endsection