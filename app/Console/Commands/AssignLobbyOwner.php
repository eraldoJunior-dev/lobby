<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
class AssignLobbyOwner extends Command {
 protected $signature='lobby:assign-owner {username}';
 protected $description='Define o proprietário do Lobby localmente, após confirmação';
 public function handle(): int {
  if(!Schema::hasColumn('users','lobby_role')){$this->error('Execute php artisan migrate antes.');return self::FAILURE;}
  if(User::where('lobby_role','owner')->exists()){$this->error('Já existe um proprietário.');return self::FAILURE;}
  $u=User::where('username',$this->argument('username'))->first();if(!$u){$this->error('Usuário não encontrado.');return self::FAILURE;}
  if(!$this->confirm('Definir @'.$u->username.' como proprietário permanente?'))return self::FAILURE;
  $u->lobby_role='owner';$u->save();$this->info('Proprietário definido.');return self::SUCCESS;
 }
}
