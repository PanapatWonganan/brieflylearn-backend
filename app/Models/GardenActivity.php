<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class GardenActivity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'garden_id',
        'activity_type',
        'target_type',
        'target_id',
        'xp_earned',
        'star_seeds_earned',
        'activity_data',
    ];

    protected $casts = [
        'activity_data' => 'array',
    ];

    // ความสัมพันธ์กับ User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ความสัมพันธ์กับ UserGarden
    public function garden(): BelongsTo
    {
        return $this->belongsTo(UserGarden::class, 'garden_id');
    }

    // ความสัมพันธ์ polymorphic กับ target (อาจจะเป็น plant หรือ garden)
    public function target()
    {
        return $this->morphTo('target', 'target_type', 'target_id');
    }

    // Scope สำหรับกรองตาม activity type
    public function scopeByType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    // Scope สำหรับกิจกรรมล่าสุด
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
                     ->orderBy('created_at', 'desc');
    }

    // Scope สำหรับกิจกรรมของผู้ใช้คนหนึ่ง
    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope สำหรับกิจกรรมในสวนหนึ่ง
    public function scopeForGarden($query, string $gardenId)
    {
        return $query->where('garden_id', $gardenId);
    }

    // Scope สำหรับกิจกรรมวันนี้
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ข้อความอธิบายกิจกรรม
    public function getDescriptionAttribute(): string
    {
        $data = $this->activity_data ?? [];

        return match($this->activity_type) {
            'water' => $this->target_type === 'plant'
                ? "รดน้ำพืช " . ($data['plant_name'] ?? '')
                : "รดน้ำสวน",
            'plant' => "ปลูก " . ($data['plant_name'] ?? ''),
            'harvest' => "เก็บเกี่ยว " . ($data['plant_name'] ?? ''),
            'grow' => "พืช " . ($data['plant_name'] ?? '') . " เติบโตเป็น " . ($data['stage_name'] ?? ''),
            'fertilize' => "ใส่ปุ่ยให้ " . ($data['plant_name'] ?? ''),
            'lesson_completed' => "เรียนจบ: " . ($data['lesson_title'] ?? ''),
            'course_completed' => "จบคอร์ส: " . ($data['course_title'] ?? ''),
            default => "กิจกรรมในสวน"
        };
    }

    // ไอคอนสำหรับแต่ละประเภทกิจกรรม
    public function getIconAttribute(): string
    {
        return match($this->activity_type) {
            'water' => '💧',
            'plant' => '🌱',
            'harvest' => '🌾',
            'grow' => '🌿',
            'fertilize' => '🌻',
            'lesson_completed' => '📚',
            'course_completed' => '🎓',
            default => '🌳'
        };
    }

    // สีสำหรับแต่ละประเภทกิจกรรม
    public function getColorAttribute(): string
    {
        return match($this->activity_type) {
            'water' => 'blue',
            'plant' => 'green',
            'harvest' => 'yellow',
            'grow' => 'emerald',
            'fertilize' => 'purple',
            'lesson_completed' => 'indigo',
            'course_completed' => 'pink',
            default => 'gray'
        };
    }

    // เวลาที่ผ่านมา
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    // สรุปรางวัลที่ได้รับ
    public function getRewardSummaryAttribute(): string
    {
        $rewards = [];
        
        if ($this->xp_earned > 0) {
            $rewards[] = "+{$this->xp_earned} XP";
        }
        
        if ($this->star_seeds_earned > 0) {
            $rewards[] = "+{$this->star_seeds_earned} ⭐";
        }
        
        return implode(', ', $rewards);
    }

    // บันทึกกิจกรรมใหม่
    public static function logActivity(
        string $userId,
        string $gardenId,
        string $activityType,
        ?string $targetType = null,
        ?string $targetId = null,
        int $xpEarned = 0,
        int $starSeedsEarned = 0,
        array $activityData = []
    ): self {
        return self::create([
            'user_id' => $userId,
            'garden_id' => $gardenId,
            'activity_type' => $activityType,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'xp_earned' => $xpEarned,
            'star_seeds_earned' => $starSeedsEarned,
            'activity_data' => $activityData
        ]);
    }

    // สถิติกิจกรรมรายวัน
    public static function getDailyStats(string $userId, int $days = 7): array
    {
        $activities = self::forUser($userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, activity_type, COUNT(*) as count, SUM(xp_earned) as total_xp')
            ->groupBy('date', 'activity_type')
            ->get();

        return $activities->groupBy('date')->map(function ($dayActivities) {
            return [
                'activities' => $dayActivities->groupBy('activity_type'),
                'total_xp' => $dayActivities->sum('total_xp'),
                'total_activities' => $dayActivities->sum('count')
            ];
        })->toArray();
    }
}