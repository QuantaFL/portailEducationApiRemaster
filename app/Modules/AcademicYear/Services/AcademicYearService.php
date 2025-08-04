<?php

namespace App\Modules\AcademicYear\Services;

use App\Modules\AcademicYear\Exceptions\AcademicYearException;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\StatusAcademicYearEnum;
use App\Modules\Term\Models\Term;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class AcademicYearService
 *
 * Service pour la logique métier des années académiques.
 */
class AcademicYearService
{
    /**
     * Récupère toutes les années académiques.
     *
     * @return Collection
     */
    public function getAllAcademicYears(): Collection
    {
        return AcademicYear::all();
    }

    /**
     * Récupère une année académique par son ID.
     *
     * @param int $id
     * @return AcademicYear
     * @throws AcademicYearException
     */
    public function getAcademicYearById(int $id): AcademicYear
    {
        $academicYear = AcademicYear::find($id);

        if (!$academicYear) {
            throw AcademicYearException::academicYearNotFound();
        }

        return $academicYear;
    }

    /**
     * Récupère l'année académique en cours.
     *
     * @return AcademicYear
     * @throws AcademicYearException
     */
    public function getCurrentAcademicYear(): AcademicYear
    {
        $currentYear = AcademicYear::getCurrentAcademicYear();
        if (!$currentYear) {
            throw AcademicYearException::noCurrentAcademicYear();
        }

        return $currentYear;
    }

    /**
     * Récupère les années académiques actives.
     *
     * @return Collection
     * @throws AcademicYearException
     */
    public function getActiveAcademicYears(): Collection
    {
        $activeYears = AcademicYear::where('status', 'active')->get();

        if ($activeYears->isEmpty()) {
            throw AcademicYearException::noActiveAcademicYears();
        }

        return $activeYears;
    }

    /**
     * Récupère les semestres d'une année académique.
     *
     * @param int $academicYearId
     * @return Collection
     * @throws AcademicYearException
     */
    public function getTermsByAcademicYear(int $academicYearId): Collection
    {
        $academicYear = $this->getAcademicYearById($academicYearId);

        return $academicYear->terms;
    }

    /**
     * Crée une nouvelle année académique.
     *
     * @param array $data
     * @return array
     * @throws AcademicYearException
     */
    public function createAcademicYear(array $data): array
    {
        $this->validateAcademicYearData($data);

        DB::beginTransaction();

        try {
            // Set all previous academic years to finished
            DB::table('academic_years')->update([
                'status' => StatusAcademicYearEnum::TERMINE->value
            ]);

            // Create new academic year
            $label = $data['start_date'] . '-' . $data['end_date'];
            $academicYear = AcademicYear::create([
                'label' => $label,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => StatusAcademicYearEnum::EN_COURS->value
            ]);

            // Create terms for the academic year
            $terms = $this->createTermsForAcademicYear($academicYear, $data);

            DB::commit();

            return [
                'academic_year' => $academicYear,
                'terms' => $terms
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create academic year with terms: ' . $e->getMessage());
            throw AcademicYearException::termCreationFailed();
        }
    }

    /**
     * Met à jour une année académique.
     *
     * @param AcademicYear $academicYear
     * @param array $data
     * @return AcademicYear
     */
    public function updateAcademicYear(AcademicYear $academicYear, array $data): AcademicYear
    {
        $academicYear->update($data);

        return $academicYear;
    }

    /**
     * Supprime une année académique.
     *
     * @param AcademicYear $academicYear
     * @return bool
     */
    public function deleteAcademicYear(AcademicYear $academicYear): bool
    {
        return $academicYear->delete();
    }

    /**
     * Valide les données de l'année académique.
     *
     * @param array $data
     * @return void
     * @throws AcademicYearException
     */
    private function validateAcademicYearData(array $data): void
    {
        $start = (int) $data['start_date'];
        $end = (int) $data['end_date'];
        $currentYear = now()->year;

        // Check if academic year already exists
        $exists = DB::table('academic_years')
            ->where('start_date', $start)
            ->where('end_date', $end)
            ->exists();

        if ($exists) {
            throw AcademicYearException::duplicateAcademicYear();
        }

        // Check if start date is not in the past
        if ($start < $currentYear) {
            throw AcademicYearException::startDateInThePast();
        }

        // Check if start date is before end date
        if ($start >= $end) {
            throw AcademicYearException::invalidDateRange();
        }

        // Check if duration is exactly 1 year
        if (($end - $start) !== 1) {
            throw AcademicYearException::invalidDuration();
        }
    }

    /**
     * Crée les semestres pour une année académique.
     *
     * @param AcademicYear $academicYear
     * @param array $data
     * @return array
     */
    private function createTermsForAcademicYear(AcademicYear $academicYear, array $data): array
    {
        $startDate = Carbon::parse($data['start_date'])->startOfDay();
        $endDate = Carbon::parse($data['end_date'])->startOfDay();

        $term1 = Term::create([
            'name' => 'Semestre 1',
            'academic_year_id' => $academicYear->id,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $term2 = Term::create([
            'name' => 'Semestre 2',
            'academic_year_id' => $academicYear->id,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return [
            'term1' => $term1,
            'term2' => $term2
        ];
    }
}
