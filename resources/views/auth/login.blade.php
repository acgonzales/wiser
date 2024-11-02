@extends('layouts.app')

@section('title', 'Login')

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

    <form action="{{ route('auth.login') }}" method="POST">
        @csrf
        <fieldset>
            <label>
                Email Address
                <input name="email" type="email" required 
                    @if ($errors->any()) aria-invalid="true" aria-describedby="invalid-helper" @endif />
            </label>
            <label>
                Password
                <input type="password" name="password" required
                    @if ($errors->any()) aria-invalid="true" aria-describedby="invalid-helper" @endif />

                    @if ($errors->any())
                        <small id="invalid-helper">
                            Cannot authenticate, please double check your credentials.
                        </small>
                    @endif
            </label>
        </fieldset>
        <input type="submit" value="Login" />
        <a href="{{ route('signup') }}" class="secondary">Don't have an account? Create one.</a>
    </form>
@endsection