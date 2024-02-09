<?php

namespace App\Models;

use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_conversations');
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }
}
