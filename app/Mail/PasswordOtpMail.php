<?php
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

    public function build(): static
    {
        return $this->subject('🔐 Code de vérification — Accès mot de passe IT COFINA')
                    ->html($this->buildHtml());
    }

    private function buildHtml(): string
    {
        $digits = implode(
            '</td><td style="width:44px;height:52px;background:#eef2fa;border-radius:8px;text-align:center;vertical-align:middle;font-size:28px;font-weight:800;color:#0a2558;font-family:Courier New,monospace;border:2px solid #c6d2e8">',
            str_split($this->otp)
        );

        $nom      = htmlspecialchars($this->nomUser,  ENT_QUOTES);
        $nomFiche = htmlspecialchars($this->nomFiche, ENT_QUOTES);

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Code OTP — COFINA IT</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f1f5f9;padding:40px 16px">
  <tr><td align="center">
    <table width="520" cellpadding="0" cellspacing="0" role="presentation"
           style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(10,37,88,.10);max-width:520px;width:100%">

      <!-- HEADER -->
      <tr>
        <td style="background:linear-gradient(135deg,#0a2558 0%,#1a3a8a 100%);padding:28px 36px;text-align:center">
          <div style="display:inline-block;background:rgba(255,255,255,.12);border-radius:50%;padding:14px;margin-bottom:12px">
            <span style="font-size:28px">🔐</span>
          </div>
          <p style="margin:0;color:#ffffff;font-size:20px;font-weight:700;letter-spacing:.5px">COFINA IT</p>
          <p style="margin:4px 0 0;color:#a8c4f0;font-size:12px">Gestion des mots de passe sécurisés</p>
        </td>
      </tr>

      <!-- BODY -->
      <tr>
        <td style="padding:32px 36px">
          <p style="margin:0 0 6px;color:#1a2a40;font-size:15px;font-weight:600">Bonjour {$nom},</p>
          <p style="margin:0 0 24px;color:#5a6e8a;font-size:13px;line-height:1.7">
            Vous avez demandé l'accès au mot de passe de la fiche :<br>
            <strong style="color:#0a2558;font-size:14px">📋 {$nomFiche}</strong>
          </p>

          <!-- CODE -->
          <table cellpadding="0" cellspacing="0" role="presentation" width="100%"
                 style="margin-bottom:24px;background:#f8fafc;border-radius:12px;padding:24px">
            <tr>
              <td align="center" style="padding-bottom:10px">
                <p style="margin:0;color:#5a6e8a;font-size:11px;text-transform:uppercase;letter-spacing:1.5px;font-weight:600">
                  Votre code de vérification
                </p>
              </td>
            </tr>
            <tr>
              <td align="center">
                <table cellpadding="0" cellspacing="6" role="presentation">
                  <tr>
                    <td style="width:44px;height:52px;background:#eef2fa;border-radius:8px;text-align:center;vertical-align:middle;font-size:28px;font-weight:800;color:#0a2558;font-family:Courier New,monospace;border:2px solid #c6d2e8">{$digits}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- AVERTISSEMENT -->
          <table cellpadding="0" cellspacing="0" role="presentation" width="100%" style="margin-bottom:20px">
            <tr>
              <td style="background:#fef6e4;border-left:4px solid #f59e0b;border-radius:0 6px 6px 0;padding:14px 16px">
                <p style="margin:0;color:#92400e;font-size:12px;line-height:1.6">
                  <strong>⚠ Attention :</strong> Ce code est valable <strong>5 minutes</strong>
                  et ne peut être utilisé <strong>qu'une seule fois</strong>.<br>
                  Si vous n'avez pas effectué cette demande, ignorez cet email.
                </p>
              </td>
            </tr>
          </table>

          <p style="margin:0;color:#94a3b8;font-size:11px;text-align:center;line-height:1.6">
            COFINA Sénégal — Service IT &amp; Exploitation
          </p>
        </td>
      </tr>

      <!-- FOOTER -->
      <tr>
        <td style="background:#f8fafc;padding:14px 36px;border-top:1px solid #e2e8f0;text-align:center">
          <p style="margin:0;color:#94a3b8;font-size:10px">
            Document confidentiel — Usage interne uniquement · COFINA Sénégal
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }
}