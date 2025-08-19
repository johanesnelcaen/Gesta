<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentionNotification extends Model
{
    protected $fillable = ['user_id', 'message_id', 'read'];

    public function message()
    {
        return $this->belongsTo(GroupMessage::class);
    }
}
