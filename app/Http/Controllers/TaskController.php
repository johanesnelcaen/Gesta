<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Renvoie la vue contenant ton composant Livewire
        return view('task-manager.index');
    }
}
