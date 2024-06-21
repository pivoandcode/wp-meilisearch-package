<?php

namespace PivoAndCode\WordpressMeilisearch\Console;

use Roots\Acorn\Console\Commands\Command;
use PivoAndCode\WordpressMeilisearch\Facades\WordpressMeilisearch;

class WordpressMeilisearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordpress-meilisearch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'My custom Acorn command.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info(
            WordpressMeilisearch::getQuote()
        );
    }
}
