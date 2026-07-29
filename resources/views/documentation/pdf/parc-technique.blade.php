<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Documentation technique — Module Parc COFINA</title>
    <style>
        @page { margin: 16mm 14mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5pt; color: #1f2937; line-height: 1.45; }
        h1 { color: #A61B29; font-size: 18pt; border-bottom: 2px solid #A61B29; padding-bottom: 6px; margin-top: 0; }
        h2 { color: #7A0C1A; font-size: 12.5pt; margin-top: 18px; page-break-after: avoid; }
        h3 { font-size: 10.5pt; color: #374151; margin-top: 12px; }
        .cover { text-align: center; padding: 50px 10px 40px; page-break-after: always; }
        .cover h1 { font-size: 22pt; border: none; }
        .cover .sub { font-size: 11pt; color: #6b7280; margin-top: 10px; }
        .cover .badge { display: inline-block; margin-top: 24px; padding: 6px 14px; background: #fdf2f3; color: #7A0C1A; border: 1px solid #f3c5cb; font-size: 9pt; }
        .meta { font-size: 8.5pt; color: #6b7280; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 12px; font-size: 8.5pt; }
        th, td { border: 1px solid #e5e7eb; padding: 5px 7px; text-align: left; vertical-align: top; }
        th { background: #fdf2f3; color: #7A0C1A; }
        ul, ol { margin: 4px 0 8px 16px; }
        li { margin-bottom: 3px; }
        pre { background: #f3f4f6; border: 1px solid #e5e7eb; padding: 8px 10px; font-size: 7.5pt; white-space: pre-wrap; word-wrap: break-word; }
        code { font-size: 8pt; background: #f3f4f6; padding: 1px 3px; }
        .figure { text-align: center; margin: 10px 0; page-break-inside: avoid; }
        .figure img { max-width: 100%; max-height: 240px; border: 1px solid #d1d5db; }
        .figcaption { font-size: 8pt; color: #6b7280; font-style: italic; margin-top: 3px; }
        .page-break { page-break-before: always; }
        .toc li { margin-bottom: 2px; }
        .footer-note { margin-top: 28px; text-align: center; font-size: 8pt; color: #9ca3af; }
    </style>
</head>
<body>

<div class="cover">
    <h1>Documentation technique</h1>
    <p class="sub"><strong>Module Parc</strong> — Gestion Parc Informatique</p>
    <p class="sub">COFINA · Laravel / MySQL</p>
    <p class="badge">Document technique — Version {{ $version }}</p>
    <p class="sub" style="margin-top: 28px;">Généré le {{ $generatedAt }}</p>
</div>

<h1>Table des matières</h1>
<ol class="toc">
    @foreach ($chapters as $ch)
        <li>{{ $ch['num'] }}. {{ $ch['title'] }}</li>
    @endforeach
</ol>

@foreach ($chapters as $chapter)
    <div class="{{ !$loop->first ? 'page-break' : '' }}">
        <h2>{{ $chapter['num'] }}. {{ $chapter['title'] }}</h2>
        {!! $chapter['html'] !!}

        @foreach ($chapter['figures'] ?? [] as $fig)
            @if (! empty($fig['path']) && is_file($fig['path']))
                <div class="figure">
                    <img src="{{ $fig['path'] }}" alt="{{ $fig['caption'] }}">
                    <div class="figcaption">{{ $fig['caption'] }}</div>
                </div>
            @endif
        @endforeach
    </div>
@endforeach

<p class="footer-note">
    © COFINA — Gestion Parc Informatique — Documentation technique module Parc<br>
    Usage interne — ne pas diffuser hors organisation
</p>

</body>
</html>
