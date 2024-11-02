@extends('layouts.app')

@section('title', 'Home')

@section('styles')
<style>
    #counter {
        text-align: center;
    }
</style>
@endsection

@section('content')
<nav>
    <ul>
        <li><strong>WISER</strong></li>
    </ul>
    <ul>
        <li><a href="{{ route('auth.logout') }}">Logout</a></li>
    </ul>
</nav>

<article>
    <header>Profile</header>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>IP Address:</strong> {{ $device_ip }}</p>

    <input type="hidden" name="wifi_expires_at" value="{{ $user->wifi_expires_at }}" />
</article>

@if(Session::has('message'))
<p>{{ session('message') }}</p>
@endif

<details>
    <summary role="button" class="secondary">Claim Voucher</summary>
    <form action="{{ route('voucher.claim') }}" method="POST">
        @csrf
        <input type="hidden" name="ip_address" value="{{ $device_ip }}" />

        <fieldset role="group">
            <input type="text" name="code" placeholder="Voucher Code" required
                @if ($errors->any()) aria-invalid="true" aria-describedby="invalid-helper" @endif
             />
            <input type="submit" value="Claim" />
        </fieldset>
        @if ($errors->any())
        <small id="invalid-helper">
            @foreach ($errors->all() as $error)
            {{ $error }} <br/>
            @endforeach
        </small>
        @endif
    </form>
</details>

@if($show_countdown)
<article x-data="countdown">
    <header>WiFi Timer</header>
    <div class="grid" id="counter">
        <div>
            <h2 x-text="String(Math.floor(timeLeft / 3600)).padStart(2, '0')"></h2>
            hours
        </div>
        <div>
            <h2 x-text="String(Math.floor((timeLeft % 3600) / 60)).padStart(2, '0')"></h2>
            minutes
        </div>
        <div>
            <h2 x-text="String(timeLeft % 60).padStart(2, '0')"></h2>
            seconds
        </div>
    </div>
    <p x-show="timeLeft < 0">Time's up, throw in some more garbage to enjoy additional Wi-Fi time!</p>
</article>
@else
<article>
    <header>WiFi Timer</header>
    <p>Throw in some more garbage to enjoy additional Wi-Fi time!</p>
</article>
@endif

@endsection

@push('scripts')
<script>

document.addEventListener('alpine:init', () => {
    Alpine.data('countdown', () => ({
        expirationDate: false,
        timeLeft: 0,
        init() {
            this.expirationDate = new Date(document.getElementsByName('wifi_expires_at')[0].value);
            this.timeLeft = this.calculateTimeLeft(this.expirationDate);
            this.startTimer();
        },
        calculateTimeLeft(futureDatetime) {
            const now = new Date().getTime();
            return Math.max(Math.floor((this.expirationDate - now) / 1000), 0); // Convert to seconds
        },
        startTimer() {
            this.timer = setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    clearInterval(this.timer);
                }
            }, 1000);
        },
        stopTimer() {
            clearInterval(this.timer);
        }
    }))
});
</script>
@endpush