<?php

namespace App\Console\Commands;

use App\Imports\DealerImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SaleThroughSymphonySAP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sale-through-symphony-sap:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will pull number of sales through symphony from the provided or uploaded file from server.';

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
        //dd("hey",Storage::disk('symfony')->exists('/TestCustomer/sales_data_format.xlsx'));
        // Create a disk instance for the FTP server       
        if(Storage::disk('symfony')->exists('/dealer-import/Surround & Duet Dealer List 08-07-2023 (1).xlsx')){     
            //dd("kinjal");
            (new DealerImport(auth()->user()))->import('/dealer-import/Surround & Duet Dealer List 08-07-2023 (1).xlsx','symfony',\Maatwebsite\Excel\Excel::XLSX);
            return "File has been added";            
        }else{
            return "File path not found";
        }
        // return $return_array = ['code'=>0,'message'=>"Something went wrong"];  
        return 0;
    }
}
