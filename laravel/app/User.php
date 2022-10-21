<?php

namespace App;


use App\Mail\BareMail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token, new BareMail()));
    }

    public function articles(): HasMany
    {
        return $this->hasMany('App\Article');
    }

    // 当該Userをフォローしている関連を取得
    public function followers(): BelongsToMany
    {
        //第1引数に関係モデル,
        //第2引数に中間テーブル名,
        //第3引数にリレーション元にあるid(フォローされている人(self),followsテーブルよりUsersのFKで指定),
        //第4引数にリレーション先にある,紐づいているid(フォローしている人たち(能動者),followsテーブルよりUsersのFKで指定),
        //中間テーブルより第3引数に紐づいている多数の第4引数のレコード
        return $this->belongsToMany('App\User', 'follows', 'followee_id', 'follower_id')->withTimestamps();
    }
    
    // 当該Userがフォローしている関連を取得
    public function followings(): BelongsToMany
    {
        //第1引数に関係モデル,
        //第2引数に中間テーブル名,
        //第3引数にリレーション元にあるid(フォローしている人(self),followsテーブルよりUsersのFKで指定),
        //第4引数にリレーション先にある,紐づいているid(フォローされている人たち(受動者),followsテーブルよりUsersのFKで指定),
        //中間テーブルより第3引数に紐づいている多数の第4引数のレコード
        return $this->belongsToMany('App\User', 'follows', 'follower_id', 'followee_id')->withTimestamps();
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany('App\Article','likes')->withTimestamps();
    }

    public function isFollowedBy(?User $user): bool
    {
        return $user
        ? (bool)$this->followers->where('id', $user->id)->count()
        : false;
    }

    public function getCountFollowersAttribute(): int
    {
        return $this->followers->count();
    }
    
    public function getCountFollowingsAttribute(): int
    {
        return $this->followings->count();
    }
}
