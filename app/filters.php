<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	if (Session::has('authenticated')) {
		$auth = User::where('xf_id', Session::get('xenforoId'))->first();
		Session::put('auth', $auth);
	}
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (!Session::has('authenticated')) return Redirect::to('login');

	if (Session::get('auth')->is_banned) {
		Session::flush();
		return Redirect::to('login')->with('banned', 1);
	}
});

Route::filter('linked', function()
{
	if (!Session::has('auth') || (empty(Session::get('auth')->sa_username) && !Session::get('auth')->is_sponsored)) return Redirect::to('/');
});

Route::filter('goon', function() {
	if (!Session::has('auth') || empty(Session::get('auth')->sa_username)) return Redirect::to('/');
});

Route::filter('admin', function() {
	if (!Session::has('auth') || !Session::get('auth')->is_admin) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
