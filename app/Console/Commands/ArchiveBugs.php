<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
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
		$bugs = Bug::where("archived_at", NULL)
					->where("deleted_at", "<=", date('Y-m-d', strtotime(now() . ' - 30 days')))
					->orWhere("done_at", "<=", date('Y-m-d', strtotime(now() . ' - 30 days')))
					->get();

		$bugs->update([
			"archived_at" => now()
		]);

        $this->info('Bugs archived successfully!');
    }
}
