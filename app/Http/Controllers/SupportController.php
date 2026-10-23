<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class SupportController extends Controller {
 private function ready(): void {abort_unless(Schema::hasTable('support_tickets'),503,'Execute php artisan migrate para ativar o suporte.');}
 private function staff(Request $r): void {$this->ready();abort_unless(in_array($r->user()->lobby_role,['owner','moderator'],true),403);}
 private function owner(Request $r): void {$this->staff($r);abort_unless($r->user()->lobby_role==='owner',403);}
 public function create(Request $r){$this->ready();$data=$r->validate(['kind'=>'required|in:report_user,report_post,report_review,report_other,support','subject'=>'required|string|min:5|max:120','description'=>'required|string|min:15|max:4000','target_url'=>'nullable|string|max:500']);$data['user_id']=$r->user()->id;$data['created_at']=now();$data['updated_at']=now();$id=DB::table('support_tickets')->insertGetId($data);if (str_starts_with($data['kind'], 'report_') && filled(config('support.notifications_to'))) {
        try {
            $email = config('support.notifications_to');
            $body = "Nova denúncia no Lobby\n\n"
                ."Protocolo: #{$id}\n"
                ."Tipo: {$data['kind']}\n"
                ."Assunto: {$data['subject']}\n"
                ."Autor: @{$r->user()->username} (ID {$r->user()->id})\n\n"
                ."Descrição:\n{$data['description']}\n\n"
                ."Link informado: ".($data['target_url'] ?? 'Não informado')."\n\n"
                ."Acesse o painel administrativo para analisar a denúncia.";
            Mail::raw($body, function ($mail) use ($email, $id) {
                $mail->to($email)->subject("Lobby | Denúncia #{$id}");
            });
        } catch (\Throwable $e) {
            Log::error('Falha ao notificar denúncia por e-mail', [
                'ticket_id' => $id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    return redirect()->route('support.show',$id)->with('success','Solicitação enviada ao suporte.');}
 public function mine(Request $r){$this->ready();$tickets=DB::table('support_tickets')->where('user_id',$r->user()->id)->latest()->paginate(20);return view('support.mine',compact('tickets'));}
 public function show(Request $r,int $id){$this->ready();$ticket=DB::table('support_tickets')->where('id',$id)->first();abort_unless($ticket,404);abort_unless($ticket->user_id===$r->user()->id||in_array($r->user()->lobby_role,['owner','moderator'],true),403);$replies=DB::table('support_replies')->join('users','users.id','=','support_replies.user_id')->where('ticket_id',$id)->orderBy('support_replies.id')->get(['support_replies.*','users.username']);return view('support.show',compact('ticket','replies'));}
 public function reply(Request $r,int $id){$this->ready();$ticket=DB::table('support_tickets')->where('id',$id)->first();abort_unless($ticket,404);abort_unless($ticket->user_id===$r->user()->id||in_array($r->user()->lobby_role,['owner','moderator'],true),403);$data=$r->validate(['message'=>'required|string|min:2|max:4000']);DB::table('support_replies')->insert(['ticket_id'=>$id,'user_id'=>$r->user()->id,'message'=>$data['message'],'created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Mensagem enviada.');}
 public function dashboard(Request $r){$this->staff($r);$tickets=DB::table('support_tickets')->join('users','users.id','=','support_tickets.user_id')->select('support_tickets.*','users.username')->orderByRaw("CASE WHEN support_tickets.status='open' THEN 0 ELSE 1 END")->orderByDesc('support_tickets.id')->paginate(30);$users=User::orderByDesc('id')->limit(40)->get();$actions=DB::table('moderation_actions')->orderByDesc('id')->limit(30)->get();return view('admin.dashboard',compact('tickets','users','actions'));}
 public function status(Request $r,int $id){$this->staff($r);$data=$r->validate(['status'=>'required|in:open,in_review,resolved,closed']);DB::table('support_tickets')->where('id',$id)->update(['status'=>$data['status'],'assigned_to'=>$r->user()->id,'updated_at'=>now()]);return back()->with('success','Status atualizado.');}
 public function moderate(Request $r,User $user){$this->staff($r);$data=$r->validate(['action'=>'required|in:warn,suspend_1d,suspend_7d,suspend_30d,ban,unban,delete','reason'=>'required|string|min:5|max:1000']);abort_if(in_array($user->lobby_role,['owner','moderator'],true),403,'Não é permitido moderar membros da equipe.');abort_if($user->id===$r->user()->id,403);if($data['action']==='delete'){$this->owner($r);abort(422,'Exclusão definitiva requer fluxo separado de retenção de dados e confirmação. Use banimento por enquanto.');}DB::transaction(function()use($r,$user,$data){$a=$data['action'];if(str_starts_with($a,'suspend_')){$days=['suspend_1d'=>1,'suspend_7d'=>7,'suspend_30d'=>30][$a];$user->suspended_until=now()->addDays($days);}if($a==='ban')$user->banned_at=now();if($a==='unban'){$user->banned_at=null;$user->suspended_until=null;}$user->save();DB::table('moderation_actions')->insert(['actor_id'=>$r->user()->id,'target_user_id'=>$user->id,'action'=>$a,'reason'=>$data['reason'],'created_at'=>now(),'updated_at'=>now()]);});return back()->with('success','Ação registrada.');}
 public function roles(Request $r,User $user){$this->owner($r);$data=$r->validate(['role'=>'required|in:user,moderator']);abort_if($user->id===$r->user()->id,403);abort_if($user->lobby_role==='owner',403);if($data['role']==='moderator')abort_if(User::where('lobby_role','moderator')->where('id','!=',$user->id)->count()>=3,422,'Limite de três moderadores.');$user->lobby_role=$data['role'];$user->save();DB::table('moderation_actions')->insert(['actor_id'=>$r->user()->id,'target_user_id'=>$user->id,'action'=>'role_'.$data['role'],'reason'=>'Alteração de equipe','created_at'=>now(),'updated_at'=>now()]);return back()->with('success','Permissão atualizada.');}
}
