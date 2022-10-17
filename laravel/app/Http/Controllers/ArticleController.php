<?php

namespace App\Http\Controllers;

use App\Article;
use App\Tag;
use App\Http\Requests\ArticleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // 初期処理に必ず実⾏
    public function __construct()
    {
        // 第二引数はルーティングパラメータ
        $this->authorizeResource(Article::class, 'article');
    }

    public function index()
    {
        $articles = Article::all()->sortByDesc('created_at');
        return view('articles.index',['articles'=> $articles]);
    }

    public function create()
    {
        $allTagNames = Tag::all()->map(function($tag){
            return ['text' => $tag->name];
        });
        return view('articles.create',[
            'allTagNames' => $allTagNames,
        ]);
    }

    public function store(ArticleRequest $request, Article $article)
    {
        $article->title = $request->title;
        $article->body = $request->body;

        // Articleモデルの指定プロパティのみが安全に代⼊
        $article->fill($request->all());
        
        // ログイン済みのユーザーが送信したリクエストをたどるuserメソッド
        $article->user_id = $request->user()->id;
        $article->save();

        // passedValidationで整形したタグ名の配列が返ってきてる?? ['USA', 'France']
        $request->tags->each(function ($tagName) use ($article){
            $tag = Tag::firstOrCreate(['name' => $tagName]); //パラメータを指定,モデル$fillable確認
            $article->tags()->attach($tag); //記事とタグarticle_tagレコードへの保存
        });
        return redirect()->route('articles.index');
    }

    public function edit(Article $article)
    {
        // VueTagsInputのタグ表示のため、textキーの配列$tagNamesに戻す
        $tagNames = $article->tags->map(function($tag){
            return ['text' => $tag->name];
        });
        $allTagNames = Tag::all()->map(function($tag){ //タグ数が膨⼤になったらパフォーマンス上絞った⽅が良い
            return ['text' => $tag->name];
        });
        return view('articles.edit', [
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();
        //中間テーブルのレコードarticle_tag全削除
        $article->tags()->detach();
        $request->tags->each(function($tagName)use($article){
            $tag = Tag::firstOrCreate(['name'=>$tagName]);
            $article->tags()->attach($tag); //article_tagを作成
        });
        return redirect()->route('articles.index');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    public function show(Article $article)
    {
        return view('articles.show',['article' => $article]);
    }

    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);
        return [
            'id'=>$article->id,
            'countLikes'=>$article->count_likes,
        ];
    }

    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        return [
            'id'=>$article->id,
            'countLikes'=>$article->count_likes,
        ];
    }
}
