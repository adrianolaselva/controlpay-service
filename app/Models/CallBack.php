<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CallBack extends Model
{
    protected $table = 'callbacks';

    protected $fillable = ['id', 'file_id', 'method', 'host', 'api', 'params', 'body', 'created_at'];

    public $timestamps = false;
    
    public function file()
    {
        return parent::belongsTo(File::class, 'file_id', 'id');
    }
}