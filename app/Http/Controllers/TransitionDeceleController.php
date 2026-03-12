<?php
namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Stock;
use App\Models\Deceler;
use App\Models\TransitionHistory;   // adaptez si votre modèle a un autre nom
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransitionDeceleController extends Controller
{
    /**
     * Soumettre le flux Parc → Stock Décélé (3 étapes).
     *
     * Données attendues (JSON) :
     *   - retour         : Fiche de retour (étape 1)
     *   - deceleration   : Fiche de décélération (étape 2)
     *   - mouvement_decele : Fiche de mouvement (étape 3)
     *   - equipment_id   : ID de l'équipement
     *   - transition_type: 'parc_to_stock_decele'
     */
    public function submitDecele(Request $request, Equipment $equipment)
    {
        // ── Validation minimale ───────────────────────────────────
        $request->validate([
            'retour'                            => 'required|array',
            'retour.detenteur_nom'              => 'required|string|max:255',
            'retour.detenteur_prenom'           => 'required|string|max:255',
            'retour.date_retour'                => 'required|date',
            'retour.raison_retour'              => 'required|string',

            'deceleration'                      => 'required|array',
            'deceleration.etat_retour'          => 'required|in:bon,reparable,irreparable',
            'deceleration.localisation_physique'=> 'required|string|max:255',
            'deceleration.date_entree_stock'    => 'required|date',

            'mouvement_decele'                  => 'required|array',
            'mouvement_decele.destination_decele'        => 'required|string',
            'mouvement_decele.signature_expediteur_decele' => 'required|string',
        ]);

        // ── Vérifier que l'équipement est bien dans le parc ───────
        if ($equipment->statut !== 'parc') {
            return response()->json([
                'success' => false,
                'message' => 'Cet équipement n\'est pas dans le parc. Statut actuel : ' . $equipment->statut,
            ], 422);
        }

        DB::beginTransaction();

        try {
            $retour      = $request->input('retour');
            $deceleration = $request->input('deceleration');
            $mouvement   = $request->input('mouvement_decele');

            // ── Déterminer l'état du stock selon le diagnostic ────
            $etatStock = match ($deceleration['etat_retour']) {
                'bon'         => 'disponible',
                'reparable'   => 'reserve',      // réservé pour maintenance
                'irreparable' => 'reserve',      // réservé pour HDS
                default       => 'disponible',
            };

            // ── Créer ou mettre à jour l'entrée Stock (décélé) ───
            $stock = Stock::create([
                'numero_serie'         => $equipment->numero_serie,
                'type_stock'           => 'deceler',
                'localisation_physique'=> $deceleration['localisation_physique'],
                'etat'                 => $etatStock,
                'quantite'             => 1,
                'date_entree'          => $deceleration['date_entree_stock'],
                'date_sortie'          => null,
                'observations'         => $deceleration['observations_retour'] ?? null,
            ]);

            // ── Créer la fiche Décélération ───────────────────────
            Deceler::create([
                'stock_id'              => $stock->id,
                'origine'               => 'parc',
                'numero_serie_origine'  => $equipment->numero_serie,
                'date_retour'           => $retour['date_retour'],
                'raison_retour'         => $retour['raison_retour'],
                'diagnostic'            => $deceleration['diagnostic'] ?? null,
                'etat_retour'           => $deceleration['etat_retour'],
                'valeur_residuelle'     => isset($deceleration['valeur_residuelle'])
                                            && $deceleration['valeur_residuelle'] !== ''
                                            ? (float) $deceleration['valeur_residuelle']
                                            : null,
                'observations_retour'   => $deceleration['observations_retour'] ?? null,
            ]);

            // ── Mettre à jour le statut de l'équipement ───────────
            //    On utilise 'stock' comme statut intermédiaire ;
            //    adaptez si votre enum a une valeur 'stock_decele'
            $equipment->update([
                'statut' => 'stock',
                // Ajoutez d'autres champs si nécessaire, ex:
                // 'user_id' => null,
                // 'agence_id' => null,
            ]);

            // ── Enregistrer l'historique de transition ────────────
            //    Adaptez le modèle/table à votre architecture
            if (class_exists(TransitionHistory::class)) {
                TransitionHistory::create([
                    'equipment_id'    => $equipment->id,
                    'from_status'     => 'parc',
                    'to_status'       => 'stock',
                    'transition_type' => 'parc_to_stock_decele',
                    'user_id'         => auth()->id(),
                    'notes'           => json_encode([
                        'retour'       => $retour,
                        'deceleration' => $deceleration,
                        'mouvement'    => $mouvement,
                    ]),
                    'created_at'      => now(),
                ]);
            }

            // ── TODO : Générer les PDFs des fiches (optionnel) ────
            // $this->generatePdfRetour($equipment, $retour, $deceleration, $mouvement);
            // $this->generatePdfMouvement($equipment, $mouvement);

            DB::commit();

            Log::info("Transition parc→stock_décélé OK", [
                'equipment_id' => $equipment->id,
                'stock_id'     => $stock->id,
                'user_id'      => auth()->id(),
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Équipement transféré en stock décélé avec succès.',
                'redirect_url' => route('equipment.show', $equipment),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur transition parc→stock_décélé", [
                'equipment_id' => $equipment->id,
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la transition : ' . $e->getMessage(),
            ], 500);
        }
    }
}