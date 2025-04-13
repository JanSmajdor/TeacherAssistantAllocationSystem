@extends('layouts.master')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ $user->role }} Dashboard </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>Welcome to your {{ $user->role }} dashboard, {{ $user->first_name }}</p>

                    <div class="edit-account-details-table">
                        <h3>Pending Requests ({{ $admin_count }}) </h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Request Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($admin_edit_account_requests->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="3">No requests to display at this time.</td>
                                </tr>
                                @else
                                @foreach($admin_edit_account_requests as $request)
                                <tr>
                                    <td>{{ $request->teaching_assistant->user->first_name }} {{ $request->teaching_assistant->user->last_name }}</td>
                                    <td>Edit Account Details</td>
                                    <td>
                                        <button class="btn btn-primary" data-toggle="modal" data-target="#requestModal-{{ $request->id }}">Manage Request</button>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                <div class="modal fade" id="requestModal-{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel-{{ $request->id }}" aria-hidden="true">
                                  <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="requestModalLabel-{{ $request->id }}">Request Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                        </button>
                                      </div>
                                      <div class="modal-body">
                                        <p><strong>Name:</strong> {{ $request->teaching_assistant->user->first_name }} {{ $request->teaching_assistant->user->last_name }}</p>
                                        <p><strong>Email:</strong> {{ $request->teaching_assistant->user->email }}</p>
                                        <p><strong>Role:</strong> {{ $request->teaching_assistant->user->role }}</p>
                                        <p><strong>Date Request Created:</strong> {{ \Carbon\Carbon::parse($request->created_at)->format('d/m/Y')}}</p>
                                        <hr>
                                        <h5>Requested Changes</h5>
                                        <p><strong>Requested Areas of Knowledge:</strong> {{ implode(', ', $request->areas_of_knowledge) }}</p>
                                        <!-- Add more details as needed -->
                                      </div>
                                      <div class="modal-footer">
                                        <form id="approve-form-{{ $request->id }}" action="{{ route('approve_edit_account') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="ta_id" value="{{ $request->ta_id }}">
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </form>
                                        <form id="deny-form-{{ $request->id }}" action="{{ route('deny_edit_account') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="ta_id" value="{{ $request->ta_id }}">
                                            <button type="submit" class="btn btn-danger">Deny</button>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="booking-requests-table">
                        <h3>Booking Requests</h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>Booking Type</th>
                                    <th>Requested TAs</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($booking_requests->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="5">No booking requests to display at this time.</td>
                                </tr>
                                @else
                                @foreach($booking_requests as $booking)
                                <tr>
                                    <td>{{ $booking->module->module_name }} ({{ $booking->module->module_code }})</td>
                                    <td>{{ $booking->booking_type }}</td>
                                    <td>{{ $booking->num_tas_requested }}</td>
                                    <td>{{ $booking->status }}</td>
                                    <td>
                                        <button class="btn btn-primary" data-toggle="modal" data-target="#bookingModal-{{ $booking->id }}">View Details</button>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                <div class="modal fade" id="bookingModal-{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel-{{ $booking->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="bookingModalLabel-{{ $booking->id }}">Booking Details</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Module:</strong> {{ $booking->module->module_name }} ({{ $booking->module->module_code }})</p>
                                                <p><strong>Booking Type:</strong> {{ $booking->booking_type }}</p>
                                                <p><strong>Requested TAs:</strong> {{ $booking->num_tas_requested }}</p>
                                                <p><strong>Date From:</strong> {{ \Carbon\Carbon::parse($booking->date_from)->format('d/m/Y') }}</p>
                                                <p><strong>Date To:</strong> {{ \Carbon\Carbon::parse($booking->date_to)->format('d/m/Y') }}</p>
                                                <p><strong>Status:</strong> {{ $booking->status }}</p>
                                                <hr>
                                                <h5>Suggested TA Match</h5>
                                                @if($booking->suggested_ta)
                                                <p><strong>Name:</strong> {{ $booking->suggested_ta->first_name }} {{ $booking->suggested_ta->last_name }}</p>
                                                <p><strong>Email:</strong> {{ $booking->suggested_ta->email }}</p>
                                                @else
                                                <p>No TA match found for this booking.</p>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <form id="approve-booking-form-{{ $booking->id }}" action="{{ route('approve_booking') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                    <button type="submit" class="btn btn-success">Approve</button>
                                                </form>
                                                <form id="deny-booking-form-{{ $booking->id }}" action="{{ route('deny_booking') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                    <button type="submit" class="btn btn-danger">Deny</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
