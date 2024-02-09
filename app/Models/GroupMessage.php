<?php

namespace App\Models;

use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupMessage extends Model
{
    use HasFactory;
    protected $fillable = ['content', 'user_id','group_id']; 

        // Define the user relationship
        public function user()
        {
            return $this->belongsTo(User::class);
        }
    
        // Define the group relationship
        public function group()
        {
            return $this->belongsTo(Group::class);
        }
}
