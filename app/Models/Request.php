<?php
/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 25/10/2016
 * Time: 15:51
 */

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Request
 * @package App\Models
 */
class Request extends Model
{
    protected $table = 'requests';

    protected $fillable = [
        'id',
        'file_id',
        'req_method',
        'req_host',
        'req_api',
        'req_params',
        'req_body',
        'resp_status',
        'resp_body',
        'created_at'
    ];

    public $timestamps = false;

    public function file()
    {
        return parent::belongsTo(File::class, 'file_id', 'id');
    }

}