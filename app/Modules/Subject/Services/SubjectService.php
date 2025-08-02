<?php

namespace App\Modules\Subject\Services;

use App\Modules\Subject\exceptions\SubjectException;
use App\Modules\Subject\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectService
{
    /**
     * Get all subjects
     */
    public function getAllSubjects(): Collection
    {
        Log::info('SubjectService: Fetching all subjects');

        $subjects = Subject::all();

        Log::info('SubjectService: Retrieved ' . $subjects->count() . ' subjects');

        return $subjects;
    }

    /**
     * Create a new subject
     */
    public function createSubject(array $validatedData): Subject
    {
        Log::info('SubjectService: Creating new subject', [
            'name' => $validatedData['name'],
            'level' => $validatedData['level']?? null,
            'coefficient' => $validatedData['coefficient'] ?? null
        ]);

        $existsName = Subject::where('name', $validatedData['name'])->first();
        if($existsName!=null){
              throw SubjectException::SubjectNameConflict();
        }

        try {
            // Check if subject already exists for this level (only if level is provided)
            if (isset($validatedData['level']) && $validatedData['level'] !== null) {
                $exists = $this->subjectExistsForLevel($validatedData['name'], $validatedData['level']);

                if ($exists) {
                    Log::warning('SubjectService: Subject already exists', [
                        'name' => $validatedData['name'],
                        'level' => $validatedData['level']
                    ]);
                    throw new \Exception("La matière '{$validatedData['name']}' existe déjà pour le niveau {$validatedData['level']}.");
                }
            }

            $subject = Subject::create($validatedData);

            Log::info('SubjectService: Subject created successfully', [
                'subject_id' => $subject->id,
                'name' => $subject->name,
                'level' => $subject->level ?? null
            ]);

            return $subject;

        } catch (\Exception $e) {
            Log::error('SubjectService: Failed to create subject', [
                'error' => $e->getMessage(),
                'data' => $validatedData
            ]);
            throw $e;
        }
    }

    /**
     * Update a subject
     */
    public function updateSubject(Subject $subject, array $validatedData): Subject
    {
        Log::info('SubjectService: Updating subject', [
            'subject_id' => $subject->id,
            'old_name' => $subject->name,
            'old_level' => $subject->level,
            'new_name' => $validatedData['name'] ?? $subject->name,
            'new_level' => $validatedData['level'] ?? $subject->level
        ]);

        try {
            // Check if the updated subject would conflict with existing one
            if (isset($validatedData['name']) && isset($validatedData['level']) && $validatedData['level'] !== null) {
                $exists = $this->subjectExistsForLevel(
                    $validatedData['name'],
                    $validatedData['level'],
                    $subject->id
                );

                if ($exists) {
                    Log::warning('SubjectService: Updated subject would conflict', [
                        'name' => $validatedData['name'],
                        'level' => $validatedData['level'],
                        'current_subject_id' => $subject->id
                    ]);
                    throw new \Exception("La matière '{$validatedData['name']}' existe déjà pour le niveau {$validatedData['level']}.");
                }
            }

            $subject->update($validatedData);

            Log::info('SubjectService: Subject updated successfully', [
                'subject_id' => $subject->id,
                'updated_fields' => array_keys($validatedData)
            ]);

            return $subject->fresh();

        } catch (\Exception $e) {
            Log::error('SubjectService: Failed to update subject', [
                'subject_id' => $subject->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a subject
     */
    public function deleteSubject(Subject $subject): bool
    {
        Log::info('SubjectService: Deleting subject', [
            'subject_id' => $subject->id,
            'name' => $subject->name,
            'level' => $subject->level
        ]);

        try {
            $subject->delete();

            Log::info('SubjectService: Subject deleted successfully', ['subject_id' => $subject->id]);

            return true;

        } catch (\Exception $e) {
            Log::error('SubjectService: Failed to delete subject', [
                'subject_id' => $subject->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get subjects by IDs
     */
    public function getSubjectsByIds(array $ids): Collection
    {
        Log::info('SubjectService: Getting subjects by IDs', [
            'ids' => $ids,
            'count' => count($ids)
        ]);

        if (empty($ids)) {
            Log::warning('SubjectService: No IDs provided for subject lookup');
            return collect();
        }

        try {
            $subjects = Subject::whereIn('id', $ids)->get();

            Log::info('SubjectService: Retrieved subjects by IDs', [
                'requested_count' => count($ids),
                'found_count' => $subjects->count(),
                'found_ids' => $subjects->pluck('id')->toArray()
            ]);

            return $subjects;

        } catch (\Exception $e) {
            Log::error('SubjectService: Failed to get subjects by IDs', [
                'ids' => $ids,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if subject exists for a specific level
     */
    private function subjectExistsForLevel(string $name, string $level, ?int $excludeId = null): bool
    {
        Log::debug('SubjectService: Checking if subject exists for level', [
            'name' => $name,
            'level' => $level,
            'exclude_id' => $excludeId
        ]);

        $query = DB::table('subjects')
            ->where('name', $name)
            ->where('level', $level);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();

        Log::debug('SubjectService: Subject existence check result', [
            'exists' => $exists,
            'name' => $name,
            'level' => $level
        ]);

        return $exists;
    }
}
