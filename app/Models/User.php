<?php

namespace App\Models;

use App\Support\UsernameGenerator;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'username', 'pronouns', 'status', 'bio', 'location', 'favorite_genre', 'website', 'avatar_path', 'settings', 'cpf', 'birth_date'])]
#[Hidden(['password', 'remember_token', 'cpf', 'birth_date'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        // Todo usuário novo ganha um @usuario único (o cadastro não precisa pedir).
        static::creating(function (User $user) {
            if (blank($user->username)) {
                $user->username = UsernameGenerator::generate($user->name);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
            'birth_date' => 'date',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /** Quem eu sigo. */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    /** Quem me segue. */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    public function savedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_saves')->withTimestamps();
    }

    /** URL da foto de perfil (null = usa a inicial do nome). */
    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return route('avatar.show', ['user' => $this->id, 'v' => substr(md5($this->avatar_path), 0, 8)]);
    }

    /** Preferências de aparência já com os padrões aplicados. */
    public function appearance(): array
    {
        return \App\Support\Appearance::for($this);
    }

    /** Lê uma preferência salva em `settings` (notação com ponto, ex.: "privacy.show_library"). */
    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings ?? [], $key, $default);
    }

    /** Biblioteca pessoal (jogos jogados, avaliados e favoritos). */
    public function library(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_user')
            ->withPivot(['status', 'rating', 'hours_logged', 'review', 'is_favorite'])
            ->withTimestamps();
    }

    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    public function forumReplies(): HasMany
    {
        return $this->hasMany(ForumReply::class);
    }

    /** Itens de guias que a pessoa já marcou como feitos. */
    public function completedGuideItems(): BelongsToMany
    {
        return $this->belongsToMany(GuideItem::class, 'guide_item_user')->withTimestamps();
    }
}
