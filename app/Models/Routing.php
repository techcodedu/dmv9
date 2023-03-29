<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Document;
use App\Models\Office;

class Routing extends Model
{
    use HasFactory;

    protected $table = 'routing';
    protected $primaryKey = 'routing_id';

    const STATUS_DRAFT = 'draft';
    const STATUS_FORWARDED = 'forwarded';
    const STATUS_ENDORSED = 'endorsed';
    const STATUS_APPROVED = 'approved';
    const STATUS_SIGNED = 'signed';
    const STATUS_RELEASED = 'released';
    const STATUS_RECEIVED = 'received';



    protected $fillable = [
        'document_id',
        'from_office_id',
        'to_office_id',
        'forwarded_by_user_id',
        'status',
        'remarks',
        'date_forwarded',
        'date_endorsed',
        'endorsed_by_office_id',
        'date_approved',
        'approved_by_office_id',
        'date_signed',
        'signed_by_office_id',
        'date_released',
        'released_by_office_id'
    ];

    public function document()
    {
       return $this->belongsTo(Document::class, 'document_id');
    }

    public function forwardedBy()
    {
        return $this->belongsTo(User::class, 'forwarded_by_user_id');
    }

    public function fromOffice()
    {
        return $this->belongsTo(Office::class, 'from_office_id');
    }

    public function toOffice()
    {
        return $this->belongsTo(Office::class, 'to_office_id','id');
    }

    public function endorsedByOffice()
    {
        return $this->belongsTo(Office::class, 'endorsed_by_office_id');
    }

    public function approvedByOffice()
    {
        return $this->belongsTo(Office::class, 'approved_by_office_id');
    }

    public function signedByOffice()
    {
        return $this->belongsTo(Office::class, 'signed_by_office_id');
    }

    public function latestRouting()
    {
        return $this->hasOne(Document::class, 'document_id')->orderByDesc('created_at');
    }
  

}
