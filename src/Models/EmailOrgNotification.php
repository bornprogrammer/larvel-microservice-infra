<?php

namespace Laravel\Infrastructure\Models;

use Laravel\Infrastructure\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Infrastructure\Models\EmailType;

class EmailOrgNotification extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['email_slug_id', 'org_id', 'is_enabled', 'subject', 'event_description', 'is_audit', 'content', 'is_approval_process'];

    public function emailType()
    {
        return $this->belongsTo(EmailType::class, 'email_slug_id', 'id');
    }
}
