<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(){
        return "Aquí se mostraran todos los datos";
    }
}
