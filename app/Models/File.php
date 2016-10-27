<?php
/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 27/10/2016
 * Time: 13:11
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * @package App\Models
 */
class File extends Model
{
    protected $table = 'files';

    protected $fillable = ['id', 'identifier', 'reference', 'name', 'content', 'created_at'];

    public $timestamps = false;

    public function requests()
    {
        return $this->hasMany(Request::class, 'file_id', 'id');
    }

    public function callBacks()
    {
        return $this->hasMany(CallBack::class, 'file_id', 'id');
    }

}