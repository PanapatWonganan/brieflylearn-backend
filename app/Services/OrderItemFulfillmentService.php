<?php

namespace App\Services;

use App\Models\BumpProduct;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Delivers paid bumps after a successful payment.
 *
 * Called from PaymentController::applyPaymentResult() once the parent
 * enrollment is confirmed completed. Walks each undelivered OrderItem and
 * fulfils based on the bump's deliverable_type:
 *
 *   - playbook_course   → create or activate Enrollment for the playbook course
 *   - group_membership  → upsert GroupMember (status=active)
 *   - manual            → mark delivered with note (team contacts buyer)
 */
class OrderItemFulfillmentService
{
    /**
     * Fulfil all undelivered order items attached to an enrollment.
     */
    public function fulfilForEnrollment(Enrollment $enrollment): void
    {
        $items = OrderItem::with('bumpProduct')
            ->where('enrollment_id', $enrollment->id)
            ->whereNull('delivered_at')
            ->get();

        foreach ($items as $item) {
            try {
                $this->fulfilOne($item, $enrollment);
            } catch (\Throwable $e) {
                Log::error('Bump fulfillment failed', [
                    'enrollment_id' => $enrollment->id,
                    'order_item_id' => $item->id,
                    'bump_slug' => $item->bumpProduct?->slug,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function fulfilOne(OrderItem $item, Enrollment $enrollment): void
    {
        $bump = $item->bumpProduct;

        if (! $bump) {
            $this->markDelivered($item, ['error' => 'bump_product_missing']);
            return;
        }

        switch ($bump->deliverable_type) {
            case BumpProduct::TYPE_PLAYBOOK_COURSE:
                $this->fulfilPlaybook($item, $enrollment, $bump);
                break;

            case BumpProduct::TYPE_GROUP_MEMBERSHIP:
                $this->fulfilGroup($item, $enrollment, $bump);
                break;

            case BumpProduct::TYPE_MANUAL:
            default:
                $this->markDelivered($item, [
                    'mode' => 'manual',
                    'note' => 'ทีมงานจะติดต่อผู้ซื้อเพื่อส่งมอบ',
                ]);
                break;
        }
    }

    protected function fulfilPlaybook(OrderItem $item, Enrollment $enrollment, BumpProduct $bump): void
    {
        $courseId = $bump->deliverable_ref_id;
        if (! $courseId) {
            $this->markDelivered($item, ['error' => 'missing_deliverable_ref_id']);
            return;
        }

        DB::transaction(function () use ($item, $enrollment, $courseId, $bump) {
            $playbookEnrollment = Enrollment::firstOrNew([
                'user_id' => $enrollment->user_id,
                'course_id' => $courseId,
            ]);

            $playbookEnrollment->enrolled_at = $playbookEnrollment->enrolled_at ?: now();
            $playbookEnrollment->status = 'active';
            $playbookEnrollment->payment_status = 'completed';
            $playbookEnrollment->payment_date = now();
            $playbookEnrollment->payment_method = 'bump';
            $playbookEnrollment->payment_gateway = 'bump-' . $bump->slug;
            $playbookEnrollment->amount_paid = (float) $item->price_snapshot;
            $playbookEnrollment->save();

            $this->markDelivered($item, [
                'mode' => 'playbook_course',
                'playbook_course_id' => $courseId,
                'playbook_enrollment_id' => $playbookEnrollment->id,
            ]);
        });
    }

    protected function fulfilGroup(OrderItem $item, Enrollment $enrollment, BumpProduct $bump): void
    {
        $groupId = $bump->deliverable_ref_id;
        if (! $groupId) {
            $this->markDelivered($item, ['error' => 'missing_deliverable_ref_id']);
            return;
        }

        $group = Group::find($groupId);
        if (! $group) {
            $this->markDelivered($item, ['error' => 'group_not_found', 'group_id' => $groupId]);
            return;
        }

        DB::transaction(function () use ($item, $enrollment, $group) {
            $member = GroupMember::firstOrNew([
                'group_id' => $group->id,
                'user_id' => $enrollment->user_id,
            ]);

            $member->role = $member->role ?: GroupMember::ROLE_MEMBER;
            $member->status = GroupMember::STATUS_ACTIVE;
            $member->joined_at = $member->joined_at ?: now();
            $member->enrollment_id = $enrollment->id;
            $member->save();

            $this->markDelivered($item, [
                'mode' => 'group_membership',
                'group_id' => $group->id,
                'group_slug' => $group->slug,
                'group_member_id' => $member->id,
            ]);
        });
    }

    protected function markDelivered(OrderItem $item, array $meta): void
    {
        $item->delivered_at = now();
        $item->delivery_meta = $meta;
        $item->save();
    }
}
