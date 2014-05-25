<?php

class AdminController extends BaseController
{
	public function showList()
	{
		if (Input::has('search')) {
			$users = User::where('xf_username', 'LIKE','%'.Input::get('search').'%')
						->orWhere('sa_username', 'LIKE', '%'.Input::get('search').'%')
						->paginate(20);
		} else {
			$users = User::paginate(20);
		}

		$include = array('users' => $users, 'search' => Input::get('search'));

		return View::make('admin.list', $include);
	}

	public function showUser(User $user)
	{
		$include = array('user' => $user);
		return View::make('admin.user', $include);
	}
}