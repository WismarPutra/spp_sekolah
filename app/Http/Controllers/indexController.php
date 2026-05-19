<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class indexController extends Controller
{
    public function index()
    {
        // Mengirim variabel nama file ke view
        return view('index', ['nama' => 'images-removebg-preview.png']);
    }
}