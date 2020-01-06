<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\ListLead;
use App\ListAgent;

use Log;

class ListLeadsController extends Controller
{
  public function index()
  {
    $leads =ListLead::all();
    return response()->json($leads);
  }

  public function show(ListLead $listLead)
  {
    return response()->json($listLead);
  }


  //Test functions
  public function markComplete(ListLead $listLead) {
    $listLead->converted = true;
    $listLead->save();

    $agent = $listLead->listAgent;
    $agent->leads_given = false;
    $agent->save();

    return response()->json($listLead);
  }
}
