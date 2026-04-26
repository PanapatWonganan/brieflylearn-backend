<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BumpProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BumpProductController extends Controller
{
    /**
     * GET /api/v1/courses/{courseId}/bumps
     *
     * Public list of active bumps that apply to the given course.
     * A bump applies if:
     *   - bump.course_id === $courseId, OR
     *   - bump.course_id IS NULL  (global bump, available on every course)
     *
     * Sorted by sort_order ASC.
     */
    public function forCourse(Request $request, string $courseId): JsonResponse
    {
        $bumps = BumpProduct::query()
            ->active()
            ->forCourse($courseId)
            ->orderBy('sort_order')
            ->get([
                'id',
                'slug',
                'name',
                'description',
                'price',
                'original_price',
                'deliverable_type',
                'sort_order',
            ])
            ->map(fn (BumpProduct $b) => [
                'id' => $b->id,
                'slug' => $b->slug,
                'name' => $b->name,
                'description' => $b->description,
                'price' => (float) $b->price,
                'original_price' => $b->original_price !== null ? (float) $b->original_price : null,
                'deliverable_type' => $b->deliverable_type,
                'sort_order' => $b->sort_order,
            ]);

        return response()->json([
            'success' => true,
            'data' => $bumps,
        ]);
    }
}
