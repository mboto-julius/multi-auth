@extends('layouts.app')

@section('main')
<main class="login-form">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 pt-5">
                <div class="card">
                    <div class="card-header">Reset Your Password</div>
                    <div class="card-body">

                        @if (Session::has('message'))
                        <div class="alert alert-success" role="alert">
                            {{ Session::get('message') }}
                        </div>
                        @endif

                        @if (Session::has('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ Session::get('error') }}
                        </div>
                        @endif

                        <form action="{{ route('reset.password_submit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $tokenInfo->token }}">

                            <div class="form-group row">
                                <label for="email_address" class="col-md-4 col-form-label text-md-right">Email
                                    Address</label>
                                <div class="col-md-6">
                                    <input type="text" id="email_address" class="form-control" name="email" value="{{ $tokenInfo->email }}" placeholder="Email Address">
                                    @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row pt-3">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                <div class="col-md-6">
                                    <input type="password" id="password" class="form-control" name="password" required autofocus>
                                    @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row pt-3">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm
                                    Password</label>
                                <div class="col-md-6">
                                    <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required autofocus>
                                    @if ($errors->has('password_confirmation'))
                                    <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 offset-md-4 pt-3">
                                <button type="submit" class="btn btn-primary mx-2">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
