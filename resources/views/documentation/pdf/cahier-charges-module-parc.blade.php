<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Cahier des charges — Gestion du parc informatique</title>
    <style>
        @page { margin: 16mm 14mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5pt; color: #1f2937; line-height: 1.45; }
        h1 { color: #A61B29; font-size: 18pt; border-bottom: 2px solid #A61B29; padding-bottom: 6px; }
        h2 { color: #7A0C1A; font-size: 12.5pt; margin-top: 16px; page-break-after: avoid; }
        h3 { color: #374151; font-size: 10.5pt; margin-top: 10px; }
        .cover { text-align: center; padding: 55px 12px 40px; page-break-after: always; }
        .cover h1 { font-size: 20pt; border: none; }
        .cover .sub { font-size: 11pt; color: #6b7280; margin-top: 10px; }
        .cover .doc-type { margin-top: 28px; font-size: 10pt; color: #7A0C1A; font-weight: bold; letter-spacing: 0.04em; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 12px; font-size: 8.5pt; }
        th, td { border: 1px solid #e5e7eb; padding: 5px 7px; text-align: left; vertical-align: top; }
        th { background: #fdf2f3; color: #7A0C1A; }
        ul, ol { margin: 4px 0 8px 16px; }
        li { margin-bottom: 3px; }
        pre { background: #f3f4f6; border: 1px solid #e5e7eb; padding: 8px 10px; font-size: 7.5pt; white-space: pre-wrap; }
        code { font-size: 8pt; background: #f3f4f6; padding: 1px 3px; }
        .page-break { page-break-before: always; }
        .toc li { margin-bottom: 2px; }
        .footer-note { margin-top: 28px; text-align: center; font-size: 8pt; color: #9ca3af; }
        .meta { font-size: 8.5pt; color: #6b7280; }
    </style>
</head>
<body>

<div class="cover">
    <p class="doc-type">CAHIER DES CHARGES</p>
    <h1>Gestion du parc informatique</h1>
    <p class="sub"><strong>GPI — Équipements · Stock · Parc · Gestion — COFINA</strong></p>
    <p class="sub">Cahier des charges métier + compléments (parties manquantes) — Version {{ $version }}</p>
    <p class="sub" style="margin-top: 36px;">Document généré le {{ $generatedAt }}</p>
    <p class="sub">Usage interne — référentiel d’exigences / recette</p>
</div>

<p class="meta">Ce document décrit le périmètre, les acteurs, les flux, les exigences et les critères d’acceptation du module Parc.</p>

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
    </div>
@endforeach

<p class="footer-note">
    © COFINA — Cahier des charges Gestion du parc informatique<br>
    Référentiel métier + plan de renforcement (Parc / Équipements / Stock / Gestion).
</p>

</body>
</html>
