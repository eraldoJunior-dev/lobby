<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumThread extends Model
{
    protected $fillable = ['game_id', 'user_id', 'title', 'body', 'votes', 'is_solved'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['is_solved' => 'boolean'];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class)->oldest('id');
    }
}
