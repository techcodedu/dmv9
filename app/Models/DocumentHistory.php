<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Routing;

class DocumentHistory extends Model
{
    use HasFactory;

    protected $table = 'document_history';

    protected $fillable = [
        'document_id',
        'from_role',
        'to_role',
        'status',
        'created_at',
        'user_id'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
    public function routing()
    {
        return $this->hasOne(Routing::class, 'document_id', 'document_id');
    }

}
