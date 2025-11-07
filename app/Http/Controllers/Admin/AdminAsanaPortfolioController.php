<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminAsanaPortfolioController extends Controller
{
    /**
     * This method renders the Asana portfolio page for the admin.
     * It simply returns the 'admin.asana-portfolio' view where the admin can see the Asana portfolio.
     * 
     * @return \Illuminate\View\View The Asana portfolio view
     */
    public function index()
    {
        // Return the Asana portfolio page view for the admin
        return view('admin.asana-portfolio');
    }
}
