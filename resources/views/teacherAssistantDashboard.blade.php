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

                    <div class="ta-confirmed-bookings-table mb-5">
                        <h3>Confirmed Bookings ({{ $ta_confirmed_bookings_count }})</h3>
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
                                @if($ta_confirmed_bookings->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="7">No bookings to display at this time.</td>
                                </tr>
                                @else
                                @foreach($ta_confirmed_bookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->booking->module->module_code }}</td>
                                    <td>{{ $booking->booking->module->module_name }}</td>
                                    <td>{{ $booking->booking->module->moduleLeader->first_name }} {{ $booking->booking->module->moduleLeader->last_name }} </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y')}}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->time)->format('H:i')}}</td>
                                    <td>{{ $booking->booking->site }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    <div class="edit-account-details-table">
                        <h3>{{$user->first_name}}'s Requests ({{ $ta_count }}) </h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Created</th>
                                    <th>Request Type</th>
                                    <th>Request Status</th>
                                    <th>Viewed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($ta_edit_account_requests->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="5">No requests to display at this time.</td>
                                </tr>
                                @else
                                @foreach($ta_edit_account_requests as $request)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('d/m/Y')}}</td>
                                    <td>Edit Account Details</td>
                                    <td>{{ $request->request_status }}</td>
                                    <td>{{\Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:m')}}</td>
                                    <td class="text-center">
                                        <form action="{{ route('request_hide') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="request_id" value="{{ $request->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm">X</button>
                                        </form>
                                    </td>
                                </tr>
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