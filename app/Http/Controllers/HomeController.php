<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home', [
            'type' => 'home',
            'title' => 'Home',
            'data' => file_get_contents('../readme.md'),
        ]);
    }

    /**
     * Show the application Client construct lib.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showConstruct()
    {
        return view('home', [
            'type' => 'construct',
            'title' => 'Client construct lib',
            'data' => file_get_contents('../resources/descriptions/client-php-construct.md'),
        ]);
    }

    /**
     * Show the application Client queries lib.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showQueries()
    {
        return view('home', [
            'type' => 'queries',
            'title' => 'Client queries lib',
            'data' => file_get_contents('../resources/descriptions/client-php-query.md'),
        ]);
    }

    /**
     * Show the application Client token lib.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showToken()
    {
        return view('home', [
            'type' => 'token',
            'title' => 'Client token lib',
            'data' => file_get_contents('../resources/descriptions/client-php-auth.md'),
        ]);
    }
}
