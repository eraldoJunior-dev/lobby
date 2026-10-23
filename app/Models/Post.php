<?php

namespace App\Models;

use App\Enums\PostCategory;
use App\Enums\ReactionType;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = ['game_id', 'rating', 'category', 'title', 'body', 'contains_spoilers'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => PostCategory::class,
            'contains_spoilers' => 'boolean',
            'rating' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_saves')->withTimestamps();
    }

    /**
     * Contagem e estado ("eu reagi?") de cada tipo de reação.
     * Usa a relação `reactions` já carregada (eager load) para não gerar N+1.
     *
     * @return array<string, array{count: int, active: bool}>
     */
    public function reactionSummary(?int $userId): array
    {
        $summary = [];

        foreach (ReactionType::cases() as $type) {
            $of = $this->reactions->where('type', $type->value);

            $summary[$type->value] = [
                'count' => $of->count(),
                'active' => $userId !== null && $of->contains('user_id', $userId),
            ];
        }

        return $summary;
    }
}
