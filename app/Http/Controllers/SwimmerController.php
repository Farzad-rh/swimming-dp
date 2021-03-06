<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Course;
use App\User;
use App\Match;
use App\Swimmer;
class SwimmerController extends Controller
{


    public function index (){

           $swimmers = Swimmer::all();
           return $swimmers ;

    }

    public function logout(Request $request){

      if(Auth::check()){

        $request->session()->flush();
        return redirect()->intended('/login');
      }

    }

   public function dashboard(){
     $_user =  auth()->guard('web')->user();
     $role = $_user->app_role->name;
     if($role=='swimmer'){
       $swimmer = User::getUser($_user);
       $email =$_user->email;
       $my_courses = $swimmer->courses->load('teacher');
       $all_courses = Course::all()->load('teacher');
       if($swimmer->team!=null){
         $team = $swimmer->team;
         $my_matches=$team->matches->load('type');
         $team_members= Swimmer::where('team_id',$team->id)->get();
         $team_name =$team->name;
         $coach_name =$team->coach->firstName." ".$team->coach->firstName;
       } else {
         $team_name = null;
         $coach_name = null;
         $team_members = null;
         $my_matches =[];
       }
       $all_matches = Match::all()->load('type');
       $selected = 'selected';
       return view('swimmer.dashboard',compact('my_courses','all_courses','team_members','coach_name','team_name','swimmer','email','my_matches'));
     }
   }

   public function update(Request $request){
     $_user =  auth()->guard('web')->user();
     $role = $_user->app_role->name;
     if($role=='swimmer'){

    $user = User::getUser($_user);
    $this->validate($request,[

        'firstName' => 'required',
        'lastName' => 'required',
        'phoneNumber' => 'required',
        'lastName' => 'required',
        'nationalNumber'=>'required',
        'mobileNumber' => 'required',
        'gender' => 'required',
        'address' => 'required',
        'fatherName' => 'required'
       ]);

   $user->firstName = $request->input('firstName');
   $user->lastName = $request->input('lastName');
   $user->nationalNumber = $request->input('nationalNumber');
   $user->mobileNumber = $request->input('mobileNumber');
   $user->phoneNumber = $request->input('phoneNumber');
   $user->gender = $request->input('gender');
   $user->address = $request->input('address');
   $user->fatherName = $request->input('fatherName');
   $user->save();
   return redirect()->intended('swimmer/dashboard')->with('status', 'با موفقیت به روز شد');

     }

   }

   public function getSwimmer(Request $request){
        //national number
        $nn = $request->input('nn');
        $swimmer = Swimmer::where('nationalNumber',$nn)->doesnthave('team')->first();
        if($swimmer!=null){

            return $swimmer;
        }else{

            return "یافت نشد";
        }

   }

}
