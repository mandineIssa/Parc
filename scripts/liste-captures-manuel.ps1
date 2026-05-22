# Liste des 43 captures + URLs à ouvrir pour réaliser le manuel COFINA
# Usage : .\scripts\liste-captures-manuel.ps1
# Puis : Win+Shift+S pour chaque écran, enregistrer dans public\documentation\captures\

$BaseUrl = "http://127.0.0.1:8000"
$OutDir = Join-Path $PSScriptRoot "..\public\doc-captures"

$captures = @(
    @{ File = "01-accueil-dashboard.png"; Url = "/dashboard"; Desc = "Tableau de bord" },
    @{ File = "02-login.png"; Url = "/login"; Desc = "Connexion" },
    @{ File = "03-profil.png"; Url = "/profile"; Desc = "Profil utilisateur" },
    @{ File = "04-sidebar-complete.png"; Url = "/parc"; Desc = "Menu latéral déplié" },
    @{ File = "05-dashboard-agent.png"; Url = "/dashboard"; Desc = "Dashboard" },
    @{ File = "06-equipment-list.png"; Url = "/equipment"; Desc = "Liste équipements" },
    @{ File = "07-equipment-create.png"; Url = "/equipment/create"; Desc = "Création équipement" },
    @{ File = "08-equipment-import.png"; Url = "/equipment/import"; Desc = "Import CSV" },
    @{ File = "09-equipment-renewal.png"; Url = "/equipment/renewal"; Desc = "Renouvellement" },
    @{ File = "10-stock-celer.png"; Url = "/dashboard/celer-informatique"; Desc = "Stock CELER" },
    @{ File = "11-stock-deceler.png"; Url = "/dashboard/deceler-informatique"; Desc = "Stock DECELER" },
    @{ File = "12-parc-index.png"; Url = "/parc"; Desc = "Parc index" },
    @{ File = "13-parc-create.png"; Url = "/parc/create"; Desc = "Nouvelle affectation" },
    @{ File = "14-parc-export-masse.png"; Url = "/equipment/parc/export"; Desc = "Export Excel (ou bouton parc)" },
    @{ File = "15-transitions-menu.png"; Url = "/equipment/1/transitions"; Desc = "Menu transitions (adapter ID)" },
    @{ File = "16-transition-stock-parc.png"; Url = "/equipment/1/transitions"; Desc = "Stock vers Parc" },
    @{ File = "17-transition-parc-maintenance.png"; Url = "/equipment/1/transitions"; Desc = "Parc maintenance" },
    @{ File = "18-transition-maintenance-stock.png"; Url = "/equipment/1/transitions"; Desc = "Maintenance stock" },
    @{ File = "19-transition-parc-hors-service.png"; Url = "/equipment/1/transitions"; Desc = "Parc hors service" },
    @{ File = "20-transition-parc-perdu.png"; Url = "/equipment/1/transitions"; Desc = "Parc perdu" },
    @{ File = "21-approbation-detail.png"; Url = "/admin/approvals"; Desc = "Détail approbation" },
    @{ File = "22-approbations-liste.png"; Url = "/admin/approvals"; Desc = "Liste approbations" },
    @{ File = "23-maintenance-index.png"; Url = "/maintenance"; Desc = "Maintenance" },
    @{ File = "24-hors-service-index.png"; Url = "/hors-service"; Desc = "Hors service" },
    @{ File = "25-perdu-index.png"; Url = "/perdu"; Desc = "Perdu" },
    @{ File = "26-reports-overview.png"; Url = "/reports"; Desc = "Rapports" },
    @{ File = "27-change-role-select.png"; Url = "/change/role"; Desc = "Rôle Change" },
    @{ File = "28-change-n1-create.png"; Url = "/change/n1/create"; Desc = "Change N1" },
    @{ File = "29-change-n2-index.png"; Url = "/change/n2"; Desc = "Change N2" },
    @{ File = "30-change-n3-index.png"; Url = "/change/n3"; Desc = "Change N3" },
    @{ File = "31-eod-n1-create.png"; Url = "/eod/n1/create"; Desc = "EOD N1" },
    @{ File = "32-eod-n2-index.png"; Url = "/eod/n2"; Desc = "EOD N2" },
    @{ File = "33-eod-n3-pending.png"; Url = "/eod/n3/pending"; Desc = "EOD N3 signature" },
    @{ File = "34-eod-controller.png"; Url = "/eod/controller"; Desc = "EOD Controller" },
    @{ File = "35-incidents-create.png"; Url = "/incidents/create"; Desc = "Incident création" },
    @{ File = "36-incidents-list.png"; Url = "/incidents"; Desc = "Incidents liste" },
    @{ File = "37-controls-dashboard.png"; Url = "/controls/dashboard"; Desc = "Contrôles dashboard" },
    @{ File = "38-controls-tasks.png"; Url = "/controls/tasks"; Desc = "Tâches contrôle" },
    @{ File = "39-passwords-index.png"; Url = "/passwords"; Desc = "Mots de passe" },
    @{ File = "40-network-index.png"; Url = "/network"; Desc = "Réseau" },
    @{ File = "41-licences-index.png"; Url = "/licences"; Desc = "Licences" },
    @{ File = "42-admin-users.png"; Url = "/admin/users"; Desc = "Utilisateurs" },
    @{ File = "43-agencies.png"; Url = "/agencies"; Desc = "Agences" }
)

Write-Host "Dossier captures : $OutDir" -ForegroundColor Cyan
Write-Host "Manuel web : $BaseUrl/documentation/manuel-complet`n" -ForegroundColor Green

$i = 1
foreach ($c in $captures) {
    $path = Join-Path $OutDir $c.File
    $exists = Test-Path $path
    $status = if ($exists) { "[OK]" } else { "[  ]" }
    Write-Host "$status $i. $($c.File)" -ForegroundColor $(if ($exists) { "Green" } else { "Yellow" })
    Write-Host "     $($c.Desc) -> $BaseUrl$($c.Url)"
    $i++
}

Write-Host "`nConnectez-vous d'abord, puis capturez chaque écran (Win+Shift+S)." -ForegroundColor Gray
