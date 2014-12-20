<?php

class NoteTypeSeeder extends Seeder {

	public function run()
	{
		DB::table('NoteType')->delete();

		// Special system notes.
		NoteType::create(array(
			'NTCode' => 'SYS',
			'NTName' => 'System',
			'NTColor' => '#FFFFCC',
			'NTSystemUseOnly' => true
		));

		// Status change notes.
		NoteType::create(array(
			'NTCode' => 'STAT',
			'NTName' => 'Status Change',
			'NTColor' => '#CCFFCC',
			'NTSystemUseOnly' => true
		));

		NoteType::create(array(
			'NTCode' => 'GEN',
			'NTName' => 'General',
			'NTColor' => '#FFCCFF'
		));

		NoteType::create(array(
			'NTCode' => 'INT',
			'NTName' => 'Intelligence',
			'NTColor' => '#CCFFFF'
		));
	}
}
