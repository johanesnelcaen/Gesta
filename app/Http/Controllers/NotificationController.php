<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class NotificationController extends Controller
{
  public function index()
{
    return view('notifications.index');
}


}
