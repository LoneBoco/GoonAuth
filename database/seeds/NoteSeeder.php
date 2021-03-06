<?php

namespace App;

// use App\GameOrg;
// use App\Group;
// use App\Note;
// use App\NoteType;
// use App\Role;
// use App\User;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NoteSeeder extends Seeder {

	public function run()
	{
		DB::table('Note')->delete();

		$admin = User::where('UGoonID', config('goonauth.admin'))->first();
		$roll = Role::where('RName', 'Admin')->first();

		$sa = Group::where('GRCode', 'SA')->first();
		$fljk = GameOrg::where('GOAbbr', 'FLJK')->first();
		$wol = GameOrg::where('GOAbbr', 'WoL')->first();

		$system = NoteType::where('NTCode', 'SYS')->first();
		$status = NoteType::where('NTCode', 'STAT')->first();

		$n_join_sa = Note::create(array(
			'NTID' => $status->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NSubject' => 'Authentication',
			'NMessage' => 'User accepted into group Something Awful.'
		));

		$n_owner_fljk = Note::create(array(
			'NTID' => $system->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NSubject' => 'Ownership Change',
			'NMessage' => 'User has taken ownership of organization Goonrathi.'
		));

		$n_owner_wol = Note::create(array(
			'NTID' => $system->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NSubject' => 'Ownership Change',
			'NMessage' => 'User has taken ownership of organization Word of Lowtax.'
		));

		$n_join_wol = Note::create(array(
			'NTID' => $status->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NSubject' => 'Authentication',
			'NMessage' => 'User accepted into organization Word of Lowtax.'
		));

		$sa->notes()->attach($n_join_sa);
		$fljk->notes()->attach($n_owner_fljk);
		$wol->notes()->attach($n_owner_wol);
		$wol->notes()->attach($n_join_wol);
	}
}
