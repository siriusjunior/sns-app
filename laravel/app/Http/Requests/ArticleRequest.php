<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:50',
            'body' => 'required|max:500',
            'tags' => 'json|regex:/^(?!.*\s).+$/u|regex:/^(?!.*\/).*$/u',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'タイトル',
            'body' => '本文',
            'tags' => 'タグ',
        ];
    }

    // フォームリクエストのバリデーションが成功した後に自動的に呼ばれるメソッド
    // 処理前
    // "[{"text":"USA","tiClasses":["ti-valid"]},{"text":"France","tiClasses":["ti-valid"]}]" 
    // ↓
    // json_decode処理後、コレクションに変換し、コレクションメソッドを適用したのち値を取出し、$this->tagsを更新
    // [['text' => 'USA','tiClasses' => ['ti-valid']],],[['text' => 'France','tiClasses' => ['ti-valid']],]
    // mapは新しいコレクション(タグの名前部分)を形成。['USA', 'France']
    public function passedValidation()
    {
        $this->tags = collect(json_decode($this->tags))
            ->slice(0,5)
            ->map(function($requestTag){
                return $requestTag->text;
            });
    }
}
