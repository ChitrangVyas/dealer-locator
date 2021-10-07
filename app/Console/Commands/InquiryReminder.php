<?php

namespace App\Console\Commands;

use App\Http\Helpers;
use App\Models\Inquiry;
use App\Models\InquiryLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InquiryReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inquiry:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send nototification to user for reminder';

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
        $interval = 15;
        $current_date = Carbon::now()->addMinutes($interval)->format('Y-m-d H:i');
        $inquiry_logs = InquiryLog::where('is_reminder',1)->where('for_created_at', 'LIKE', "$current_date%")->get();
        $notification = '';
        $err = 1;
        foreach($inquiry_logs as $inquiry_log){
            $user_id = empty($inquiry_log->loggedBy->users_group_id)?NULL:$inquiry_log->logged_by;
            try{
                $notification = Helpers::storeNotification($user_id,$inquiry_log->getTitle(),0,2);
            }catch(Exception $e){
                $err = 0;
                continue;
            }
        }
        echo "$current_date\n";
        echo "Send Inquiry reminder....".Carbon::now()->toDateTimeString()."\n";
        if(!empty($err)){
            return 0;
        }else{
            return 1;
        }
    }
}
