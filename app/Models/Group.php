<?php

namespace App\Models;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;
    protected $fillable = ['name','user_id'];


    public function conversations()
    {
        return $this->belongsToMany(Conversation::class)->withTimestamps();
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_group');
    }
}
