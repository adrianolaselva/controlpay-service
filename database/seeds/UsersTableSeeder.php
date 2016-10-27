<?php

use Illuminate\Database\Seeder;

/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 26/10/2016
 * Time: 10:23
 */
class UsersTableSeeder extends Seeder
{
    public function run()
    {
        \App\Models\User::truncate();
        \App\Models\User::create([
            'name' => 'Desenv',
            'email' => 'desenv@ntk.com.br',
            'api_token' => base64_encode(sprintf("%s:%s", "desenv@ntk.com.br", "desenv")),
            'password' => \Illuminate\Support\Facades\Hash::make('desenv')
        ]);
    }

}