@php
    $logoPath = null;
    foreach (['images/Cofina1.png', 'logo_Cofina.png', 'logo_Cofina.jpg'] as $logoFile) {
        $candidate = public_path($logoFile);
        if (file_exists($candidate)) {
            $logoPath = $candidate;
            break;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#e8e8e8;font-family:Arial,Helvetica,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#e8e8e8;padding:24px 12px">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" role="presentation"
           style="background:#ffffff;max-width:600px;width:100%;border-collapse:collapse">

      {{-- En-tête COFINA --}}
      <tr>
        <td style="background:#555555;padding:28px 32px;text-align:center">
          @if($logoPath)
            <img src="{{ $message->embed($logoPath) }}" alt="COFINA" width="200" style="display:block;margin:0 auto 16px;max-width:200px;height:auto;border:0">
          @else
            <p style="margin:0 0 4px;color:#c8102e;font-size:32px;font-weight:700;letter-spacing:-0.5px">cofina</p>
            <p style="margin:0 0 16px;color:#cccccc;font-size:9px;letter-spacing:0.5px">Compagnie Financière Africaine</p>
          @endif
          <p style="margin:0;color:#ffffff;font-size:15px;font-weight:400;line-height:1.4">
            Gestion du parc informatique
          </p>
        </td>
      </tr>

      {{-- Corps --}}
      <tr>
        <td style="padding:32px 36px 28px;color:#333333;font-size:14px;line-height:1.7">
          <p style="margin:0 0 20px;font-size:14px">
            Bonjour <strong>{{ $recipientName }}</strong>,
          </p>

          <p style="margin:0 0 16px;font-size:14px">
            Les éléments suivants nécessitent votre action&nbsp;:
          </p>

          <p style="margin:0 0 20px;font-size:14px">
            <strong>1.</strong> {{ $title }}
          </p>

          <div style="margin:0 0 24px;font-size:14px;color:#333333">
            {!! nl2br(e($bodyText)) !!}
          </div>

          @if($actionUrl)
          <p style="margin:0 0 8px;font-size:14px">
            👉 <strong>Action attendue&nbsp;:</strong>
          </p>
          <p style="margin:0 0 20px;font-size:14px">
            Merci de consulter la demande et d’effectuer l’action requise dans GPI.
          </p>
          <p style="margin:0 0 28px;text-align:center">
            <a href="{{ $actionUrl }}"
               style="display:inline-block;background:#555555;color:#ffffff;text-decoration:none;padding:12px 28px;font-size:14px;font-weight:600;border-radius:2px">
              {{ $actionLabel ?? 'Ouvrir dans GPI' }}
            </a>
          </p>
          @endif

          <p style="margin:0 0 4px;font-size:14px">Cordialement,</p>
          <p style="margin:0;font-size:14px"><strong>L’équipe GPI — COFINA</strong></p>
        </td>
      </tr>

      {{-- Pied de page --}}
      <tr>
        <td style="background:#a51d1d;padding:14px 24px;text-align:center">
          <p style="margin:0;color:#ffffff;font-size:12px;font-style:italic;line-height:1.5">
            Ce message est généré automatiquement. Merci de ne pas y répondre.
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
