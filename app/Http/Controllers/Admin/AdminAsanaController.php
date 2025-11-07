<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AdminAsanaController extends Controller
{
    /**
     * This method fetches tasks from Asana API and displays them on the admin's Asana dashboard.
     * It uses Guzzle to send a GET request to the Asana API, retrieves task data,
     * and passes that data to the view for rendering.
     * 
     * @return \Illuminate\View\View The Asana dashboard view with tasks data
     */
    public function index()
    {
        // Create a new Guzzle HTTP client instance to make the API request
        $client = new Client();

        // Send a GET request to the Asana API to retrieve tasks for a specific project
        $response = $client->get('https://app.asana.com/api/1.0/projects/1209912987048387/tasks?opt_fields=name,completed,assignee.name,due_on,created_at,modified_at', [
            'headers' => [
                // Authorization header with a Bearer token for API access
                'Authorization' => 'Bearer 2/1199657042504744/1209930182683047:9334cc0d9178118c838ac2b608da0b30',
            ]
        ]);

        // Decode the JSON response from Asana into an associative array
        $data = json_decode($response->getBody()->getContents(), true);

        // Extract task data from the decoded response
        $tasks = $data['data'];

        // Return the 'admin.asana' view, passing the tasks data to be displayed
        return view('admin.asana', compact('tasks')); // or 'admin.asana-dashboard' if your file is named that
    }

}
