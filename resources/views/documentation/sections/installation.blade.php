<div class="doc-prose">
    <p class="text-lg text-gray-600 mb-6">
        Procédure d’installation, configuration et mise en production de l’application
        Gestion Parc Informatique COFINA.
    </p>

    <h2>1. Prérequis</h2>
    <ul>
        <li>PHP 8.2+ (<code>pdo_mysql</code>, <code>mbstring</code>, <code>openssl</code>, <code>intl</code>, <code>curl</code>, <code>zip</code>)</li>
        <li>Composer 2.x</li>
        <li>Node.js 20+ et npm</li>
        <li>MySQL 8.0+ (ou MariaDB 10.4+)</li>
        <li>Serveur web (Apache/Nginx) ou <code>php artisan serve</code> en dev</li>
    </ul>

    <h2>2. Installation locale</h2>
    <pre>git clone [url-du-depot] parc
cd parc
composer install
cp .env.example .env
php artisan key:generate</pre>

    <h3>Base de données</h3>
    <pre># Créer la base
mysql -u root -e "CREATE DATABASE parc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"</pre>
    <p>Configurer <code>.env</code> :</p>
    <pre>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parc
DB_USERNAME=root
DB_PASSWORD=</pre>
    <pre>php artisan migrate
# Optionnel : php artisan db:seed</pre>

    <h3>Assets front</h3>
    <pre>npm install
npm run build
# Dev : npm run dev</pre>

    <h3>Lancer l’application</h3>
    <pre>php artisan serve</pre>
    <p>URL par défaut : <code>http://127.0.0.1:8000</code></p>

    <h2>3. Variables d’environnement utiles</h2>
    <table>
        <thead>
            <tr><th>Variable</th><th>Description</th></tr>
        </thead>
        <tbody>
            <tr><td>APP_ENV</td><td><code>local</code> / <code>production</code></td></tr>
            <tr><td>APP_DEBUG</td><td><code>false</code> en production</td></tr>
            <tr><td>APP_URL</td><td>URL publique de l’app</td></tr>
            <tr><td>SUPER_ADMIN_EMAILS</td><td>Emails bypass admin (virgules)</td></tr>
            <tr><td>EQUIPMENT_RENEWAL_ORANGE_YEARS</td><td>Seuil orange renouvellement (défaut 2)</td></tr>
            <tr><td>EQUIPMENT_RENEWAL_RED_YEARS</td><td>Seuil rouge (défaut 3)</td></tr>
        </tbody>
    </table>

    <h2>4. Environnement de tests (PHPUnit)</h2>
    <pre>mysql -u root -e "CREATE DATABASE IF NOT EXISTS testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
cp .env.testing.example .env.testing
php artisan test</pre>

    <h2>5. Permissions fichiers</h2>
    <pre>chmod -R 775 storage bootstrap/cache
# Propriétaire = utilisateur du serveur web (www-data, nginx, etc.)</pre>

    <h2>6. Production</h2>
    <ol>
        <li><code>composer install --no-dev --optimize-autoloader</code></li>
        <li><code>php artisan config:cache</code>, <code>route:cache</code>, <code>view:cache</code></li>
        <li>Configurer HTTPS et <code>APP_URL</code></li>
        <li>Planifier les sauvegardes MySQL</li>
        <li>Vérifier que <code>routes/debug.php</code> n’est pas exposé (chargé seulement si <code>APP_ENV=local</code>)</li>
    </ol>

    <h2>7. CI / qualité</h2>
    <p>Pipeline GitHub : <code>.github/workflows/ci.yml</code></p>
    <ul>
        <li>PHPUnit sur MySQL</li>
        <li>PHPStan (<code>phpstan.neon</code>)</li>
    </ul>

    <h2>8. Dépannage courant</h2>
    <table>
        <thead>
            <tr><th>Problème</th><th>Solution</th></tr>
        </thead>
        <tbody>
            <tr><td>Erreur migration MySQL</td><td>Vérifier version MySQL, droits utilisateur, charset utf8mb4</td></tr>
            <tr><td>Page blanche 500</td><td><code>storage/logs/laravel.log</code>, permissions storage</td></tr>
            <tr><td>Assets manquants</td><td><code>npm run build</code></td></tr>
            <tr><td>Session / CSRF</td><td><code>php artisan config:clear</code>, cookie domain</td></tr>
        </tbody>
    </table>

    <div class="doc-box-cofina">
        <strong>Support technique interne COFINA</strong> — pour l’hébergement bancaire, suivre les procédures infra groupe (pare-feu, WAF, certificats).
    </div>
</div>
