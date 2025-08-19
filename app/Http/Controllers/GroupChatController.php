<?php

namespace App\Http\Controllers;

use App\Models\Group;

class GroupChatController extends Controller
{
    public function show(Group $group)
    {
        return view('groups.chat', compact('group'));
    }
}
