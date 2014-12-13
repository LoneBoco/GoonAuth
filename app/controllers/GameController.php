<?php

class GameController extends BaseController
{
	protected function verify_sc($username, $token)
	{
		// RSI splits important info into two separate profiles.  WTF guys.
		// We first get the site profile which contains our user id and regdate.
		// We then get the forum profile which contains the postcount, separated into posts and threads.  Because why.

		$ret = array(
			'ret' => false,
			'userid' => 0,
			'regdate' => Carbon::now(),
			'postcount' => 0,
		);

		// Grab our site profile.
		$page = file_get_contents('https://robertsspaceindustries.com/citizens/' . urlencode($username));
		if ($page === FALSE)
			return $ret;

		$pattern404 = "/<title>.*404.*<\/title>/is";
		$patternUserID = "/UEE Citizen Record.*?<strong class=\"value\">#([\w\s,]+)<\/strong>/is";
		$patternEnlisted = "/Enlisted.*?<strong class=\"value\">([\w\s,]+)<\/strong>/is";

		// Test for a 404.  A 404 means that they are not an RSI user.
		// The 404 should have occurred with file_get_contents, so this is just a sanity check.
		if (preg_match($pattern404, $page) === 1)
			return $ret;

		// See if the token was found.
		if (stristr($page, $token) === false)
			return $ret;

		// We are good at this point.
		$ret['ret'] = true;

		// Grab our user ID.
		if (preg_match($patternUserID, $page, $matches) === 1)
			$ret['userid'] = intval($matches[1]);

		// Grab regdate.
		if (preg_match($patternEnlisted, $page, $matches) === 1)
			$ret['regdate'] = Carbon::createFromFormat('M j, Y', $matches[1]);

		// Grab the forum profile now so we can get our post count.
		$page = file_get_contents('https://forums.robertsspaceindustries.com/profile/' . urlencode($username));
		if ($page === FALSE)
			return $ret;

		$patternComments = "/Comments.*?<span class=\"Count\">([\w\s,]+)<\/span>/is";
		$patternDiscussions = "/Discussions.*?<span class=\"Count\">([\w\s,]+)<\/span>.*?<span class=\"Count\">([\w\s,]+)<\/span>/is";

		// Grab our comment count.
		if (preg_match($patternComments, $page, $matches) === 1)
			$ret['postcount'] += intval($matches[1]);

		// Grab our discussion count.
		if (preg_match($patternDiscussions, $page, $matches) === 1)
			$ret['postcount'] += intval($matches[1]);

		return $ret;
	}

	protected function verify_mwo($username, $token)
	{
		// We have to grab two pages to get to the user profile.
		// The first page is a member search so we can get the profile link.

		$ret = array(
			'ret' => false,
			'userid' => 0,
			'regdate' => Carbon::now(),
			'postcount' => 0,
		);

		// Search for our member.
		$page = file_get_contents('http://mwomercs.com/forums/index.php?app=members&module=list&name_box=begins&name=' . urlencode($username));
		if ($page === FALSE)
			return $ret;

		$pattern404 = "/<title>.*404.*<\/title>/is";
		$patternProfile = "/<strong><a href='(.+?)' title='View Profile'>(.+?)<\/a><\/strong>/is";

		// Test for a 404.  A 404 means that they are not an RSI user.
		// The 404 should have occurred with file_get_contents, so this is just a sanity check.
		if (preg_match($pattern404, $page) === 1)
			return $ret;

		// See if the profile was found.
		if (preg_match($patternProfile, $page, $matches) !== 1)
			return $ret;

		// Compare names from the profile.
		if (strcasecmp($matches[2], $username) !== 0)
			return $ret;

		// Grab the profile page.
		$profileURL = $matches[1];
		$page = file_get_contents($profileURL);
		if ($page === FALSE)
			return $ret;

		// See if the token was found.
		if (stristr($page, $token) === false)
			return $ret;

		// We are good at this point.
		$ret['ret'] = true;

		$patternUserID = "/mwomercs\.com\/forums\/user\/(\d+)-.+\//is";
		$patternRegDate = "/Member Since (\d+ \w+ \d+)<br \/>/is";
		$patternPostCount = "/Active Posts.*?<span class='row_data'>([\d,]+).*?<\/span>/is";

		// Grab our user id.
		if (preg_match($patternUserID, $page, $matches) === 1)
			$ret['userid'] = intval($matches[1]);

		// Grab our reg date.
		if (preg_match($patternRegDate, $page, $matches) === 1)
			$ret['regdate'] = Carbon::createFromFormat('j M Y', $matches[1]);

		// Grab our post count.
		if (preg_match($patternPostCount, $page, $matches) === 1)
		{
			$s = str_replace(",", "", $matches[1]);
			$ret['postcount'] = intval($s);
		}

		return $ret;
	}

	public function showGames()
	{
		$auth = Session::get('auth');

		$include = array('auth' => $auth);
		return View::make('games.list', $include);
	}

	public function showGame($abbr)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$include = array('auth' => $auth, 'game' => $game);
		return View::make('games.details', $include);
	}

	public function showJoinGame($abbr)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$token = uniqid('FART-');
		Session::put('token', $token);

		$include = array('auth' => $auth, 'game' => $game, 'token' => $token);
		return View::make('games.join', $include);
	}

	public function doLink($abbr)
	{
		$auth = Session::get('auth');
		$game = Game::where('GAbbr', $abbr)->first();
		if (empty($game))
			return Redirect::to('games');

		$username = Input::get('username');
		$token = Session::get('token');

		if (!isset($token))
			return Redirect::back()->with('error', 'An unknown error has occured.');

		if (!isset($username))
			return Redirect::back()->with('error', 'You must enter your Game Username.');

		// Attempt a verification.
		$func = 'verify_'.$abbr;
		$ret = $this->$func($username, $token);
		if (!isset($ret) || $ret['ret'] === false)
			return Redirect::back()->with('error', 'Verification failed.');

		// Success!  Let's create our user now.
		$user = new GameUser;
		$user->GID = $game->GID;
		$user->UID = $auth->UID;
		$user->USID = UserStatus::pending()->first()->USID;
		$user->GUUserID = $ret['userid'];
		$user->GURegDate = $ret['regdate'];
		$user->GUCacheDate = Carbon::now();
		$user->GUCachedName = $username;
		$user->GUCachedPostCount = $ret['postcount'];
		$user->save();

		return View::make('games.complete');
	}


	public function showAddForm()
	{
		$auth = Session::get('auth');

		$include = array('auth' => $auth);
		return View::make('character.add', $include);
	}

	public function doAddCharacter()
	{
		$auth = Session::get('auth');

		if ((!empty($auth->sa_username) && $auth->characters->count() >= Config::get('goonauth.characters')) ||
			($auth->is_sponsored && $auth->characters->count() >= Config::get('goonauth.sponsored'))) {
			Session::flash('error', 'You have exceeded your character limit.');
			return Redirect::back();
		}

		$character = new Character();
		$character->auth_id = $auth->id;
		$character->name = Input::get('name');

		if ($auth->characters->count() == 0) {
			$character->is_main = true;
		}
		$character->save();

		return Redirect::to('characters');
	}

	public function doSetMain(Character $character)
	{
		$auth = Session::get('auth');

		if ($auth->id != $character->auth_id) {
			App::abort(500, "Error processing request!");
		}

		$current = Character::where('auth_id', $auth->id)->where('is_main', true)->first();
		$current->is_main = false;
		$current->save();

		$character->is_main = true;
		$character->save();

		return Redirect::back();
	}

	public function doDelete(Character $character)
	{
		$auth = Session::get('auth');

		if ($auth->id != $character->auth_id) {
			App::abort(500, "Error processing request!");
		}

		if (!$character->locked) {
			$character->delete();
		}

		return Redirect::back();
	}
}