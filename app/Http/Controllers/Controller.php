<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs; // ✅ Trait to dispatch jobs
use Illuminate\Routing\Controller as BaseController; // ✅ Base controller class for routing
use Illuminate\Foundation\Validation\ValidatesRequests; // ✅ Trait to handle form request validation
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ✅ Trait to manage authorization logic

/**
 * This is the base controller class.
 * It uses the necessary traits for authorization, job dispatching, and request validation.
 */
class Controller extends BaseController
{
    // Use the AuthorizesRequests trait for handling authorization logic
    use AuthorizesRequests, 
        // Use the DispatchesJobs trait to dispatch jobs
        DispatchesJobs, 
        // Use the ValidatesRequests trait to handle form validation
        ValidatesRequests;
}
