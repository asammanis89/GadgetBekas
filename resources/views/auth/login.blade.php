@extends('layouts.app')

@section('title', 'Masuk - '.config('shop.name'))

@section('content')
<div class="login-box">
    <h1>Masuk admin</h1>
    <p class="muted">Khusus pengelola toko.</p>

    @if ($errors->any())
        <div class="errors" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ url('/masuk') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
        </div>
        <label style="display:flex;gap:8px;align-items:center;margin-bottom:14px"><input type="checkbox" name="ingat" value="1" style="width:auto;min-height:0"> Ingat saya di perangkat ini</label>
        <button class="btn primary" style="width:100%" type="submit">Masuk</button>
    </form>
</div>
@endsection
