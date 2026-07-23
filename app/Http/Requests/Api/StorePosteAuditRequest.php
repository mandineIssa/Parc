<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePosteAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'hostname' => ['required', 'string', 'max:255'],
            'utilisateurSession' => ['required', 'string', 'max:255'],
            'fabricant' => ['required', 'string', 'max:255'],
            'modele' => ['required', 'string', 'max:255'],
            'numeroSerie' => ['required', 'string', 'max:255'],
            'os' => ['required', 'string', 'max:255'],
            'versionOS' => ['required', 'string', 'max:255'],
            'antivirusDefender' => ['required', 'boolean'],
            'firewall' => ['required', 'string', 'max:512'],
            'bitlocker' => ['required', 'string', 'max:512'],
            'usbStockageBloque' => ['required', 'boolean'],
            'adresseMAC' => ['required', 'string', 'max:64'],
            'adresseIP' => ['required', 'string', 'max:64'],
            'dateAudit' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'utilisateurSession.required' => 'Le champ utilisateurSession est obligatoire (DOMAINE\\utilisateur).',
            'antivirusDefender.boolean' => 'antivirusDefender doit être un booléen.',
            'usbStockageBloque.boolean' => 'usbStockageBloque doit être un booléen.',
            'dateAudit.date' => 'dateAudit doit être une date ISO 8601 valide.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalise les booléens envoyés parfois en "true"/"false" string
        foreach (['antivirusDefender', 'usbStockageBloque'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Payload invalide.',
            'error' => 'validation_error',
            'errors' => $validator->errors(),
        ], 400));
    }

    /**
     * Attributs normalisés snake_case pour persistance.
     *
     * @return array<string, mixed>
     */
    public function posteAttributes(): array
    {
        $v = $this->validated();

        return [
            'hostname' => $v['hostname'],
            'numero_serie' => $v['numeroSerie'],
            'utilisateur_session' => $v['utilisateurSession'],
            'fabricant' => $v['fabricant'],
            'modele' => $v['modele'],
            'os' => $v['os'],
            'version_os' => $v['versionOS'],
            'antivirus_defender' => $v['antivirusDefender'],
            'firewall' => $v['firewall'],
            'bitlocker' => $v['bitlocker'],
            'usb_stockage_bloque' => $v['usbStockageBloque'],
            'adresse_mac' => $v['adresseMAC'],
            'adresse_ip' => $v['adresseIP'],
            'date_audit' => $v['dateAudit'],
        ];
    }
}
