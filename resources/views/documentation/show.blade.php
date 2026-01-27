
@extends('layouts.app')

@section('title', 'Documentation - ' . ucfirst(str_replace('-', ' ', $section)))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Navigation -->
        <div class="mb-6">
            <a href="{{ route('documentation.index') }}" 
               class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la documentation
            </a>
        </div>

        <!-- Contenu de la section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
            @switch($section)
                @case('utilisateur')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-user text-blue-600 mr-3"></i>
                        Guide Utilisateur
                    </h1>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <h2>1. Connexion au système</h2>
                        <p>Pour accéder au système :</p>
                        <ol>
                            <li>Accédez à l'URL : <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">http://10.221.5.17</code></li>
                            <li>Entrez votre email et mot de passe</li>
                            <li>Cliquez sur "Se connecter"</li>
                        </ol>
                        
                        <h2>2. Tableau de bord</h2>
                        <p>Après connexion, vous accédez au tableau de bord qui affiche :</p>
                        <ul>
                            <li>Vos équipements assignés</li>
                            <li>Statistiques personnelles</li>
                            <li>Notifications importantes</li>
                        </ul>
                        
                        <h2>3. Gestion des équipements</h2>
                        <p>Pour consulter vos équipements :</p>
                        <ol>
                            <li>Cliquez sur "Mes Équipements" dans le menu latéral</li>
                            <li>Consultez la liste de vos équipements</li>
                            <li>Cliquez sur un équipement pour voir les détails</li>
                        </ol>
                        
                        <h2>4. Support technique</h2>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <p class="text-blue-800 dark:text-blue-300">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Contact support :</strong><br>
                                Email : support.it@cofina.sn<br>
                                Téléphone : Contacter le service IT
                            </p>
                        </div>
                    </div>
                    @break
                    
                @case('agent-it')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-laptop-code text-green-600 mr-3"></i>
                        Guide Agent IT
                    </h1>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <h2>Gestion des équipements</h2>
                        <p>En tant qu'agent IT, vous pouvez :</p>
                        <ul>
                            <li>Ajouter de nouveaux équipements</li>
                            <li>Modifier les informations des équipements</li>
                            <li>Gérer les transitions d'état (stock → parc, etc.)</li>
                            <li>Importer des équipements via CSV</li>
                        </ul>
                        
                        <h2>Transitions d'état</h2>
                        <p>Procédure pour changer l'état d'un équipement :</p>
                        <ol>
                            <li>Sélectionnez un équipement dans la liste</li>
                            <li>Cliquez sur "Effectuer une transition"</li>
                            <li>Sélectionnez le type de transition</li>
                            <li>Remplissez le formulaire et soumettez</li>
                        </ol>
                    </div>
                    @break
                    
                @case('admin')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-user-shield text-purple-600 mr-3"></i>
                        Guide Administrateur
                    </h1>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <h2>Gestion des utilisateurs</h2>
                        <p>Accès : Menu latéral → Administration</p>
                        <ul>
                            <li>Ajouter de nouveaux utilisateurs</li>
                            <li>Modifier les rôles et permissions</li>
                            <li>Désactiver des comptes</li>
                        </ul>
                        
                        <h2>Système d'approbations</h2>
                        <p>En tant qu'administrateur, vous pouvez :</p>
                        <ul>
                            <li>Voir toutes les approbations en attente</li>
                            <li>Approuver ou rejeter les demandes</li>
                            <li>Consulter l'historique des approbations</li>
                        </ul>
                    </div>
                    @break
                    
                @case('api')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-code text-orange-600 mr-3"></i>
                        Documentation API
                    </h1>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <h2>Routes principales</h2>
                        <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg overflow-x-auto">
GET     /api/equipements           Liste des équipements
POST    /api/equipements          Créer un équipement
GET     /api/equipements/{id}     Détails d'un équipement
PUT     /api/equipements/{id}     Modifier un équipement
DELETE  /api/equipements/{id}     Supprimer un équipement</pre>
                        
                        <h2>Authentification</h2>
                        <p>Utilisez l'authentification Bearer Token :</p>
                        <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                        Authorization: Bearer {votre_token}
                        </code>
                    </div>
                    @break
                    
                @case('installation')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-download text-red-600 mr-3"></i>
                        Guide d'Installation
                    </h1>
                    
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <h2>Prérequis</h2>
                        <ul>
                            <li>PHP 8.1+</li>
                            <li>Composer 2.0+</li>
                            <li>MySQL 8.0+ ou MariaDB 10.4+</li>
                            <li>Node.js 18+ et NPM</li>
                        </ul>
                        
                        <h2>Installation</h2>
                        <pre class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
# 1. Cloner le projet
git clone [repository-url] mon-projet
cd mon-projet

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JavaScript
npm install

# 4. Configurer l'environnement
cp .env.example .env
# Éditer .env avec vos paramètres

# 5. Générer la clé
php artisan key:generate

# 6. Migrer la base de données
php artisan migrate --seed

# 7. Démarrer le serveur
php artisan serve</pre>
                    </div>
                    @break
                    
                @default
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-book text-gray-600 mr-3"></i>
                        Documentation - {{ ucfirst(str_replace('-', ' ', $section)) }}
                    </h1>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                        <h2 class="text-xl font-semibold text-yellow-800 dark:text-yellow-300 mb-2">
                            <i class="fas fa-clock mr-2"></i>
                            Section en cours de rédaction
                        </h2>
                        <p class="text-yellow-700 dark:text-yellow-400">
                            Cette section de documentation sera disponible prochainement.
                        </p>
                    </div>
            @endswitch
        </div>
    </div>
</div>
@endsection
