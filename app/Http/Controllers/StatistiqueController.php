<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;


class StatistiqueController extends Controller
{
    public function index()
    {
        $total = Auth::user()->tasks()->count();
        $completed = Auth::user()->tasks()->where('is_completed', 1)->count();
        $late = Auth::user()->tasks()
            ->where('end', '<', now())
            ->where('is_completed', 0)
            ->count();

        return view('statistiques.index', compact('total', 'completed', 'late'));
    }    }

