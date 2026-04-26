<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * GET /api/v1/groups/my
     * List groups the authenticated user is an active member of.
     */
    public function myGroups(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $groups = Group::query()
            ->whereHas('members', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('status', GroupMember::STATUS_ACTIVE);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'slug', 'name', 'description', 'type']);

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    /**
     * GET /api/v1/groups/{slug}
     * Show full group details, but only if the caller is an active member.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $group = Group::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $group) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบกลุ่มที่ระบุ',
            ], 404);
        }

        $membership = GroupMember::where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->where('status', GroupMember::STATUS_ACTIVE)
            ->first();

        if (! $membership) {
            return response()->json([
                'success' => false,
                'message' => 'คุณไม่ได้เป็นสมาชิกกลุ่มนี้',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $group->id,
                'slug' => $group->slug,
                'name' => $group->name,
                'description' => $group->description,
                'type' => $group->type,
                'zoom_link' => $group->zoom_link,
                'meeting_schedule' => $group->meeting_schedule,
                'resources' => $group->resources ?? [],
                'max_members' => $group->max_members,
                'members_count' => $group->activeMembers()->count(),
                'membership' => [
                    'role' => $membership->role,
                    'joined_at' => $membership->joined_at?->toIso8601String(),
                ],
            ],
        ]);
    }
}
