<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportLatestProductsFromJiji;
use App\Models\ProductImportSettings;

class ImportLatestProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:latest-products {--days=3 : Number of days to import products from} {--force : Force import even if not scheduled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import latest products from jiji.ng based on configured days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $force = $this->option('force');
        
        $this->info("Starting latest product import from jiji.ng for last {$days} days");
        
        // Get settings to check if import is enabled
        $settings = ProductImportSettings::getCurrentSettings();
        
        if (!$force && !$settings->import_enabled) {
            $this->error("Product import is currently disabled in settings.");
            return;
        }
        
        if (!$force && !$settings->isTimeToImport()) {
            $this->info("It's not time to import yet based on the configured interval.");
            $nextImport = $settings->last_import_time->addHours($settings->import_interval_hours);
            $this->info("Next import scheduled for: {$nextImport}");
            return;
        }
        
        $job = new ImportLatestProductsFromJiji($days);
        $job->onQueue('import');
        
        // Dispatch the job
        $job->dispatch();
        
        // Update the last import time
        $settings->last_import_time = now();
        $settings->save();
        
        $this->info('Latest product import job has been dispatched to the queue.');
        $this->info('Check your queue processor to monitor the import progress.');
    }
}