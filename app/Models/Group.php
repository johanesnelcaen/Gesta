<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;



class Group extends Model
{
   

    protected $fillable = ['name', 'owner_id'];

    // ✅ Relation avec les utilisateurs membres du groupe

    public function users()
{
    return $this->belongsToMany(User::class, 'group_user');
}


    // ✅ Relation avec l'admin/owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
public function members()
{
    return $this->belongsToMany(User::class);
}

public function tasks()
{
    return $this->hasMany(Task::class);
}

public function messages()
{
    return $this->hasMany(GroupMessage::class);
}


}
