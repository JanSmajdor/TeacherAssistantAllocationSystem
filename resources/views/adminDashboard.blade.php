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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
