<!-- partials/reject_modal.blade.php -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg max-w-md w-full mx-4 shadow-lg relative">
        <!-- Bouton de fermeture -->
        <button type="button" onclick="closeRejectModal()" 
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 font-bold text-xl">&times;</button>

        <h3 class="text-2xl font-bold text-cofina-red mb-4">❌ Rejeter cette demande</h3>
        
        <form method="POST" action="{{ route('transitions.reject', $approval) }}">
            @csrf
            
            <div class="mb-6">
                <label for="raison_rejet" class="block font-bold text-cofina-red mb-2">
                    Raison du rejet *
                </label>
                <textarea name="raison_rejet" id="raison_rejet" rows="5"
                          class="w-full px-4 py-3 border-2 border-cofina-gray rounded-lg focus:border-cofina-red"
                          placeholder="Expliquez pourquoi vous rejetez cette demande..."
                          required></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-cofina-danger flex-1 py-3 font-bold">
                    ❌ Confirmer le rejet
                </button>
                <button type="button" onclick="closeRejectModal()" 
                        class="btn-cofina-outline flex-1 py-3 font-bold">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Ouvrir le modal
function openRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Fermer le modal
function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Fermer si clic en dehors du contenu
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
