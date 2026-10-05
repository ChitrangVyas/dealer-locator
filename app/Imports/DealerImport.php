<?php

namespace App\Imports;

use App\Jobs\NotifyUserOfCompletedImport;
use App\Models\Branch;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use App\Models\Zone;
use App\Notifications\ImportHasFailedNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\ImportFailed;

class DealerImport implements ToModel,WithValidation,WithChunkReading,WithEvents,ShouldQueue,WithHeadingRow,SkipsEmptyRows
{
    use Importable,SkipsFailures,SkipsErrors;
    public $createdByuser;
    private $selectedrole;

    private $totalRecord,$totalSuccessRecord,$skiped ='';

    public function __construct()
    {
        //$this->createdByuser = $createdByuser; // created by user        

        $this->totalRecord = 0;
        $this->totalSuccessRecord = 0;
        $this->skiped = [];
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       
        try{
            $roleName = "Dealer";
            // $user = $this->createdByuser;
            $problemeticData = [];           

            $sympark_id  = $row['sympark_id'];       
            $name  = $row['dealer_name'];
            $Town = $row['town'];
            $address = $row['address'];            
            $State = $row['state'];
            $district = $row['district'];
            $zipcode = $row['pincode'];
            $latitude = $row['latitude'];            
            $longitude = $row['longitude'];
            $mobile_no = $row['mobile_no'];

            $insertData = array(
                'name' => $name, 
                'sympark_id' => $sympark_id, 
                'mobile' => $mobile_no,      
                'address'=>  $address,         
                'zipcode'=> $zipcode,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'created_at' => Date('Y-m-d H:i:s'),
                'updated_at' => Date('Y-m-d H:i:s'),   
                'status'=>1,
                'users_group_id'=>'2'
            );
            $categories = array( 'category_id' => '2');

            ++$this->totalRecord;

            /** problematic_json is contain the problemetic field with the reason */
            /** problematic fields are - gstin, Pan Number,Zone,Branch,Country,State,City */

            /** check for Unique GST - what to do if system have the same?  TSC per. has no gst*/
            // if(User::where('GSTIN','=',$GSTIN)->exists()){
            //     $problemeticData['GSTIN'] = "duplicate GST already there";                
            // }

            // Find zone from database and get it's id .. if not fund then leave it blank
                   
            // Find branch from database and get it's id .. if not fund then leave it blank
            $State  = State::where('name','=',$State)->first();
            if(!empty($State)){
                $insertData['state_id'] = $State->id;
                $insertData['country_id'] = $State->country_id;
            }else{
                $State  = State::where('name','=',$district)->first();
                $insertData['state_id'] = !empty( $State ) ? $State->id : NULL;
                $insertData['country_id'] = !empty( $State ) ? $State->country_id : NULL;
                $problemeticData['state_id'] = !empty( $State ) ? NULL : "no state added ".$State;
            }
            $Towns = City::where('name','=',$Town)->first();
            if(!empty($Towns)){
                $insertData['city_id'] = $Towns->id;
            }else{
                $City = City::create([
                    'name' => $Town,             
                    'state_id' => $State->id,
                ]);
                $insertData['city_id'] = $City->id;
            }  
            // last insert the request json and the problematic json
            $requsetJson = json_encode($insertData);
            $insertData['request_json'] = $requsetJson;
            $insertData['problematic_json'] = (!empty($problemeticData))?json_encode($problemeticData,true):NULL;            
            
            /** create user..! */
            DB::beginTransaction();
            $usersapExist = User::where('sympark_id','=',$sympark_id)->first();           
            // dd($usersapExist); 
            if(!empty($usersapExist))   {
                $usersapExist->dealerCategory()->sync($categories);
            }else{       
                $user = User::create($insertData);
                $user->dealerCategory()->sync($categories);
            }
            DB::commit();

            $this->totalSuccessRecord++;
        }catch(QueryException $e){
            DB::rollBack();
            $this->onError(throw new Exception(" Somethings wents wrong when user import record: ".json_encode($row)." Message :".$e->getMessage(), 1));
        }catch (Exception $e) {           
            DB::rollBack();
            $this->onError(throw new Exception($e->getMessage()." Last Record: ".json_encode($row), 1));
            // throw new ValidationException($e,$e->getTrace());
        }       
       
    }

    public function registerEvents(): array
    {
        return [
        ];
    }


    public function chunkSize(): int
    {
        return 1000;
    }

    /**
	 *
	 * @param \Throwable $e
	 *
	 * @return mixed
	 */
	function onError(\Throwable $e) {

	}

    /**
	 *
	 * @return array
	 */
	function rules(): array {
        return [
        ];
	}
}
