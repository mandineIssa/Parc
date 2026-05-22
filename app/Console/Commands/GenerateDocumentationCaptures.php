<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDocumentationCaptures extends Command
{
    protected $signature = 'documentation:generate-captures {--force : Remplacer les captures existantes}';

    protected $description = 'Génère les images PNG du manuel (maquettes COFINA) pour le PDF';

    /** @var array<int, array{id: string, title: string, subtitle: string}> */
    private array $captures = [
        ['id' => '01-accueil-dashboard', 'title' => 'Tableau de bord', 'subtitle' => 'Vue synthèse KPI parc IT'],
        ['id' => '02-login', 'title' => 'Connexion', 'subtitle' => 'Authentification COFINA'],
        ['id' => '03-profil', 'title' => 'Profil utilisateur', 'subtitle' => 'Informations et signature'],
        ['id' => '04-sidebar-complete', 'title' => 'Menu latéral', 'subtitle' => 'Navigation PARC et modules'],
        ['id' => '05-dashboard-agent', 'title' => 'Dashboard Agent IT', 'subtitle' => 'Indicateurs opérationnels'],
        ['id' => '06-equipment-list', 'title' => 'Liste équipements', 'subtitle' => 'Inventaire et filtres'],
        ['id' => '07-equipment-create', 'title' => 'Création équipement', 'subtitle' => 'Formulaire multi-étapes'],
        ['id' => '08-equipment-import', 'title' => 'Import CSV', 'subtitle' => 'Équipements en masse'],
        ['id' => '09-equipment-renewal', 'title' => 'Renouvellement', 'subtitle' => 'Plan de remplacement'],
        ['id' => '10-stock-celer', 'title' => 'Stock CELER', 'subtitle' => 'Entrées en stock'],
        ['id' => '11-stock-deceler', 'title' => 'Stock DECELER', 'subtitle' => 'Sorties de stock'],
        ['id' => '12-parc-index', 'title' => 'Parc informatique', 'subtitle' => 'Équipements affectés'],
        ['id' => '13-parc-create', 'title' => 'Nouvelle affectation', 'subtitle' => 'Attribution collaborateur'],
        ['id' => '14-parc-export-masse', 'title' => 'Export en masse', 'subtitle' => 'Excel modèle COFINA'],
        ['id' => '15-transitions-menu', 'title' => 'Transitions', 'subtitle' => 'Choix du mouvement'],
        ['id' => '16-transition-stock-parc', 'title' => 'Stock → Parc', 'subtitle' => 'Mise en service'],
        ['id' => '17-transition-parc-maintenance', 'title' => 'Parc → Maintenance', 'subtitle' => 'Envoi SAV'],
        ['id' => '18-transition-maintenance-stock', 'title' => 'Maintenance → Stock', 'subtitle' => 'Retour réparé'],
        ['id' => '19-transition-parc-hors-service', 'title' => 'Parc → Hors service', 'subtitle' => 'Retrait'],
        ['id' => '20-transition-parc-perdu', 'title' => 'Parc → Perdu', 'subtitle' => 'Déclaration perte'],
        ['id' => '21-approbation-detail', 'title' => 'Approbation', 'subtitle' => 'Validation demande'],
        ['id' => '22-approbations-liste', 'title' => 'File approbations', 'subtitle' => 'Demandes en attente'],
        ['id' => '23-maintenance-index', 'title' => 'Maintenance', 'subtitle' => 'Suivi SAV'],
        ['id' => '24-hors-service-index', 'title' => 'Hors service', 'subtitle' => 'Équipements retirés'],
        ['id' => '25-perdu-index', 'title' => 'Perdu', 'subtitle' => 'Déclarations perte/vol'],
        ['id' => '26-reports-overview', 'title' => 'Rapports', 'subtitle' => 'Vue d\'ensemble'],
        ['id' => '27-change-role-select', 'title' => 'Rôle Change', 'subtitle' => 'N1 / N2 / N3'],
        ['id' => '28-change-n1-create', 'title' => 'Change N1', 'subtitle' => 'Nouvelle demande'],
        ['id' => '29-change-n2-index', 'title' => 'Change N2', 'subtitle' => 'Traitement technique'],
        ['id' => '30-change-n3-index', 'title' => 'Change N3', 'subtitle' => 'Validation finale'],
        ['id' => '31-eod-n1-create', 'title' => 'EOD N1', 'subtitle' => 'Fiche fin de journée'],
        ['id' => '32-eod-n2-index', 'title' => 'EOD N2', 'subtitle' => 'Complément technique'],
        ['id' => '33-eod-n3-pending', 'title' => 'EOD N3', 'subtitle' => 'Signatures en attente'],
        ['id' => '34-eod-controller', 'title' => 'EOD Controller', 'subtitle' => 'Validation batch'],
        ['id' => '35-incidents-create', 'title' => 'Nouvel incident', 'subtitle' => 'Création fiche'],
        ['id' => '36-incidents-list', 'title' => 'Incidents', 'subtitle' => 'Liste des fiches'],
        ['id' => '37-controls-dashboard', 'title' => 'Contrôles IT', 'subtitle' => 'Dashboard campagnes'],
        ['id' => '38-controls-tasks', 'title' => 'Mes tâches', 'subtitle' => 'Contrôles assignés'],
        ['id' => '39-passwords-index', 'title' => 'Mots de passe', 'subtitle' => 'Coffre credentials'],
        ['id' => '40-network-index', 'title' => 'Réseau', 'subtitle' => 'Plan adressage'],
        ['id' => '41-licences-index', 'title' => 'Licences', 'subtitle' => 'Suivi licences'],
        ['id' => '42-admin-users', 'title' => 'Utilisateurs', 'subtitle' => 'Administration comptes'],
        ['id' => '43-agencies', 'title' => 'Agences', 'subtitle' => 'Référentiel sites'],
    ];

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('Extension PHP GD requise pour générer les captures.');

            return self::FAILURE;
        }

        $dir = public_path('doc-captures');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $bar = $this->output->createProgressBar(count($this->captures));
        $bar->start();

        foreach ($this->captures as $i => $capture) {
            $path = $dir . DIRECTORY_SEPARATOR . $capture['id'] . '.png';
            if (file_exists($path) && ! $this->option('force')) {
                $bar->advance();
                continue;
            }

            $this->renderCapture($path, $capture, $i + 1);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Captures générées dans : ' . $dir);

        return self::SUCCESS;
    }

    private function renderCapture(string $path, array $capture, int $number): void
    {
        $w = 960;
        $h = 540;
        $img = imagecreatetruecolor($w, $h);

        $bg = imagecolorallocate($img, 248, 249, 250);
        $header = imagecolorallocate($img, 166, 27, 41);
        $sidebar = imagecolorallocate($img, 61, 65, 84);
        $white = imagecolorallocate($img, 255, 255, 255);
        $gray = imagecolorallocate($img, 107, 114, 128);
        $light = imagecolorallocate($img, 253, 242, 243);
        $border = imagecolorallocate($img, 229, 231, 235);

        imagefill($img, 0, 0, $bg);
        imagefilledrectangle($img, 0, 0, $w, 48, $header);
        imagefilledrectangle($img, 0, 48, 200, $h, $sidebar);
        imagefilledrectangle($img, 210, 58, $w - 10, $h - 10, $white);
        imagerectangle($img, 210, 58, $w - 10, $h - 10, $border);

        imagestring($img, 5, 16, 14, 'COFINA - Gestion Parc IT', $white);
        imagestring($img, 3, 12, 70, 'PARC', $white);
        imagestring($img, 2, 12, 95, 'Rapports', $white);
        imagestring($img, 2, 12, 115, 'Gestion', $white);
        imagestring($img, 2, 12, 135, 'Equipements', $white);
        imagestring($img, 2, 12, 155, 'Stocks', $white);

        $title = $capture['title'];
        imagestring($img, 5, 230, 80, $title, $header);
        imagestring($img, 4, 230, 110, $capture['subtitle'], $gray);

        imagefilledrectangle($img, 230, 140, $w - 30, 280, $light);
        imagerectangle($img, 230, 140, $w - 30, 280, $header);
        imagestring($img, 4, 245, 165, 'Module : ' . $capture['id'], $sidebar);
        imagestring($img, 3, 245, 195, 'Gestion Parc Informatique COFINA', $gray);
        imagestring($img, 3, 245, 225, 'Figure ' . $number . ' — Documentation', $gray);
        imagestring($img, 2, 245, 255, 'Remplacer : Win+Shift+S puis public/doc-captures/', $gray);

        imagepng($img, $path);
        imagedestroy($img);
    }
}
