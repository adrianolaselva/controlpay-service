<?php
/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 25/10/2016
 * Time: 15:51
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Request
 * @package App\Models
 */
class Request extends Model
{
    protected $attributes = ['cnpj','api', 'method', 'status_code', 'body', 'response_body'];
}