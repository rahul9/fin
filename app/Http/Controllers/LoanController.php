<?php
namespace App\Http\Controllers;
use App\Models\Loans;
use App\Models\Emis;
use Illuminate\Http\Request;
use App\Exceptions\LogException as LogException;
use Illuminate\Support\Facades\Validator;
use App\Libraries\Utils as Utils;
use Exception;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function apply(Request $request){
        try{
            
            $inputs = $request->all(); 
            $user_id=Auth::guard('api')->user()->type; 
           
            $Validator = Validator::make($inputs, [
                'amount' => 'required',
                'term' => 'required',
               
            ]);
            if ($Validator->fails()) {
                $errors = $Validator->messages()->all();
                return Utils::sendFailedResponse([], implode(",",$errors));
            }
            $req_res = new Loans();
            $req_res->amount=$inputs['amount'];
            $req_res->term=$inputs['term'];
            $req_res->pending_emi=$inputs['term'];
            $req_res->user_id=Auth::guard('api')->user()->id;
            $req_res->save();
            return Utils::sendSuccessResponse($req_res,'request submit successfully');
        }catch(Exception $ex){
            return LogException::logEexceptionError($ex,$request);
        }
    }

    public function getPendingLoan(Request $request){
        try{
            $inputs = $request->all();
            $userType=Auth::guard('api')->user()->type; 
       
            if($userType !='A'){
                return Utils::sendFailedResponse([], "access denied");
            }

          $pending=  Loans::where('status', 'pending')->get();
          return Utils::sendSuccessResponse($pending,'loan details returned successfully');
         }catch(Exception $ex){
            return LogException::logEexceptionError($ex,$request);
        }
    }

    public function approvedLoan(Request $request){
        try{
            $inputs = $request->all();
            $userType=Auth::guard('api')->user()->type;
            $inputs['user_id']=Auth::guard('api')->user()->id; 
            
            if($userType !='A'){
                return Utils::sendFailedResponse([], "access denied");
            }

          $Validator = Validator::make($inputs, [
            'loanId' => 'required',
           
        ]);
        if ($Validator->fails()) {
            $errors = $Validator->messages()->all();
            return Utils::sendFailedResponse([], implode(",",$errors));
        }
          $pending=  Loans::where('status', 'pending')->where("id",$inputs["loanId"])->first();
          
         if(empty($pending)){
            return Utils::sendFailedResponse($pending,'Loan Id not found');
          }
          $data = [];
          for($i=0;$i<$pending->term;$i++){
            $d=$i*7;
            $data[] = ['amount'=>(int)($pending->amount/$pending->term),'loan_id'=>$pending->id, 'user_id'=>$inputs['user_id'],"repayments"=>date('Y-m-d',strtotime("+$d day")) ,'term_id'=>1+$i ,'status' => 'pending'];
          }
          $emis= new Emis; 
          $emis::insert($data);
          $pending->status="approved";
          $pending->approved_by=$inputs['user_id'];
          
          $pending->save();
          return Utils::sendSuccessResponse($pending,'your Loan has been approved');
         }catch(Exception $ex){
            return LogException::logEexceptionError($ex,$request);
        }
    }

    public function payEmi(Request $request){
        try{         
            $inputs = $request->all();   
            $inputs['type']=Auth::guard('api')->user()->type;
            $inputs['user_id']=Auth::guard('api')->user()->id; 
            $Validator = Validator::make($inputs, [
            'loanId' => 'required',
            'termId' => 'required',
        ]);
        if ($Validator->fails()) {
            $errors = $Validator->messages()->all();
            return Utils::sendFailedResponse([], implode(",",$errors));
        }
        $data=  Emis::where('status', 'pending')->where("loan_id",$inputs["loanId"])->where("term_id",$inputs["termId"])->first();
        if(empty($data)){
            return Utils::sendFailedResponse([],'Loan Id & term id not found');
        }
        $data->status="completed";
        $data->save();

        $pending=  Loans::where("id",$inputs["loanId"])->first();

        if((int)$pending->pending_emi-1==0){
            $pending->status ='completed'; 
        } 
        $pending->pending_emi=$pending->pending_emi-1;
        $pending->save();
        return Utils::sendSuccessResponse($pending,'EMI submit successfully');
        }catch(Exception $ex){
            return LogException::logEexceptionError($ex,$request);
        }
    }


}
