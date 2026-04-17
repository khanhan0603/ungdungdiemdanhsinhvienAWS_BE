<?php

namespace App\Http\Controllers;

use App\Models\Lop;
use Illuminate\Http\Request;

class LopController extends Controller
{
    public function listlop(){
        $lops = Lop::with('nganh')->get();
       return response()->json($lops);
    }
}
