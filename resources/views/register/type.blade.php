@extends('layouts.main')
@section('content')

<a class="label label-info" href="{{ URL::to('register') }}">Back to Start</a>
<div class="row col-md-12">
    <h2 class="register-heading">Are you a(n)...</h2>
</div>

<div class="row">
    <div class="col-md-4">
        <a href="{{ URL::to('register/goon') }}" class="register-choice">
            Goon<br><span>(:getout:)</span>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ URL::to('register/sponsored') }}" class="register-choice">
            Sponsored member<br><span>(a goon gave you a sponsor code)</span>
        </a>
    </div>
    <div class="col-md-4">
        {{-- <a href="{{ URL::to('register/affiliate') }}" class="register-choice"> --}}
        <a href="#" class="register-choice bg-notimplemented">
            Affiliate<br><span>(a member of an affiliated organization)</span>
            <br><span>(not yet implemented)</span>
        </a>
    </div>
</div>
@stop
