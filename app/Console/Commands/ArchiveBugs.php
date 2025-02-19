<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Bug;

class ArchiveBugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugs:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archives all bugs that are either done or deleted for 30 days or more';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		Log::info("Retrieving bugs to archive");
		$bugs = Bug::whereNull("archived_at")
			->where(function($query) {
				$query->where("done_at", "<=", date('Y-m-d', strtotime(now() . '- 30 days')))
				->orWhere("deleted_at", "<=", date('Y-m-d', strtotime(now() . '- 30 days')));
			})
			->withTrashed()
			->get();

		foreach($bugs as $bug) {
			$bug->fill([
				"archived_at" => now()
			]);

			$bug->fireCustomEvent('bugArchived');
			$bug->save();
		}

        $this->info('Bugs archived successfully!');
    }
}
