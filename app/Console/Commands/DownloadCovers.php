<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DownloadCovers extends Command
{
    protected $signature = 'lobby:covers {--force : baixa de novo mesmo se o arquivo já existir}';

    protected $description = 'Baixa as capas dos jogos para public/img/covers (assim elas não dependem de sites externos)';

    public function handle(): int
    {
        $dir = public_path('img/covers');
        is_dir($dir) || mkdir($dir, 0775, true);

        $failed = 0;

        foreach (Game::orderBy('title')->get() as $game) {
            $target = "{$dir}/{$game->slug}.jpg";

            if (! $this->option('force') && is_file($target)) {
                $this->line("• {$game->title}: já existe");

                continue;
            }

            $urls = array_filter([$game->cover_url, ...(array) config("covers.{$game->slug}", [])]);
            $saved = false;

            foreach ($urls as $url) {
                try {
                    $res = Http::timeout(20)->withHeaders(['User-Agent' => 'LobbyCoverFetcher/1.0'])->get($url);
                } catch (\Throwable) {
                    continue;
                }

                if ($res->successful() && str_starts_with((string) $res->header('Content-Type'), 'image/') && strlen($res->body()) > 2000) {
                    file_put_contents($target, $res->body());
                    $saved = true;
                    $this->info("✓ {$game->title}");
                    break;
                }
            }

            if (! $saved) {
                $failed++;
                $this->warn("✗ {$game->title}: nenhuma fonte respondeu (coloque public/img/covers/{$game->slug}.jpg à mão)");
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
