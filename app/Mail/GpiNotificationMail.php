<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GpiNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mailSubject,
        public string $title,
        public string $message,
        public ?string $actionUrl = null,
        public ?string $actionLabel = 'Ouvrir dans GPI'
    ) {}

    public function build(): static
    {
        return $this->subject($this->mailSubject)
            ->html($this->buildHtml());
    }

    private function buildHtml(): string
    {
        $title = htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8');
        $message = nl2br(htmlspecialchars($this->message, ENT_QUOTES, 'UTF-8'));
        $fromName = htmlspecialchars((string) config('mail.from.name', 'GPI'), ENT_QUOTES, 'UTF-8');

        $actionBlock = '';
        if ($this->actionUrl) {
            $url = htmlspecialchars($this->actionUrl, ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars($this->actionLabel ?? 'Ouvrir dans GPI', ENT_QUOTES, 'UTF-8');
            $actionBlock = <<<HTML
<p style="margin:24px 0 0;text-align:center">
  <a href="{$url}" style="display:inline-block;background:#0a2558;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600">{$label}</a>
</p>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{$title}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f1f5f9;padding:40px 16px">
  <tr><td align="center">
    <table width="520" cellpadding="0" cellspacing="0" role="presentation"
           style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(10,37,88,.10);max-width:520px;width:100%">
      <tr>
        <td style="background:linear-gradient(135deg,#0a2558 0%,#1a3a8a 100%);padding:28px 36px;text-align:center">
          <p style="margin:0;color:#ffffff;font-size:20px;font-weight:700">{$fromName}</p>
          <p style="margin:4px 0 0;color:#a8c4f0;font-size:12px">Gestion du parc informatique</p>
        </td>
      </tr>
      <tr>
        <td style="padding:32px 36px">
          <p style="margin:0 0 12px;color:#1a2a40;font-size:17px;font-weight:700">{$title}</p>
          <p style="margin:0;color:#5a6e8a;font-size:14px;line-height:1.7">{$message}</p>
          {$actionBlock}
        </td>
      </tr>
      <tr>
        <td style="background:#f8fafc;padding:14px 36px;border-top:1px solid #e2e8f0;text-align:center">
          <p style="margin:0;color:#94a3b8;font-size:10px">Message automatique — COFINA · Usage interne</p>
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
