<?php

namespace App\Console\Commands;

use App\Models\Product;
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
        switch ($this->option('type')) {
            case 'file':
                $this->ProductFileImport($path);
                break;
            case 'folder':
                $files = [];
                $handle = opendir($path);
                if ($handle) {
                    while (($entry = readdir($handle)) !== FALSE) {
                        $files[] = $entry;
                    }
                }
                closedir($handle);
                foreach (array_slice($files, 2) as $file) {
                    #TODO check if last char of $path is \ or not
                    $this->ProductFileImport($path . '\\' . $file);
                }
                break;          
            default:
                print("option non existent");
                break;
        }
        return 0;
    }

    private function ProductFileImport($filename)
    {
        if (file_exists($filename)) {
            $fn = fopen($filename, 'r');
            $lines = [];
            $existingProducts = [];

            while (! feof($fn)){
                $lines[] = fgets($fn);
            }
            fclose($fn);

            array_shift($lines);

            $fornecedor = basename($filename, ".csv");
            foreach ($lines as $line) {
                if (!empty($line)) {
                    print("Starting import of: " . $filename . "\n");
                    $details = explode(';', $line);
                    $product_code = strtoupper($fornecedor . "-" . trim($details[0]));
                    $product_description = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', trim($details[1]));
                    $product_ean = trim($details[2]);
                    if (empty($product_ean)) {
                        $product_ean = $product_code;
                    }
                    $product_price = str_replace(',','.', trim($details[3]));
                    $prod = Product::where('ProductNumberCode', $product_ean)->first();
                    if ($prod == null) {
                        Product::create([
                            'ProductCode' => $product_code,
                            'ProductDescription' => $product_description,
                            'ProductNumberCode' => $product_ean,
                            'PriceCost' => floatval($product_price),
                        ]);
                        print("Inserted product: " . $product_code . "\n");
                    } else {
                        $existingProducts[] = $line;
                    }
                }
            }
            print("Finished import of: " . $filename . "\n");
            if (!empty($existingProducts)) {
                $existingProductsFile = pathinfo($filename)['dirname'] . "\\existingProducts.csv";
                file_put_contents($existingProductsFile, $existingProducts, FILE_APPEND);
            }
        }
    }
}
