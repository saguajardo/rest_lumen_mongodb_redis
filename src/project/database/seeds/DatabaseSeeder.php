<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('TaskTableSeeder');
    }
}

class TaskTableSeeder extends Seeder {
	public function run() {
		DB::collection('task')->insert([
			[
				'title'			=> 'Titulo 1',
				'description'	=> 'Description 1',
				'due_date'		=> Carbon::now()->format('Y-m-d'),
				'completed' 	=> "false",
				'created_at'	=> Carbon::now()->format('Y-m-d'),
				'updated_at'	=> Carbon::now()->format('Y-m-d'),
			],
			[
				'title'			=> 'Titulo 2',
				'description'	=> 'Description 2',
				'due_date'		=> Carbon::now()->format('Y-m-d'),
				'completed' 	=> "true",
				'created_at'	=> Carbon::now()->format('Y-m-d'),
				'updated_at'	=> Carbon::now()->format('Y-m-d'),
			]
		]);
	}
}