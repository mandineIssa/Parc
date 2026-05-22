<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Manuel utilisateur — Parc IT COFINA</title>
    <style>
        @page { margin: 18mm 15mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.45; }
        h1 { color: #A61B29; font-size: 20pt; border-bottom: 2px solid #A61B29; padding-bottom: 6px; margin-top: 0; }
        h2 { color: #7A0C1A; font-size: 13pt; margin-top: 22px; page-break-after: avoid; }
        h3 { font-size: 11pt; color: #374151; margin-top: 14px; }
        .cover { text-align: center; padding: 40px 0 30px; page-break-after: always; }
        .cover h1 { font-size: 26pt; border: none; }
        .cover .sub { font-size: 12pt; color: #6b7280; margin-top: 12px; }
        .meta { font-size: 9pt; color: #6b7280; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 9pt; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #fdf2f3; color: #7A0C1A; }
        ul, ol { margin: 6px 0 10px 18px; }
        li { margin-bottom: 4px; }
        .figure { text-align: center; margin: 12px 0; page-break-inside: avoid; }
        .figure img { max-width: 100%; max-height: 280px; border: 1px solid #d1d5db; }
        .figcaption { font-size: 8.5pt; color: #6b7280; font-style: italic; margin-top: 4px; }
        .scenario { border-left: 3px solid #A61B29; padding-left: 10px; margin: 12px 0; }
        .page-break { page-break-before: always; }
        .toc li { margin-bottom: 3px; }
    </style>
</head>
<body>

<div class="cover">
    <h1>Gestion Parc Informatique</h1>
    <p class="sub"><strong>COFINA</strong> — Manuel d'utilisation complet</p>
    <p class="sub">Version {{ $version }} — {{ $generatedAt }}</p>
</div>

<h1>Table des matières</h1>
<ol class="toc">
    @foreach ($chapters as $ch)
        <li>{{ $ch['title'] }}</li>
    @endforeach
</ol>

@foreach ($chapters as $chapter)
    <div class="{{ $loop->index > 0 ? 'page-break' : '' }}">
        <h2>{{ $chapter['num'] }}. {{ $chapter['title'] }}</h2>
        {!! $chapter['html'] !!}

        @foreach ($chapter['figures'] ?? [] as $fig)
            @if (!empty($fig['path']) && file_exists($fig['path']))
                <div class="figure">
                    <img src="{{ $fig['path'] }}" alt="{{ $fig['caption'] }}">
                    <div class="figcaption">{{ $fig['caption'] }}</div>
                </div>
            @endif
        @endforeach
    </div>
@endforeach

<p class="meta" style="margin-top: 30px; text-align: center;">
    © COFINA — Gestion Parc Informatique — Document généré automatiquement
</p>

</body>
</html>
