<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CaptureDocumentationScreenshots extends Command
{
    protected $signature = 'documentation:capture-screenshots
                            {--base-url=http://127.0.0.1:8000 : URL de l\'application}
                            {--only= : IDs séparés par virgule (ex: 01-accueil-dashboard,02-login)}';

    protected $description = 'Captures réelles via navigateur headless (Edge/Chrome) — serveur Laravel doit tourner';

    /** @var array<string, string> */
    private array $pages = [
        '01-accueil-dashboard' => '/dashboard',
        '02-login' => '/login',
        '03-profil' => '/profile',
        '06-equipment-list' => '/equipment',
        '12-parc-index' => '/parc',
        '26-reports-overview' => '/reports',
        '39-passwords-index' => '/passwords',
        '43-agencies' => '/agencies',
    ];

    public function handle(): int
    {
        $browser = $this->findBrowser();
        if ($browser === null) {
            $this->error('Chrome ou Edge introuvable. Utilisez Win+Shift+S et copiez les PNG dans public/doc-captures/');

            return self::FAILURE;
        }

        $baseUrl = rtrim($this->option('base-url'), '/');
        $outDir = public_path('doc-captures');
        $only = $this->option('only')
            ? array_map('trim', explode(',', $this->option('only')))
            : null;

        $this->info("Navigateur : {$browser}");
        $this->warn('Connectez-vous dans le navigateur normal si les pages nécessitent auth (session non partagée avec headless).');

        foreach ($this->pages as $id => $path) {
            if ($only !== null && ! in_array($id, $only, true)) {
                continue;
            }

            $url = $baseUrl . $path;
            $output = $outDir . DIRECTORY_SEPARATOR . $id . '.png';

            $cmd = sprintf(
                '"%s" --headless=new --disable-gpu --window-size=1280,720 --screenshot=%s %s',
                $browser,
                $output,
                escapeshellarg($url)
            );

            $this->line("Capture {$id} …");
            exec($cmd . ' 2>&1', $lines, $code);

            if ($code !== 0 || ! file_exists($output) || filesize($output) < 500) {
                $this->warn("  Échec {$id} — faites une capture manuelle.");
                continue;
            }

            $this->info("  OK {$output}");
        }

        $this->newLine();
        $this->info('Terminé. Rechargez /documentation/manuel-complet (Ctrl+F5).');

        return self::SUCCESS;
    }

    private function findBrowser(): ?string
    {
        $candidates = [
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
