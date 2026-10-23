<?php

namespace App\Http\Controllers;

use App\Models\NewsItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $category = array_key_exists((string) $request->query('categoria'), NewsItem::CATEGORIES)
            ? (string) $request->query('categoria')
            : '';

        $items = NewsItem::query()
            ->when($category !== '', fn ($q) => $q->where('category', $category))
            ->latest('published_at')
            ->latest('id')
            ->get();

        return view('news.index', [
            'items' => $items,
            'category' => $category,
            'updatedAt' => NewsItem::latest('published_at')->first()?->published_at,
        ]);
    }
}
