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
                        <h3>{{$user->first_name}}'s Requests  </h3>
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
                                <tr>
                                    <td class="text-center" colspan="4">No requests to display at this time.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection