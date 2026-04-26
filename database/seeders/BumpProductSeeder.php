<?php

namespace Database\Seeders;

use App\Models\BumpProduct;
use Illuminate\Database\Seeder;

/**
 * Seeds the two bumps currently shown on the AI ฿100M sale + checkout pages.
 *
 * Pricing matches the UI as of Phase 1 (see fitness-lms/src/app/ai-100m/checkout/CheckoutClient.tsx).
 * deliverable_ref_id is intentionally null at this stage:
 *   - press-method-playbook → admin will paste the existing PRESS Method Playbook course UUID in Filament
 *   - dwy-upgrade           → linked to a Group row created in Phase 2
 *
 * `course_id` is left null so these bumps work for the AI ฿100M course (and any other) —
 * Phase 3 will add per-course filtering when the bump list is exposed to the frontend.
 */
class BumpProductSeeder extends Seeder
{
    public function run(): void
    {
        BumpProduct::updateOrCreate(
            ['slug' => 'press-method-playbook'],
            [
                'name' => 'The PRESS Method™ Playbook',
                'description' => 'Check list ที่ผมใช้สร้าง Framework ที่ Mobile App ในไทยโตมากกว่า 600% '
                    . 'ในเวลาแค่ 2 เดือน กับตลาดที่มีการแข่งขันเข้มข้นที่สุดจนทยานขึ้นไปทำยอดดาวน์โหลดได้อันดับ 1',
                'price' => 1990.00,
                'original_price' => 3900.00,
                'deliverable_type' => BumpProduct::TYPE_PLAYBOOK_COURSE,
                'deliverable_ref_id' => null, // admin จะใส่ UUID ของ playbook PRESS Method ทีหลัง
                'course_id' => null,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        BumpProduct::updateOrCreate(
            ['slug' => 'dwy-upgrade'],
            [
                'name' => 'Done-With-You Upgrade',
                'description' => 'Group Coaching 6 ครั้ง ที่ผมรีวิว Offer · Funnel · Pitch ของคุณเองโดยตรง '
                    . 'และแก้ให้สดๆ ในคลาส (รับแค่ 12 คน/รุ่น)',
                'price' => 4900.00,
                'original_price' => 29000.00,
                'deliverable_type' => BumpProduct::TYPE_GROUP_MEMBERSHIP,
                'deliverable_ref_id' => null, // จะ link หลัง Phase 2 สร้าง group
                'course_id' => null,
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $this->command?->info('Seeded 2 bump products: press-method-playbook, dwy-upgrade');
    }
}
