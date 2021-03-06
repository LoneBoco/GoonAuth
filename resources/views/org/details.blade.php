@extends('layouts.main')
@section('content')

<?php
use App\Game;
use App\GameOrg;
use App\GameOrgHasGameUser;
use App\User;
use App\UserStatus;
use App\Extensions\Permissions\UserPerm;
?>

<p><a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ $game->GName }}</a></p>

<!-- Organization navbar -->
<div class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".org-list">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<span class="navbar-brand">Organizations</span>
	</div>
	<div class="container">
		<div class="org-list navbar-collapse collapse">
			<div class="navbar-form">
			@foreach ($game->orgs()->get() as $org)
				<a href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}" class="btn btn-default">{{ $org->GOName }}</a>
			@endforeach
			</div>
		</div>
	</div>
</div>

<?php
$perms = new UserPerm($auth);
$pending = UserStatus::pending();
$pending_count = $org->gameusers()->where('GameOrgHasGameUser.USID', $pending->USID)->count();
?>

<p style="margin-top: 20px">
@if ($perms->gameOrg($org->GOID)->auth == true)
	<a class="btn btn-danger" href="{{ URL::to('auth/'.Request::path()) }}">Authorize Members
	@if ($pending_count !== 0)
		<span class="badge">{{ $pending_count }}</span>
	@endif
	</a>
	@endif

	@if ($perms->gameOrg($org->GOID)->read == true)
	<a class="btn btn-success" href="{{ URL::to(Request::path().'/view') }}">View Members</a>
@endif
</p>

<?php
$gamecharacters = $game->gameusers()->where('UID', $auth->UID)->count();
$characters = $org->gameusers()->where('UID', $auth->UID)->get();
?>

<h1>{{ e($org->GOName) }} Character List</h1>
<div class="row">
	<div class="col-md-12">

	@if ($characters->count() == 0)

	<h4>You are currently not a part of this organization.</h4>

	@if ($gamecharacters == 0)
	<h4>You also have not registered any characters.</h4>
	<p style="margin-top: 30px"><a href="{{ URL::to('games/'.$game->GAbbr.'/link') }}" class="btn btn-success">Add Character</a></p>
	@else
	<p style="margin-top: 30px"><a href="{{ URL::to(Request::path().'/join') }}" class="btn btn-success">Join Organization</a></p>
	@endif

	@else

	<h4>Your linked characters:</h4>

	<table class="table">
		<thead>
			<th style="width: 75px;">Status</th>
			<th>Character Name</th>
		</thead>
		@foreach ($characters as $character)
		<tr>
			<?php $status = UserStatus::find(GameOrgHasGameUser::where('GOID', $org->GOID)->where('GUID', $character->GUID)->first()->USID) ?>
			@if (strcmp($status->USCode, 'ACTI') == 0)
				<td><span class="label label-primary">{{ e($status->USStatus) }}</span></td>
			@else
				<td><span class="label label-default">{{ e($status->USStatus) }}</span></td>
			@endif
			<td>{{ e($character->GUCachedName) }}</td>
		</tr>
		@endforeach
	</table>

	<p><a href="{{ URL::to(Request::path().'/join') }}" class="btn btn-success">Add Character</a></p>

	@endif

	</div>
</div>
@stop
