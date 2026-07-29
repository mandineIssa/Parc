# Collecte audits postes (PowerShell)
#
# ---------------------------------------------------------------
# USAGE ENTREPRISE (recommande)
# ---------------------------------------------------------------
# 1. Sur un PARTAGE reseau (ex. \\SERVEUR\IT$\audit-poste\) deposer :
#      Collecte-AuditPoste.bat
#      Collecte-AuditPoste.ps1
#      config.json   (copier depuis config.prod.example.json)
#
# 2. config.json :
#      {
#        "ApiUrl": "https://gpi.cofinaonline.com",
#        "ApiKey": "meme_cle_que_AUDIT_API_KEY_du_serveur"
#      }
#
# 3. Sur un poste : double-cliquer Collecte-AuditPoste.bat
#    (pas besoin de copier les fichiers sur chaque PC)
#
# 4. Ou GPO / tache planifiee SYSTEM :
#      Programme : powershell.exe
#      Arguments :
#        -NoProfile -NonInteractive -ExecutionPolicy Bypass -File "\\SERVEUR\IT$\audit-poste\Collecte-AuditPoste.ps1"
#    Voir DEPLOY-GPO.md
#
# ---------------------------------------------------------------
# DEV LOCAL
# ---------------------------------------------------------------
#   Copy-Item config.example.json config.json
#   .\Collecte-AuditPoste.ps1 -ApiUrl "http://127.0.0.1:8000" -ApiKey "..."
#   ou double-clic sur Collecte-AuditPoste.bat
#
# Interface web : /audits-postes
