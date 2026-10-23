<?php
namespace App\Support;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
final class LobbyNotifications {
 public static function send(User $to, User $actor, string $type, string $message, string $url): void {
   if ($to->id === $actor->id || !Schema::hasTable('notifications')) return;
   $settings = $to->setting('notifications', []);
   if (is_array($settings) && array_key_exists($type, $settings) && !$settings[$type]) return;
   DB::table('notifications')->insert(['id'=>(string)Str::uuid(),'type'=>'lobby.'.$type,'notifiable_type'=>$to->getMorphClass(),'notifiable_id'=>$to->id,'data'=>json_encode(['actor'=>$actor->name,'actor_id'=>$actor->id,'kind'=>$type,'message'=>$message,'url'=>$url],JSON_UNESCAPED_UNICODE),'created_at'=>now(),'updated_at'=>now()]);
 }
}
