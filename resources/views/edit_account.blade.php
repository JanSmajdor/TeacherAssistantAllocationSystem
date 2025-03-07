@extends('layouts.master')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Edit Account Details</div>
                <form id="edit-account-form" action="{{ route('edit_account') }}" method='POST'>
                    @csrf
                    @method('PUT')

                    <!-- Personal Information -->
                    <div class="card-body">
                        <h4>Personal Information</h4>
                        <div class="mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input id="firstname" type="text" class="form-control" name="firstname" value="{{ $ta_details->first_name }}" required disabled>
                        </div>

                        <div class="mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input id="lastname" type="text" class="form-control" name="lastname" value="{{ $ta_details->last_name }}" required disabled>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ $ta_details->email }}" disabled>
                        </div>

                        <a href="{{ route('password.request') }}" class="btn btn-warning">Reset Password</a>
                    </div>

                    <hr>

                    <!-- Availability -->
                    <div class="card-body">
                        <h4>Availability</h4>
                        <div id="choice-container">
                            @forelse ($availability as $avail)
                            <div class="mb-3">
                                <label for="available_from" class="form-label">From:</label>
                                <input type="datetime-local" name="start_times[]" value="{{ $avail->start_time }}" class="form-control">
                                
                                <label for="available_to" class="form-label">To:</label>
                                <input type="datetime-local" name="end_times[]" value="{{ $avail->end_time }}" class="form-control">
                                
                                <button type="button" class="btn btn-danger remove-choice">Remove</button>
                            </div>
                            @empty
                            <div class="choice-entry mb-3">
                                <label for="available_from" class="form-label">From:</label>
                                <input type="datetime-local" name="start_times[]" class="form-control">
                                
                                <label for="available_to" class="form-label">To:</label>
                                <input type="datetime-local" name="end_times[]" class="form-control">
                                
                                <button type="button" class="btn btn-danger remove-choice">Remove</button>
                            </div>
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-primary" id="add-availability">Add Availability</button>
                    </div>

                    <hr>

                    <!-- Areas of Knowledge -->
                    <div class="card-body">
                        <h4>Areas of Knowledge</h4>
                        <div id="areas-container choice-entry">
                            @foreach($ta_areas_of_knowledge as $area)
                            <div class="mb-3 area-entry">
                                <input type="text" name="areas_of_knowledge[]" value="{{ $area->name }}" class="form-control" readonly>
                                <button type="button" class="btn btn-danger remove-area">Remove</button>
                            </div>
                            @endforeach
                        </div>
                        <div class="input-group choice-entry mb-3">
                            <select id="new-area" class="form-select">
                                @foreach($all_areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" id="add-area">Add Area</button>
                        </div>
                    </div>

                    <hr>

                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function for Flatpickr
        function initializeFlatpickr(input) {
            flatpickr(input, {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                minDate: "today",
                minTime: "07:00",
                maxTime: "19:00",
                disable: [
                    function(date) {
                        // Disable Saturdays and Sundays
                        return (date.getDay() === 6 || date.getDay() === 0);
                    }
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    input.value = dateStr;
                }
            });
        }

        // Initialize Flatpickr on existing date and time inputs
        document.querySelectorAll('input[type="datetime-local"]').forEach(function(input) {
            initializeFlatpickr(input);
        });

        // Add event listener to dynamically added date and time inputs
        document.getElementById('add-availability').addEventListener('click', function() {
            let container = document.getElementById('choice-container');
            let newEntry = document.createElement('div');
            newEntry.classList.add('availability-entry', 'mb-3');
            newEntry.innerHTML = `
                <label for="available_from" class="form-label">From:</label>
                <input type="datetime-local" name="start_times[]" class="form-control">
                
                <label for="available_to" class="form-label">To:</label>
                <input type="datetime-local" name="end_times[]" class="form-control">
                
                <button type="button" class="btn btn-danger remove-choice">Remove</button>
            `;
            container.appendChild(newEntry);

            // Initialize Flatpickr on new date and time inputs
            newEntry.querySelectorAll('input[type="datetime-local"]').forEach(function(input) {
                initializeFlatpickr(input);
            });
        });

        // Add event listener to remove availability entries
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-choice')) {
                event.target.parentElement.remove();
            }
        });

        // Add event listener to add new areas of knowledge
        document.getElementById('add-area').addEventListener('click', function() {
            let container = document.getElementById('areas-container choice-entry');
            let select = document.getElementById('new-area');
            let selectedOption = select.options[select.selectedIndex];
            let newEntry = document.createElement('div');
            newEntry.classList.add('area-entry', 'choice-entry', 'mb-3');
            newEntry.innerHTML = `
                <input type="text" name="areas_of_knowledge[]" value="${selectedOption.text}" class="form-control" readonly>
                <button type="button" class="btn btn-danger remove-choice">Remove</button>
            `;
            container.appendChild(newEntry);
        });

        // Add event listener to remove areas of knowledge entries
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-area')) {
                event.target.parentElement.remove();
            }
        });
    });
</script>
@endsection