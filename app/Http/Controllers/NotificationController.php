<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
class NotificationController extends Controller {
 public function index(Request $request): View { return view('notifications.index',['items'=>$request->user()->notifications()->paginate(20),'unread'=>$request->user()->unreadNotifications()->count()]); }
 public function readAll(Request $request): RedirectResponse { $request->user()->unreadNotifications()->update(['read_at'=>now()]); return back(); }
 public function open(Request $request,string $id): RedirectResponse { $notification=$request->user()->notifications()->whereKey($id)->firstOrFail(); $notification->markAsRead(); $url=$notification->data['url'] ?? route('notifications.index'); return redirect(Str::startsWith($url, '/') && !Str::startsWith($url, '//') ? $url : route('notifications.index')); }
}
