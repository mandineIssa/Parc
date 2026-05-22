<div class="doc-prose">
    <p class="text-lg text-gray-600 mb-6">
        Manuel opérationnel COFINA couvrant <strong>tous les scénarios</strong> de l'application.
        Les figures s'affichent automatiquement lorsque les fichiers PNG sont déposés dans
        <code>public/doc-captures/</code>.
    </p>

    <div class="doc-box-cofina doc-manuel-toc mb-8">
        <h2 class="text-lg font-semibold text-[#7A0C1A] mb-3">Table des matières</h2>
        <ol class="text-sm space-y-1">
            <li><a href="#ch1">1. Présentation</a></li>
            <li><a href="#ch2">2. Connexion et navigation</a></li>
            <li><a href="#ch3">3. Équipements et inventaire</a></li>
            <li><a href="#ch4">4. Stocks CELER / DECELER</a></li>
            <li><a href="#ch5">5. Parc et affectations</a></li>
            <li><a href="#ch6">6. Transitions (tous scénarios)</a></li>
            <li><a href="#ch7">7. Maintenance, hors service, perdu</a></li>
            <li><a href="#ch8">8. Rapports</a></li>
            <li><a href="#ch9">9. Change Management</a></li>
            <li><a href="#ch10">10. EOD Suivi</a></li>
            <li><a href="#ch11">11. Incidents</a></li>
            <li><a href="#ch12">12. Contrôles IT</a></li>
            <li><a href="#ch13">13. Infrastructure IT</a></li>
            <li><a href="#ch14">14. Configuration</a></li>
            <li><a href="#ch15">15. FAQ et dépannage</a></li>
            <li><a href="#annexe-captures">Annexe A — Captures</a></li>
            <li><a href="#annexe-urls">Annexe B — URLs</a></li>
        </ol>
    </div>

    <div class="doc-box-info text-sm mb-8">
        <strong>Comment ajouter les captures :</strong> faites une capture d'écran (Win + Shift + S),
        enregistrez en PNG avec le nom indiqué sous chaque figure (ex. <code>12-parc-index.png</code>),
        copiez dans <code>public/doc-captures/</code>, puis actualisez cette page (F5).
    </div>
</div>

@include('documentation.sections.manuel.ch01-presentation')
@include('documentation.sections.manuel.ch02-acces-navigation')
@include('documentation.sections.manuel.ch03-parc-equipements')
@include('documentation.sections.manuel.ch04-parc-transitions')
@include('documentation.sections.manuel.ch05-change-eod-incidents')
@include('documentation.sections.manuel.ch06-infra-config-faq')
