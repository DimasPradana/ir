<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
  //
  public function first(){
    return 'hello, this is me';
  }
  public function IRpdl(){
    $retribusi = DB::table('rekap_per_rekening')->get();
    return response()->json($retribusi);
  }
  public function IRpbb(){
    return 'hello, this is wu';
  }
  public function IRbphtb(){
    $result = DB::connection('mysql2')
      ->table('rekap')
      ->get();
    return response()->json($result);
  }
}
