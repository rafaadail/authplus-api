<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenApi\Generator;

class GenerateOpenApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openapi:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate OpenAPI documentation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating OpenAPI documentation...');

        $generator = new Generator();

        $openApi = $generator->generate([
            app_path('Http/Controllers'),
            app_path('Swagger'),
        ]);

        $file = base_path('public/openapi.json');
        file_put_contents($file, $openApi->toJson());

        $this->info("OpenAPI generated successfull at: $file");
    }
}
