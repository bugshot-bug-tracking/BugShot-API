<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
	    Status::create([
            'id' => 'DDDDDDDD-DDDD-DDDD-DDDD-DDDDDDDDDDD1',
            'project_id' => 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC',
			'designation' => 'Backlog',
            'order_number' => 1,
            'permanent' => 'backlog',
		]);

        Status::create([
            'id' => 'DDDDDDDD-DDDD-DDDD-DDDD-DDDDDDDDDDD2',
            'project_id' => 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC',
			'designation' => 'ToDo',
            'order_number' => 2,
            'permanent' => NULL,
		]);

        Status::create([
            'id' => 'DDDDDDDD-DDDD-DDDD-DDDD-DDDDDDDDDDD3',
            'project_id' => 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC',
			'designation' => 'Doing',
            'order_number' => 3,
            'permanent' => NULL,
		]);

        Status::create([
            'id' => 'DDDDDDDD-DDDD-DDDD-DDDD-DDDDDDDDDDD4',
            'project_id' => 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC',
			'designation' => 'Testing',
            'order_number' => 4,
            'permanent' => NULL,
		]);

        Status::create([
            'id' => 'DDDDDDDD-DDDD-DDDD-DDDD-DDDDDDDDDDD5',
            'project_id' => 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC',
			'designation' => 'Done',
            'order_number' => 5,
            'permanent' => 'done',
		]);
	}
}
