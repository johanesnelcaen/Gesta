<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start',
        'end',
        'user_id',
        'group_id',
        'assigned_to',    // si tu veux gérer un utilisateur assigné différent du créateur
        'is_completed',
        'is_project',
        'parent_id',      // pour les sous-tâches
        'notified',
        'is_urgent',
        'owner_id'
    ];

    protected $casts = [
        'notified' => 'boolean',
        'is_completed' => 'boolean',
        'is_project' => 'boolean',
        'is_urgent' => 'boolean',
    ];

    // 🔹 Relation : une tâche peut avoir plusieurs sous-tâches
    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    // 🔹 Relation : une sous-tâche appartient à une tâche parente
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    // 🔹 Relation : une tâche appartient à un groupe
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // 🔹 Relation : une tâche appartient à un utilisateur (créateur ou propriétaire)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔹 Relation : une tâche peut être assignée à un utilisateur spécifique
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
