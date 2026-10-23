<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    /** @var array<string, string> */
    public const CATEGORIES = [
        'eventos' => 'Eventos',
        'lancamentos' => 'Lançamentos',
        'assinaturas' => 'Assinaturas',
        'plataformas' => 'Plataformas',
        'selecoes' => 'Seleções',
    ];

    protected $fillable = ['title', 'source', 'url', 'published_at', 'category', 'summary'];

    /** Link "Leia na fonte": matéria original (externo) ou página do próprio Lobby (começa com /). */
    public function href(): ?string
    {
        if (blank($this->url)) {
            return null;
        }

        return str_starts_with($this->url, '/') ? url($this->url) : $this->url;
    }

    public function isExternal(): bool
    {
        return filled($this->url) && ! str_starts_with($this->url, '/');
    }

    public function categoryLabel(): string
    {
        return __(self::CATEGORIES[$this->category] ?? ucfirst($this->category));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
