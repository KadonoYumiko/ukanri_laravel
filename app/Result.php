<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    /**
     * モデル主キー
     * @var int
     */
    protected $primaryKey = [ 'name', 'kadno' ];

    /**
     * 複数代入する属性 (データの代入が必須)
     *
     * @var array
     */
    protected $fillable = [ 'name', 'kadno' ];
}
