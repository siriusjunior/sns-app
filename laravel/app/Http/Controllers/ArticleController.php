<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        // テーブル内容を模したダミーデータ
        $articles = [
            (object)[
                'id'=>1,
                'title'=>'タイトル１',
                'body'=>'本文１',
                'created_at'=>now(),
                'user'=>(object)[
                    'id'=>1,
                    'name'=>'ユーザー名１',
                ],
            ],
            (object)[
                'id'=>2,
                'title'=>'タイトル２',
                'body'=>'本文２',
                'created_at'=>now(),
                'user'=>(object)[
                    'id'=>2,
                    'name'=>'ユーザー名２',
                ],
            ],
            (object)[
                'id'=>3,
                'title'=>'タイトル３',
                'body'=>'本文３',
                'created_at'=>now(),
                'user'=>(object)[
                    'id'=>3,
                    'name'=>'ユーザー名３',
                ],
            ],
        ];

        return view('articles.index',['articles'=> $articles]);
    }
}
