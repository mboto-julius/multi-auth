@extends('layouts.app')

@section('main')
{{-- form --}}
<div class="container">
    <div class="row">
        <div class="col-md-4 offset-md-4 pt-5">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if (Session::has('success'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('success') }}
            </div>
            @endif
            <form action="{{ route('admin.login_submit') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ route('admin.forget_password') }}">Forget Password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- /form --}}
@endsection
