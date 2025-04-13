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