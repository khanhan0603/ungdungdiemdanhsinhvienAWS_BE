<?php

namespace App\Http\Controllers;

use App\Models\Nganh;
use Illuminate\Http\Request;

class NganhController extends Controller
{
    public function listnganh(){
       return response()->json(Nganh::all());
    }
}
