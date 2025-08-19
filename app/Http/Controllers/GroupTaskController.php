<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class GroupTaskController extends Controller
{
   public function show(Group $group)
{
    return view('groups.tasks', compact('group'));
}

}
