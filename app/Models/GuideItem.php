<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GuideItem extends Model
{
    protected $fillable = ['guide_id', 'title', 'position'];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    public function doneBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'guide_item_user')->withTimestamps();
    }
}
