<?php
// ─────────────────────────────────────────────────────────────────────────────
// app/Mail/PasswordOtpMail.php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $nomFiche,
        public string $nomUser
    ) {}

    public function build()
    {
        return $this->subject('Code de vérification — Accès mot de passe IT')
                    ->html($this->buildHtml());
    }

    private function buildHtml(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f1f5f9;padding:40px 20px;margin:0">
<div style="max-width:480px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
  <div style="background:#0a2558;padding:28px 32px;text-align:center">
    <h1 style="color:#fff;margin:0;font-size:20px;font-weight:700">COFINA IT — Vérification</h1>
    <p style="color:#a8c4f0;margin:6px 0 0;font-size:13px">Gestion des mots de passe sécurisés</p>
  </div>
  <div style="padding:32px">
    <p style="color:#1a2a40;font-size:14px;margin:0 0 8px">Bonjour <strong>{$this->nomUser}</strong>,</p>
    <p style="color:#5a6e8a;font-size:13px;margin:0 0 24px">
      Vous avez demandé à accéder au mot de passe de la fiche :<br>
      <strong style="color:#0a2558">{$this->nomFiche}</strong>
    </p>
    <div style="background:#eef2fa;border-radius:10px;padding:24px;text-align:center;margin:0 0 24px">
      <p style="color:#5a6e8a;font-size:12px;margin:0 0 10px;text-transform:uppercase;letter-spacing:.5px">Votre code de vérification</p>
      <div style="font-size:38px;font-weight:800;color:#0a2558;letter-spacing:10px;font-family:monospace">{$this->otp}</div>
    </div>
    <div style="background:#fef6e4;border-left:4px solid #f59e0b;border-radius:4px;padding:12px 16px">
      <p style="color:#92400e;font-size:12px;margin:0">⚠ Ce code est valable <strong>5 minutes</strong> et ne peut être utilisé qu'une seule fois.</p>
    </div>
    <p style="color:#94a3b8;font-size:11px;margin:24px 0 0;text-align:center">
      Si vous n'avez pas demandé ce code, ignorez cet email.<br>
      COFINA Sénégal — Service IT &amp; Exploitation
    </p>
  </div>
</div>
</body>
</html>
HTML;
    }
}
