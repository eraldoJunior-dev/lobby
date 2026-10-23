<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guide extends Model
{
    /** @var array<string, string> */
    public const TYPES = ['platina' => 'Platina', '100' => '100%', 'conquistas' => 'Conquistas'];

    protected $fillable = ['game_id', 'title', 'slug', 'type', 'difficulty', 'hours_label', 'summary'];

    public function typeLabel(): string
    {
        return __(self::TYPES[$this->type] ?? $this->type);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GuideItem::class);
    }
}
