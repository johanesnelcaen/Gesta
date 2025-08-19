<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $fillable = ['group_id', 'user_id', 'message'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentions()
{
    return $this->hasMany(MentionNotification::class, 'message_id');
}

}
