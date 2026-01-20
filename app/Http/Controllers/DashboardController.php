<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;

class DashboardController extends Controller
{
    public function index(){
        return view('layouts', [
            'title' => 'Dahsboard' // <--- Tambahkan ini
        ]);
    }
}
