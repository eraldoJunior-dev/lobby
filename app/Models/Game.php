<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'title', 'slug', 'year', 'genre', 'rating', 'hours_to_beat',
        'cover_url', 'cover_from', 'cover_to', 'members_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
        ];
    }

    /**
     * Endereço da capa. Ordem: arquivo local em public/img/covers/{slug}.jpg
     * (ver `php artisan lobby:covers`) > cover_url do banco > mapa em config/covers.php.
     */
    public function coverSrc(): ?string
    {
        if (! $this->slug) {
            return $this->cover_url && preg_match('#^https?://#i', $this->cover_url) ? $this->cover_url : null;
        }

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (is_file(public_path("img/covers/{$this->slug}.{$ext}"))) {
                return asset("img/covers/{$this->slug}.{$ext}");
            }
        }

        $remote = $this->cover_url ?: config("covers.{$this->slug}.0");

        return $remote && preg_match('#^https?://#i', $remote) ? $remote : null;
    }

    /** Alternativas de capa caso a principal não carregue (usadas pelo navegador). */
    public function coverFallbacks(): array
    {
        if (! $this->slug) {
            return [];
        }

        return array_values(array_filter(array_slice((array) config("covers.{$this->slug}", []), 1)));
    }

    /** Gênero traduzido para o idioma ativo. */
    public function genreLabel(): ?string
    {
        return $this->genre ? __($this->genre) : null;
    }

    /** Biblioteca de quem adicionou o jogo (status, nota, resenha…). */
    public function libraryEntries(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_user')
            ->withPivot(['status', 'rating', 'hours_logged', 'review', 'is_favorite'])
            ->withTimestamps();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    public function guides(): HasMany
    {
        return $this->hasMany(Guide::class);
    }
}
