<?php

namespace App\Modules\Statistique\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatistiqueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'general_statistics' => $this->resource['general'] ?? null,
            'current_academic_year' => $this->resource['current_academic_year'] ?? null,
            'quick_stats' => $this->resource['quick_stats'] ?? null,
            'generated_at' => now()->toISOString(),
            'metadata' => [
                'source' => 'Portail Education API',
                'version' => '1.0',
                'description' => 'Comprehensive educational statistics and analytics'
            ]
        ];
    }
}