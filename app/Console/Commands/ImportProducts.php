<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {--t|type=} {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from file or folder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');
        // $lines = [];
        switch ($this->option('type')) {
            case 'file':
                if (file_exists($path)) {
                    $fn = fopen($path, 'r');
                    $lines = [];
                    while (! feof($fn)){
                        $lines[] = fgets($fn);
                    }
                    fclose($fn);
                    dd($lines);
                    print($lines);
                }
                break;
            case 'folder':
                print("folder");
                break;          
            default:
                print("option non existent");
                break;
        }
        return 0;
    }
}
