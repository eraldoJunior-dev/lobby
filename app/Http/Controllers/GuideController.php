<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\GuideItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function index(Request $request): View
    {
        $me = $request->user();
        $type = array_key_exists((string) $request->query('tipo'), Guide::TYPES) ? (string) $request->query('tipo') : '';
        $search = $request->string('q')->trim()->limit(80, '')->value();

        $guides = Guide::query()
            ->with('game')
            ->withCount('items')
            ->withCount(['items as done_count' => fn (Builder $q) => $q->whereHas(
                'doneBy', fn (Builder $w) => $w->where('users.id', $me->id)
            )])
            ->when($type !== '', fn (Builder $q) => $q->where('type', $type))
            ->when($search !== '', function (Builder $q) use ($search) {
                $like = '%'.$search.'%';

                $q->where(fn (Builder $w) => $w
                    ->where('title', 'like', $like)
                    ->orWhereHas('game', fn (Builder $g) => $g->where('title', 'like', $like))
                );
            })
            ->orderBy('id')
            ->get();

        return view('guides.index', [
            'guides' => $guides,
            'type' => $type,
            'search' => $search,
        ]);
    }

    public function show(Request $request, Guide $guide): View
    {
        $guide->load('game');
        $items = $guide->items()->orderBy('position')->orderBy('id')->get();

        $doneIds = $request->user()->completedGuideItems()
            ->whereIn('guide_items.id', $items->pluck('id'))
            ->pluck('guide_items.id')
            ->all();

        return view('guides.show', [
            'guide' => $guide,
            'items' => $items,
            'doneIds' => $doneIds,
        ]);
    }

    /** Marca/desmarca um item do checklist. */
    public function toggle(Request $request, GuideItem $item): RedirectResponse
    {
        $request->user()->completedGuideItems()->toggle($item->id);

        return redirect()->to(route('guides.show', $item->guide).'#item-'.$item->id);
    }
}
