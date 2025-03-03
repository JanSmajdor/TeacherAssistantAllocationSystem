@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Create New Area Of Knowledge Form</div>

                <div class="card-body">
                    <p>Please fill out this form to create a new area of knowledge</p>
                    
                    <form id="create-new-area-of-knowledge-form" action="{{ route('admin.areas_of_knowledge.create') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="aok-name" class="col-md-4 col-form-label text-md-end">{{ ('Area Of Knowledge Name') }}</label>

                            <div class="col-md-6">
                                <input id="aok-name" type="text" class="form-control @error('aok-name') is-invalid @enderror" name="aok-name" value="{{ old('aok-name') }}" required autocomplete="aok-name" autofocus>

                                @error('aok-name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ ('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection