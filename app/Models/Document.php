<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Department;
use App\Models\Office;
use App\Models\Routing;
use App\Models\File;

class Document extends Model
{
    use HasFactory;

    const STATUS = ['draft', 'forwarded', 'endorsed', 'approved', 'signed', 'released'];

    protected $primaryKey = 'document_id';

    protected $fillable = [
        'document_type',
        'status',
        'current_owner_id',
        'date_created',
        'date_modified',
        'date_forwarded',
        'date_endorsed',
        'date_approved',
        'date_signed',
        'date_released',
        'department_id'
    ];

    public function currentOwner()
    {
        return $this->belongsTo(User::class, 'current_owner_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'document_id');
    }

    public function routing()
    {
        return $this->hasMany(Routing::class);
    }
    public function latestRouting()
    {
        return $this->hasOne(Routing::class, 'document_id')->orderByDesc('created_at');
    }
    
    

}
