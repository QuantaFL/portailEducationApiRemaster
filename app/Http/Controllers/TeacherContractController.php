<?php

namespace App\Http\Controllers;

use App\Modules\User\Models\UserModel;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherContractController extends Controller
{
    /**
     * Generate and send a work contract to a teacher.
     *
     * This method validates the teacher's email, retrieves the teacher's data,
     * populates a contract with predefined and dynamic data, and then uses
     * the ContractService to generate and email the contract.
     *
     * @param  Request  $request The incoming request containing the teacher's email.
     * @return JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function sendContract(Request $request): JsonResponse
    {
        $request->validate([
            'teacher_email' => 'required|email',
        ]);

        $teacher = UserModel::where('email', $request->input('teacher_email'))->firstOrFail();

        $contractData = [
            'nom_etablissement' => 'Groupe Scolaire Quanta',
            'adresse_etablissement' => '123 Rue de l\'Espoir, Dakar, Sénégal',
            'telephone_etablissement' => '+221 33 123 45 67',
            'email_etablissement' => 'contact@quanta.sn',
            'statut_juridique' => 'SARL',
            'ninea_etablissement' => '97647688650323',
            'nom_representant_legal' => 'M. Cheikh Tidiane Traore',
            'qualite_representant_legal' => 'Directeur Général',
            'nom_enseignant' => $teacher->last_name ?? '',
            'prenom_enseignant' => $teacher->first_name ?? '',
            'date_lieu_naissance_enseignant' => ($teacher->birthday ? date('d/m/Y', strtotime($teacher->birth_date)) : '') . ($teacher->birth_place ? ' à ' . $teacher->birth_place : ''),
            'nationalite_enseignant' => $teacher->nationality ?? 'Sénégalaise',
            'cni_passeport_enseignant' => $teacher->cni_number ?? '8983293239832',
            'adresse_enseignant' => $teacher->address ?? '456 Avenue de la Liberté, Dakar, Sénégal',
            'telephone_enseignant' => $teacher->phone ?? '+221 77 392 32 47',
            'email_enseignant' => $teacher->email ?? $request->input('teacher_email'),
            'matieres_enseignees' => $teacher->subjects ?? 'Mathématiques, Physique-Chimie',
            'niveaux_enseignement' => $teacher->teaching_levels ?? 'Lycée (Seconde, Première, Terminale)',
            'date_debut_contrat' => date('d F Y', strtotime('+1 week')),
            'mode_paiement' => 'virement bancaire',
            'nombre_heures_travail' => '30',
            'jour_debut_semaine' => 'Lundi',
            'jour_fin_semaine' => 'Vendredi',
            'ville_tribunal_competent' => 'Dakar',
            'lieu_signature' => 'Dakar',
            'date_signature' => date('d F Y'),
        ];

        if (ContractService::generateAndSendContract($contractData, $request->input('teacher_email'))) {
            return response()->json(['message' => 'Contrat généré et envoyé avec succès.'], 200);
        }

        return response()->json(['message' => 'Échec de la génération ou de l\'envoi du contrat.'], 500);
    }
}
