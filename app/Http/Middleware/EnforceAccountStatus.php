<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
class EnforceAccountStatus {
 public function handle(Request $request,Closure $next): Response {
  $user=$request->user();
  if($user && Schema::hasColumn('users','banned_at')) {
   $banned=$user->banned_at!==null;
   $suspended=$user->suspended_until && now()->lessThan(\Illuminate\Support\Carbon::parse($user->suspended_until));
   if($banned || $suspended){Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();abort(403,'Sua conta está suspensa. Entre em contato com o suporte.');}
  }
  return $next($request);
 }
}
