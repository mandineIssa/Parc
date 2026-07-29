<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $name }}</title>
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #f8fafc; color: #1e293b; }
        .bar { padding: 10px 14px; background: #fff; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; }
        .wrap { padding: 12px; overflow: auto; }
        table { border-collapse: collapse; width: max-content; min-width: 100%; background: #fff; font-size: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; white-space: nowrap; max-width: 320px; overflow: hidden; text-overflow: ellipsis; }
        th { background: #f1f5f9; font-weight: 700; color: #334155; position: sticky; top: 0; }
        tr:nth-child(even) td { background: #f8fafc; }
        .empty { padding: 24px; text-align: center; color: #64748b; }
    </style>
</head>
<body>
    <div class="bar">
        Aperçu Excel — {{ $name }}
        @if($truncated)
            · affichage limité aux {{ $maxRows }} premières lignes
        @endif
    </div>
    <div class="wrap">
        @if(count($rows) === 0)
            <p class="empty">Fichier vide.</p>
        @else
            <table>
                <tbody>
                    @foreach($rows as $rIndex => $row)
                        <tr>
                            @foreach(($row ?? []) as $cell)
                                @if($rIndex === 0)
                                    <th>{{ $cell === null || $cell === '' ? '—' : $cell }}</th>
                                @else
                                    <td>{{ $cell === null || $cell === '' ? '' : $cell }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
