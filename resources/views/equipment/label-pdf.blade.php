<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 8px; }
        .header { color: #C8102E; font-weight: bold; font-size: 12px; margin-bottom: 6px; }
        .serie { font-size: 14px; font-weight: bold; }
        table { width: 100%; }
        td { vertical-align: top; }
        .qr img { width: 80px; height: 80px; }
    </style>
</head>
<body>
    <div class="header">COFINA — GPI</div>
    <table>
        <tr>
            <td>
                <div class="serie">N° {{ $equipment->numero_serie }}</div>
                <div>{{ $equipment->nom }}</div>
                <div>{{ $equipment->marque }} {{ $equipment->modele }}</div>
                <div>Statut : {{ $equipment->statut }}</div>
            </td>
            <td class="qr" align="right">
                <img src="{{ $qrDataUri }}" alt="QR">
            </td>
        </tr>
    </table>
</body>
</html>
