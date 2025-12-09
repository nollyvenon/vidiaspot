<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportCategoriesFromJiji;

class ImportCategoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:categories {--url=https://jiji.ng : The URL to import categories from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import categories and subcategories from jiji.ng';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = $this->option('url');
        
        $this->info("Starting category import from: {$url}");
        
        $job = new ImportCategoriesFromJiji($url);
        $job->onQueue('import');
        
        // Dispatch the job
        $job->dispatch();
        
        $this->info('Category import job has been dispatched to the queue.');
        $this->info('Check your queue processor to monitor the import progress.');
    }
}