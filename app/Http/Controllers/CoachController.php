<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Course;
use App\User;
use App\Match;
use App\Coach;
use App\Swimmer;
use App\Team;
class CoachController extends Controller
{

  public function dashboard(){
    $_user =  auth()->guard('web')->user();
    $role = $_user->app_role->name;
    if($role=='coach'){
      $swimmer = User::getUser($_user);
      $email =$_user->email;
      if($swimmer->team!=null){
        $team = Team::where('coach_id',$swimmer->id)->first();
        $swimmer =$team->coach;
        $my_matches=$team->matches->load('type');;
        $team_members= Swimmer::where('team_id',$team->id)->get();
        $team_name =$team->name;
        $coach_name =$team->coach->firstName." ".$team->coach->lastName;

      } else {
        $team = [];
        $my_matches=[];
        $team_name=null;
        $coach_name =null;
        $team_members=null;
      }
      $all_matches = Match::all()->load('type');
      $selected = 'selected';
      return view('coach.dashboard',compact('team_name','swimmer','email','my_matches','coach_name','team_members'));
    }
  }
  public function update(Request $request){
    $_user =  auth()->guard('web')->user();
    $role = $_user->app_role->name;
    if($role=='coach'){

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
  return redirect()->intended('coach/dashboard')->with('status', 'با موفقیت به روز شد');
    }

  }
  public function logout(Request $request){

    if(Auth::check()){

      $request->session()->flush();
      return redirect()->intended('/login');
    }

  }
  public function createTeam(Request $request){

      $_user =  auth()->guard('web')->user();
      $role = $_user->app_role->name;
      if($role=='coach'){

          $this->validate($request,[

              'name' => 'required'
          ]);
          $user = User::getUser($_user);
          $team = new Team();
          $team->name = $request->name;
          $team->coach_id =$user->id ;
          $team->save();
          return redirect()->intended('coach/dashboard')->with('status', 'تیم با موفقیت اضافه شد');
      }

  }
}
