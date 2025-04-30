<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class Admin extends Model
{

    use HasFactory, HasApiTokens;

    protected $table = 'admins';
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_invitation_code',
    ];

    public function invitationCode()
    {
        return $this->belongsTo(InvitationCode::class, 'id_invitation_code');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
