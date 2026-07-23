# Collecte audits postes (PowerShell)
#
# Usage rapide (dev local) :
#   1. Copier config.example.json → config.json et renseigner ApiKey (= AUDIT_API_KEY du .env)
#   2. php artisan serve
#   3. .\Collecte-AuditPoste.ps1 -ApiUrl "http://127.0.0.1:8000" -ApiKey "..."
#
# Prévisualiser sans envoyer :
#   .\Collecte-AuditPoste.ps1 -WhatIf
#
# Production / GPO :
#   Voir DEPLOY-GPO.md
#   - Partage : \\SERVEUR\IT$\audit-poste\ (script + config.json)
#   - Tâche planifiée SYSTEM → powershell.exe -File "...\Collecte-AuditPoste.ps1"
#   - Modele XML : Collecte-AuditPoste-Task.xml
#   - Config prod exemple : config.prod.example.json
#
# Interface web après envoi : /audits-postes
