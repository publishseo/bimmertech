<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareVersion extends Model
{
    /** @use HasFactory<\Database\Factories\SoftwareVersionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'system_version',
        'system_version_alt',
        'link',
        'st',
        'gd',
        'latest',
    ];
}
