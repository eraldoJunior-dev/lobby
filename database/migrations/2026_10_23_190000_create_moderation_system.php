<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  if (!Schema::hasColumn('users','lobby_role')) Schema::table('users', fn(Blueprint $t)=>$t->string('lobby_role')->default('user'));
  if (!Schema::hasColumn('users','suspended_until')) Schema::table('users', fn(Blueprint $t)=>$t->timestamp('suspended_until')->nullable());
  if (!Schema::hasColumn('users','banned_at')) Schema::table('users', fn(Blueprint $t)=>$t->timestamp('banned_at')->nullable());
  if (!Schema::hasTable('support_tickets')) Schema::create('support_tickets',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained('users');$t->string('kind');$t->string('subject');$t->text('description');$t->string('target_url')->nullable();$t->string('status')->default('open');$t->foreignId('assigned_to')->nullable();$t->timestamps();});
  if (!Schema::hasTable('support_replies')) Schema::create('support_replies',function(Blueprint $t){$t->id();$t->foreignId('ticket_id')->constrained('support_tickets');$t->foreignId('user_id')->constrained('users');$t->text('message');$t->timestamps();});
  if (!Schema::hasTable('moderation_actions')) Schema::create('moderation_actions',function(Blueprint $t){$t->id();$t->foreignId('actor_id')->constrained('users');$t->foreignId('target_user_id')->nullable();$t->string('action');$t->text('reason');$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('moderation_actions');Schema::dropIfExists('support_replies');Schema::dropIfExists('support_tickets');Schema::table('users',function(Blueprint $t){$t->dropColumn(['lobby_role','suspended_until','banned_at']);});}
};
