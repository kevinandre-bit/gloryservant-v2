<?php
/*
* Workday - A time clock application for employees
* Email: official.codefactor@gmail.com
* Version: 1.1
* Author: Brian Luna
* Copyright 2020 Codefactor
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class LanguageController extends Controller
{
    public function lang($locale) 
    {
        App::setLocale($locale);
        session()->put('locale', $locale);

        return redirect()->back();
    }
}
