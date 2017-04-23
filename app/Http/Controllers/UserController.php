<?php

namespace App\Http\Controllers;

use App\StatisticsEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {

    public function index() {
        // Get statistics
        $stats = StatisticsEntry::first();
        // Get job queue length
        $jobCount = DB::table('jobs')->count();
        return view('user.index', ['stats' => $stats, 'job_count' => $jobCount]);
    }

    public function generateApiKey() {
        $new_api_token = str_random(60);
        $user = Auth::user();
        $user->api_token = $new_api_token;
        $user->save();
        return back()->with('status', 'New API Key generated!');
    }

}
