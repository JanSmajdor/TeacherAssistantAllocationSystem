@extends('layouts.master')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ $user->role }} Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>Welcome to your {{ $user->role }} dashboard, {{ $user->first_name }}</p>

                    <div class="module-leader-confirmed-bookings mb-5">
                        <h3>Confirmed Bookings ({{ $moduleLeaderConfirmedBookingsCount }})</h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Module ID</th>
                                    <th>Module Name</th>
                                    <th>Module Leader Name</th>
                                    <th>Booking Date</th>
                                    <th>Booking Time</th>
                                    <th>Site</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($moduleLeaderConfirmedBookings->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="7">No bookings to display at this time.</td>
                                </tr>
                                @else
                                @foreach($moduleLeaderConfirmedBookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->module->module_code }}</td>
                                    <td>{{ $booking->module->module_name }}</td>
                                    <td>
                                        @if($booking->taBookings->isNotEmpty())
                                            @foreach($booking->taBookings as $taBooking)
                                                {{ $taBooking->ta->user->first_name }} {{ $taBooking->ta->user->last_name }}<br>
                                            @endforeach
                                        @else
                                            No TA assigned
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->date_from)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->date_from)->format('H:i') }}</td>
                                    <td>{{ $booking->site }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    <div class="edit-account-details-table">
                        <h3>{{ $user->first_name }}'s Requests ({{ $bookingRequestsCount }})</h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Request Batch ID</th>
                                    <th>Request Type</th>
                                    <th>Viewed</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($bookingRequests->count() > 0)
                                    @foreach ($bookingRequests as $requestBatchId => $requests)
                                        <tr>
                                            <td>{{ $requestBatchId }}</td>
                                            <td>Booking Request</td>
                                            @if($requests->last()->updated_at == $requests->last()->created_at)
                                                <td>Not yet viewed by Admin.</td>
                                            @else
                                                <td>Viewed at: {{ $requests->last()->updated_at }}</td>
                                            @endif
                                            <td>{{ $requests->last()->created_at }}</td>
                                            <td>
                                                <button class="btn btn-primary" data-toggle="modal" data-target="#requestModal-{{ $requestBatchId }}">View Details</button>
                                            </td>
                                        </tr>

                                        <!-- Modal -->
                                        <div class="modal fade" id="requestModal-{{ $requestBatchId }}" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel-{{ $requestBatchId }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="requestModalLabel-{{ $requestBatchId }}">Request Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Request Created At:</strong> {{ $requests->last()->created_at }}</p>
                                                        <p><strong>Total Requests in Group:</strong> {{ $requests->count() }}</p>
                                                        <hr>
                                                        <h5>Requests in this Group:</h5>
                                                        <ul>
                                                            @foreach ($requests as $request)
                                                                <li>
                                                                    <strong>Booking ID:</strong> {{ $request->id }} <br>
                                                                    <strong>Module ID:</strong> {{ $request->module_id }} <br>
                                                                    <strong>Booking Type:</strong> {{ $request->booking_type }} <br>
                                                                    <strong>Date From:</strong> {{ $request->date_from }} <br>
                                                                    <strong>Date To:</strong> {{ $request->date_to }} <br>
                                                                    <strong>Status:</strong> {{ $request->status }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form id="deny-form-{{ $requestBatchId }}" action="" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger">Cancel Booking</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="5">No requests to display at this time.</td>
                                    </tr>
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