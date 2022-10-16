<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany('App\Article')->withTimestamps();
        // 第⼆引数には中間テーブルのテーブル名,article_tag といった2つのモデル名の単数形で省略可能
        // 非省略形はbelongsToMany('App\Tag','article_tag'))
    }

    protected $fillable = [
        'name',
    ];
    
}
