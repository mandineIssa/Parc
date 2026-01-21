<div id="stock-to-parc-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">📦 → 👨‍💼 Affecter au Parc</h4>
    
    <form action="{{ route('equipment.transition.stock-to-parc', $equipment) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Utilisateur -->
            <div>
                <label for="utilisateur_id" class="block text-sm font-bold text-cofina-red mb-2">Utilisateur *</label>
                <select name="utilisateur_id" id="utilisateur_id" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner un utilisateur --</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} {{ $user->prenom ?? '' }} - {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Département -->
            <div>
                <label for="departement" class="block text-sm font-bold text-cofina-red mb-2">Département *</label>
                <input type="text" name="departement" id="departement" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Informatique, RH..." required>
            </div>
            
            <!-- Poste -->
            <div>
                <label for="poste_affecte" class="block text-sm font-bold text-cofina-red mb-2">Poste *</label>
                <input type="text" name="poste_affecte" id="poste_affecte" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Chef de Projet, Technicien..." required>
            </div>
            
            <!-- Date d'affectation -->
            <div>
                <label for="date_affectation" class="block text-sm font-bold text-cofina-red mb-2">Date d'affectation *</label>
                <input type="date" name="date_affectation" id="date_affectation" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       value="{{ date('Y-m-d') }}" required>
            </div>
            
            <!-- Choix des fiches -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-cofina-red mb-2">Fiches à générer *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center p-4 border-2 border-cofina-gray rounded hover:bg-red-50 cursor-pointer">
                        <input type="checkbox" name="choix_fiches[]" id="fiche_mouvement" value="mouvement" class="h-5 w-5 text-cofina-red rounded focus:ring-cofina-red">
                        <label for="fiche_mouvement" class="ml-3 block">
                            <span class="font-bold">Fiche de Mouvement</span><br>
                            <span class="text-sm text-gray-600">Document de transfert de matériel</span>
                        </label>
                    </div>
                    <div class="flex items-center p-4 border-2 border-cofina-gray rounded hover:bg-red-50 cursor-pointer">
                        <input type="checkbox" name="choix_fiches[]" id="fiche_installation" value="installation" class="h-5 w-5 text-cofina-red rounded focus:ring-cofina-red">
                        <label for="fiche_installation" class="ml-3 block">
                            <span class="font-bold">Fiche d'Installation</span><br>
                            <span class="text-sm text-gray-600">Checklist d'installation complète</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-bold text-cofina-red mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Remarques sur l'affectation..."></textarea>
            </div>
        </div>
        
        <div class="flex gap-4 mt-8 pt-6 border-t border-cofina-gray">
            <button type="submit" class="btn-cofina-primary">
                📋 Soumettre pour validation
            </button>
            <button type="button" class="btn-cofina-outline" onclick="resetForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>