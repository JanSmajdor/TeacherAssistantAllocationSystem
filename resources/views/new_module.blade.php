@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Create New Module Form</div>

                <div class="card-body">
                    <p>Please fill out this form to create a new module</p>
                    
                    <form id="create-new-module-form" action="{{ route('admin.create_new_module.create') }}" method='POST'>
                        @csrf
                        <div class="row mb-3">
                            <label for="module-name" class="col-md-4 col-form-label text-md-end">{{ ('Module Name') }}</label>

                            <div class="col-md-6">
                                <input id="module-name" type="text" class="form-control @error('module-name') is-invalid @enderror" name="module-name" value="{{ old('module-name') }}" required autocomplete="module-name" autofocus>

                                @error('module-name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="module-code" class="col-md-4 col-form-label text-md-end">{{ ('Module Code') }}</label>

                            <div class="col-md-6">
                                <input id="module-code" type="text" class="form-control @error('module-code') is-invalid @enderror" name="module-code" value="{{ old('module-code') }}" required autocomplete="module-code" autofocus>

                                @error('module-code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="module-leader" class="col-md-4 col-form-label text-md-end">{{ ('Module Leader') }}</label>
                            
                            <div class="col-md-6">
                                <select name="module-leader" id="module-leader" class="form-control @error('module-leader') is-invalid @enderror" required autocomplete="module-leader">
                                    <!-- add in a parser for each ML in the db and create an option tag for each of them -->
                                     @foreach($module_leaders as $ml)
                                     <option value="{{ $ml->id }}">{{ $ml->first_name }} {{ $ml->last_name }}</option>
                                     @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="number-of-students" class="col-md-4 col-form-label text-md-end">{{ ('Number Of Students') }}</label>

                            <div class="col-md-6">
                                <input id="number-of-students" type="text" class="form-control @error('number-of-students') is-invalid @enderror" name="number-of-students" value="{{ old('number-of-students') }}" required autocomplete="number-of-students">

                                @error('number-of-students')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="module-area-of-knowledge" class="col-md-4 col-form-label text-md-end">{{ ('Area of Knowledge') }}</label>
                            
                            <div class="col-md-6">
                                <select name="module-area-of-knowledge" id="module-area-of-knowledge" class="form-control @error('module-area-of-knowledge') is-invalid @enderror" required autocomplete="module-area-of-knowledge">
                                    <!-- add in a parser for each M AoK in the db and create an option tag for each of them -->
                                     @foreach($aok as $area)
                                     <option value="{{ $area->id }}">{{ $area->name }}</option>
                                     @endforeach
                                </select>
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
