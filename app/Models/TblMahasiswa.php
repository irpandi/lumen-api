<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblMahasiswa extends Model
{
    protected $table   = 'tbl_mahasiswa';
    protected $guarded = ['id'];

    // * Relation Mahasiswa one to one User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
