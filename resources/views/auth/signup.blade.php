@extends('layouts.app')

@section('title', 'Sign up')

@section('styles')
<style>
.title {
    margin: 4rem 0;
    text-align: center;
}
</style>
@endsection

@section('content')
    <div class="title">
        <h1>WISER</h1>
        <p>Login to access WiFi</p>
    </div>

    <form action="{{ route('auth.signup') }}" method="POST">
        @csrf
        <fieldset>
            <label>
                Name
                <input name="name" type="text" required 
                    @if ($errors->has('name')) aria-invalid="true" aria-describedby="invalid-helper" @endif />
                    @if ($errors->has('name'))
                        <small id="invalid-helper">
                            {{ $errors->first('name') }}
                        </small>
                    @endif
            </label>
            <label>
                Email Address
                <input name="email" type="email" required
                    @if ($errors->has('email')) aria-invalid="true" aria-describedby="invalid-helper" @endif />
                    @if ($errors->has('email'))
                        <small id="invalid-helper">
                            {{ $errors->first('email') }}
                        </small>
                    @endif
            </label>
            <label>
                Password
                <input type="password" name="password" required
                    @if ($errors->has('password')) aria-invalid="true" aria-describedby="invalid-helper" @endif />
                    @if ($errors->has('password'))
                        <small id="invalid-helper">
                            {{ $errors->first('password') }}
                        </small>
                    @endif
            </label>
        </fieldset>
        <input type="submit" value="Create Account" />
        <a href="{{ route('login') }}" class="secondary">Already have an account? Login.</a>
    </form>
@endsection