<?php
namespace App\Console\Commands;

use App\Models\Game;
use App\Support\GameFacts;
use Illuminate\Console\Command;

class SyncGameFacts extends Command
{
    protected $signature = 'lobby:sync-game-facts {--refresh : Atualiza cache existente}';
    protected $description = 'Consulta dados reais da Steam e RAWG para o catálogo';

    public function handle(): int
    {
        $ok = $missing = $failed = 0;
        foreach (Game::orderBy('title')->cursor() as $game) {
            $facts = GameFacts::get($game, (bool) $this->option('refresh'));
            if ($facts) { $ok++; $this->line("OK: {$game->title}"); }
            else { $failed++; $this->warn("Sem retorno: {$game->title}"); }
            usleep(400000);
        }
        $this->info("Com dados verificados: {$ok} | Sem retorno ou API não configurada: {$failed}");
        return self::SUCCESS;
    }
}
