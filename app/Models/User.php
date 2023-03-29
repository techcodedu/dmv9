<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Notifications\Notifiable;
use App\Models\Office;
use App\Models\Department;

class User extends Model implements Authenticatable
{
    use HasFactory, AuthenticableTrait,Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'department_id', 'office_id'
    ];

    protected $hidden = [
        'password'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
