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

                    <!-- Admin Dashboard Content -->
                    @if ($user->role == 'Admin')
                    
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
                                    @if($request->teaching_assistant->user->role == 'Teaching Assistant')
                                    <td>Edit Account Details</td>
                                    @endif
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
                                        @if(!empty($request->teaching_assistant->current_area_id))
                                        <p><strong>Current Area of Knowledge:</strong> {{ $request->teaching_assistant->current_area_id }}</p>
                                        @else
                                        <p><strong>Current Area of Knowledge:</strong> N/A</p>
                                        @endif
                                        <p><strong>Requested Area of Knowledge:</strong> {{ $request->area_of_knowledge->name }}</p>
                                        <!-- Add more details as needed -->
                                      </div>
                                      <div class="modal-footer">
                                        <form id="approve-form-{{ $request->id }}" action="{{ route('approve_edit_account') }}" method="POST" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="request_id" value="{{ $request->id }}">
                                            <button type="button" class="btn btn-success" onclick="event.preventDefault(); document.getElementById('approve-form-{{ $request->id }}').submit();">Approve</button>
                                        </form>
                                        <form id="deny-form-{{ $request->id }}" action="{{ route('deny_edit_account') }}" method="POST" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="request_id" value="{{ $request->id }}">
                                            <button type="button" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('deny-form-{{ $request->id }}').submit();">Deny</button>
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

                    @endif

                    <!-- Module Leader Dashboard Content -->
                    @if ($user->role == 'Module Leader')

                    @endif

                    <!-- Teaching Assistant Dashboard Content -->
                    @if ($user->role == 'Teaching Assistant')
                    
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
                    
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
