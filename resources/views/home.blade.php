@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @if(Auth::user()->teachingAssistant && !Auth::user()->teachingAssistant->isProfileComplete())
            <div class="alert alert-warning">
                Your profile is incomplete! Please <a href="{{ route('edit_account') }}">click here</a> to update your information.
            </div>
        @endif
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

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
