<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationCode extends Model
{
    protected $table = 'invitation_codes';
    protected $fillable = [
        'code',
        'used_status',
        'created_at',
        'expires_at',
    ];

    protected $casts = [
        'used_status' => 'boolean',
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_codigo_invitacion');
    }
}
