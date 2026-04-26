<?php

namespace Database\Seeders;

use App\Models\BumpProduct;
use App\Models\Group;
use Illuminate\Database\Seeder;

/**
 * Seeds the AI ฿100M Done-With-You coaching group, then links the
 * 'dwy-upgrade' bump → this group via deliverable_ref_id.
 *
 * Idempotent: re-running updates the existing group + bump link in place.
 */
class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $group = Group::updateOrCreate(
            ['slug' => 'ai-100m-dwy'],
            [
                'name' => 'AI ฿100M — Done-With-You Coaching',
                'description' => 'Group Coaching 6 ครั้ง ที่ผมรีวิว Offer · Funnel · Pitch ของคุณเองโดยตรง '
                    . 'และแก้ให้สดๆ ในคลาส (รับแค่ 12 คน/รุ่น)',
                'type' => Group::TYPE_COACHING,
                'zoom_link' => null, // admin จะใส่ใน Filament หลัง seed
                'meeting_schedule' => 'ทุกวันอังคาร 20:00-21:30 (รวม 6 ครั้ง) — ตารางเริ่มแจ้งหลังปิดรุ่น',
                'resources' => null,
                'max_members' => 12,
                'is_active' => true,
            ]
        );

        // Link the dwy-upgrade bump to this group
        $bump = BumpProduct::where('slug', 'dwy-upgrade')->first();

        if ($bump) {
            $bump->update([
                'deliverable_type' => BumpProduct::TYPE_GROUP_MEMBERSHIP,
                'deliverable_ref_id' => $group->id,
            ]);

            $this->command?->info("Linked bump 'dwy-upgrade' → group '{$group->slug}' ({$group->id})");
        } else {
            $this->command?->warn("Bump 'dwy-upgrade' not found — run BumpProductSeeder first.");
        }

        $this->command?->info("Seeded group: {$group->slug} (max {$group->max_members} members)");
    }
}
