# Collecte l'audit d'un poste Windows et l'envoie au backend Parc (POST /api/audit).
# Compatible session utilisateur et tache planifiee / GPO sous compte SYSTEM
# (utilisateur lu via Win32_ComputerSystem, pas $env:USERNAME).
#
# Exemples :
#   .\Collecte-AuditPoste.ps1 -ApiUrl "http://127.0.0.1:8000" -ApiKey "votre_cle"
#   .\Collecte-AuditPoste.ps1 -WhatIf

[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [string]$ApiUrl,
    [string]$ApiKey,
    [string]$ConfigPath = (Join-Path $PSScriptRoot 'config.json')
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Get-AuditConfig {
    param(
        [string]$Path,
        [string]$Url,
        [string]$Key
    )

    $cfgUrl = $Url
    $cfgKey = $Key

    if ((-not $cfgUrl -or -not $cfgKey) -and (Test-Path -LiteralPath $Path)) {
        $json = Get-Content -LiteralPath $Path -Raw -Encoding UTF8 | ConvertFrom-Json
        if (-not $cfgUrl) { $cfgUrl = [string]$json.ApiUrl }
        if (-not $cfgKey) { $cfgKey = [string]$json.ApiKey }
    }

    if (-not $cfgKey -and $env:AUDIT_API_KEY) {
        $cfgKey = $env:AUDIT_API_KEY
    }
    if (-not $cfgUrl -and $env:AUDIT_API_URL) {
        $cfgUrl = $env:AUDIT_API_URL
    }

    if (-not $cfgUrl -or -not $cfgKey) {
        throw 'ApiUrl et ApiKey sont obligatoires. Exemple : .\Collecte-AuditPoste.ps1 -ApiUrl "http://127.0.0.1:8000" -ApiKey "..."'
    }

    return @{
        ApiUrl = $cfgUrl.TrimEnd('/')
        ApiKey = $cfgKey
    }
}

function Get-SessionUser {
    try {
        $cs = Get-CimInstance -ClassName Win32_ComputerSystem -ErrorAction Stop
        if ($cs.UserName -and $cs.UserName.Trim() -ne '') {
            return $cs.UserName.Trim()
        }
    } catch {
        Write-Verbose 'Win32_ComputerSystem.UserName indisponible'
    }

    if ($env:USERDOMAIN -and $env:USERNAME -and $env:USERNAME -notin @('SYSTEM', 'LOCAL SERVICE', 'NETWORK SERVICE')) {
        return "$env:USERDOMAIN\$env:USERNAME"
    }

    return 'UNKNOWN\NO_SESSION'
}

function Get-FirewallStatus {
    try {
        $profiles = Get-NetFirewallProfile -ErrorAction Stop
        $parts = foreach ($p in $profiles) {
            '{0}:{1}' -f $p.Name, $p.Enabled
        }
        return ($parts -join ';')
    } catch {
        return 'Unknown'
    }
}

function Get-BitLockerStatus {
    try {
        $vols = Get-BitLockerVolume -ErrorAction Stop
        $parts = foreach ($v in $vols) {
            $letter = if ($v.MountPoint) { $v.MountPoint.TrimEnd('\') } else { '?' }
            $status = if ($v.ProtectionStatus -eq 'On') { 'On' } else { 'Off' }
            '{0}:{1}' -f $letter, $status
        }
        if ($parts) {
            return ($parts -join ';')
        }
    } catch {
        Write-Verbose 'BitLocker indisponible (droits admin souvent requis)'
    }
    return 'C::Unknown'
}

function Test-UsbStorageBlocked {
    $keys = @(
        'HKLM:\SOFTWARE\Policies\Microsoft\Windows\RemovableStorageDevices\{53f5630d-b6bf-11d0-94f2-00a0c91efb8b}',
        'HKLM:\SOFTWARE\Policies\Microsoft\Windows\RemovableStorageDevices\AllRemovableStorageDevices'
    )
    foreach ($key in $keys) {
        if (Test-Path -LiteralPath $key) {
            $props = Get-ItemProperty -LiteralPath $key -ErrorAction SilentlyContinue
            if ($null -ne $props) {
                if (($props.Deny_Write -eq 1) -or ($props.Deny_All -eq 1) -or ($props.Deny_Execute -eq 1)) {
                    return $true
                }
            }
        }
    }
    return $false
}

function Test-DefenderEnabled {
    try {
        $mp = Get-MpComputerStatus -ErrorAction Stop
        return [bool]$mp.RealTimeProtectionEnabled
    } catch {
        return $false
    }
}

function Get-PrimaryMac {
    try {
        $nic = Get-NetAdapter -Physical -ErrorAction Stop |
            Where-Object { $_.Status -eq 'Up' } |
            Sort-Object -Property ifIndex |
            Select-Object -First 1
        if ($nic -and $nic.MacAddress) {
            return ($nic.MacAddress -replace '-', ':')
        }
    } catch {
        Write-Verbose 'Get-NetAdapter indisponible'
    }

    try {
        $mac = (Get-CimInstance Win32_NetworkAdapterConfiguration |
            Where-Object { $_.IPEnabled -eq $true -and $_.MACAddress } |
            Select-Object -First 1).MACAddress
        if ($mac) { return $mac }
    } catch {
        Write-Verbose 'MAC WMI indisponible'
    }

    return '00:00:00:00:00:00'
}

function Get-PrimaryIPv4 {
    try {
        $ip = Get-NetIPAddress -AddressFamily IPv4 -ErrorAction Stop |
            Where-Object {
                $_.IPAddress -notlike '127.*' -and
                $_.PrefixOrigin -ne 'WellKnown' -and
                $_.IPAddress -notlike '169.254.*'
            } |
            Sort-Object -Property InterfaceIndex |
            Select-Object -First 1 -ExpandProperty IPAddress
        if ($ip) { return $ip }
    } catch {
        Write-Verbose 'Get-NetIPAddress indisponible'
    }

    try {
        $cfg = Get-CimInstance Win32_NetworkAdapterConfiguration |
            Where-Object { $_.IPEnabled -eq $true -and $_.IPAddress } |
            Select-Object -First 1
        $v4 = @($cfg.IPAddress) | Where-Object { $_ -match '^\d+\.\d+\.\d+\.\d+$' -and $_ -notlike '127.*' } | Select-Object -First 1
        if ($v4) { return $v4 }
    } catch {
        Write-Verbose 'IP WMI indisponible'
    }

    return '0.0.0.0'
}

function Get-BiosSerial {
    try {
        $serial = (Get-CimInstance -ClassName Win32_BIOS -ErrorAction Stop).SerialNumber
        if ($serial -and $serial.Trim() -ne '' -and $serial -notmatch '^(To be filled|Default string|None|System Serial Number)$') {
            return $serial.Trim()
        }
    } catch {
        Write-Verbose 'Serial BIOS indisponible'
    }

    try {
        $serial = (Get-CimInstance -ClassName Win32_ComputerSystemProduct -ErrorAction Stop).IdentifyingNumber
        if ($serial) { return $serial.Trim() }
    } catch {
        Write-Verbose 'Serial produit indisponible'
    }

    return "UNKNOWN-$env:COMPUTERNAME"
}

function New-AuditPayload {
    $cs = Get-CimInstance -ClassName Win32_ComputerSystem
    $os = Get-CimInstance -ClassName Win32_OperatingSystem

    return @{
        hostname           = $env:COMPUTERNAME
        utilisateurSession = Get-SessionUser
        fabricant          = [string]$cs.Manufacturer
        modele             = [string]$cs.Model
        numeroSerie        = Get-BiosSerial
        os                 = [string]$os.Caption
        versionOS          = [string]$os.Version
        antivirusDefender  = Test-DefenderEnabled
        firewall           = Get-FirewallStatus
        bitlocker          = Get-BitLockerStatus
        usbStockageBloque  = Test-UsbStorageBlocked
        adresseMAC         = Get-PrimaryMac
        adresseIP          = Get-PrimaryIPv4
        dateAudit          = (Get-Date).ToUniversalTime().ToString('o')
    }
}

function Send-AuditPayload {
    param(
        [hashtable]$Config,
        [hashtable]$Payload
    )

    $endpoint = "$($Config.ApiUrl)/api/audit"
    $body = $Payload | ConvertTo-Json -Compress -Depth 5

    Write-Host "Envoi vers $endpoint ..." -ForegroundColor Cyan
    Write-Host ("Poste : {0} | Utilisateur : {1}" -f $Payload.hostname, $Payload.utilisateurSession) -ForegroundColor Gray

    try {
        $response = Invoke-RestMethod -Method Post -Uri $endpoint -Body $body -ContentType 'application/json; charset=utf-8' -Headers @{
            'X-API-Key' = $Config.ApiKey
            'Accept'    = 'application/json'
        } -TimeoutSec 60

        Write-Host ("OK - {0}" -f $response.message) -ForegroundColor Green
        if ($response.data) {
            Write-Host ("  poste_id={0} audit_id={1} created={2}" -f $response.data.poste_id, $response.data.audit_id, $response.data.created) -ForegroundColor Green
        }
        return 0
    } catch {
        $status = $null
        $detail = $_.Exception.Message
        if ($_.Exception.Response) {
            try {
                $status = [int]$_.Exception.Response.StatusCode
            } catch {
                $status = 'unknown'
            }
            try {
                $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                $detail = $reader.ReadToEnd()
                $reader.Close()
            } catch {
                $detail = $_.Exception.Message
            }
        }
        Write-Host ("ERREUR HTTP {0} : {1}" -f $status, $detail) -ForegroundColor Red
        return 1
    }
}

# --- Main ---
$exitCode = 0
try {
    $config = Get-AuditConfig -Path $ConfigPath -Url $ApiUrl -Key $ApiKey
    $payload = New-AuditPayload

    if ($WhatIfPreference) {
        Write-Host 'Mode WhatIf - JSON qui serait envoye :' -ForegroundColor Yellow
        $payload | ConvertTo-Json -Depth 5
        $exitCode = 0
    } else {
        $exitCode = Send-AuditPayload -Config $config -Payload $payload
    }
} catch {
    Write-Host ("ERREUR : {0}" -f $_.Exception.Message) -ForegroundColor Red
    $exitCode = 2
}

exit $exitCode
