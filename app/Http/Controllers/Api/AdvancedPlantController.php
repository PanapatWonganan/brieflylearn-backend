<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserGarden;
use App\Models\UserPlant;
use App\Models\PlantType;
use App\Models\GardenActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdvancedPlantController extends Controller
{
    /**
     * Get current user ID from authenticated request
     */
    private function getCurrentUserId(Request $request): ?string
    {
        $user = Auth::user() ?? $request->auth_user;
        return $user ? $user->id : null;
    }

    /**
     * Get plant special abilities
     */
    public function getPlantAbilities(Request $request, string $plantId): JsonResponse
    {
        try {
            $userId = $this->getCurrentUserId($request);
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            $plant = UserPlant::where('id', $plantId)
                            ->where('user_id', $userId)
                            ->with('plantType')
                            ->first();

            if (!$plant) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบพืชที่ระบุ'
                ], 404);
            }

            // Define special abilities based on plant type and stage
            $abilities = $this->calculatePlantAbilities($plant);

            return response()->json([
                'success' => true,
                'data' => [
                    'plant_id' => $plant->id,
                    'plant_name' => $plant->getDisplayName(),
                    'plant_type' => $plant->plantType->name,
                    'current_stage' => $plant->stage,
                    'abilities' => $abilities,
                    'next_evolution' => $this->getNextEvolution($plant),
                    'breeding_potential' => $this->getBreedingPotential($plant)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลความสามารถพืชได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate plant special ability
     */
    public function activateAbility(Request $request, string $plantId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'ability_type' => 'required|string|in:xp_boost,star_seeds_boost,garden_boost,friend_boost,healing',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ถูกต้อง',
                    'errors' => $validator->errors()
                ], 400);
            }

            $userId = $this->getCurrentUserId($request);
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $plant = UserPlant::where('id', $plantId)
                            ->where('user_id', $userId)
                            ->with('plantType')
                            ->first();

            if (!$plant) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบพืชที่ระบุ'
                ], 404);
            }

            $abilityType = $request->ability_type;
            $abilities = $this->calculatePlantAbilities($plant);
            $selectedAbility = collect($abilities)->firstWhere('type', $abilityType);

            if (!$selectedAbility || !$selectedAbility['available']) {
                return response()->json([
                    'success' => false,
                    'message' => 'ความสามารถนี้ไม่สามารถใช้ได้ในขณะนี้'
                ], 400);
            }

            // Check cooldown
            $lastUsed = $plant->metadata['abilities'][$abilityType]['last_used'] ?? null;
            if ($lastUsed && now()->diffInHours($lastUsed) < $selectedAbility['cooldown_hours']) {
                $remainingHours = $selectedAbility['cooldown_hours'] - now()->diffInHours($lastUsed);
                return response()->json([
                    'success' => false,
                    'message' => "ต้องรอ {$remainingHours} ชั่วโมงก่อนใช้ความสามารถนี้อีกครั้ง"
                ], 400);
            }

            // Apply ability effects
            $effects = $this->applyAbilityEffects($plant, $abilityType, $selectedAbility);

            // Update plant metadata
            $metadata = $plant->metadata ?? [];
            $metadata['abilities'][$abilityType]['last_used'] = now()->toISOString();
            $metadata['abilities'][$abilityType]['times_used'] = ($metadata['abilities'][$abilityType]['times_used'] ?? 0) + 1;
            $plant->metadata = $metadata;
            $plant->save();

            // Log activity
            GardenActivity::create([
                'user_id' => $userId,
                'garden_id' => $plant->garden_id,
                'activity_type' => 'ability_used',
                'target_type' => 'plant',
                'target_id' => $plant->id,
                'xp_earned' => 0,
                'description' => "ใช้ความสามารถ {$selectedAbility['name']} ของพืช {$plant->getDisplayName()}",
                'metadata' => json_encode([
                    'ability_type' => $abilityType,
                    'ability_name' => $selectedAbility['name'],
                    'effects' => $effects
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => "ใช้ความสามารถ {$selectedAbility['name']} สำเร็จ!",
                'data' => [
                    'ability_used' => $selectedAbility,
                    'effects' => $effects,
                    'cooldown_until' => now()->addHours($selectedAbility['cooldown_hours'])->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถใช้ความสามารถได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attempt plant evolution
     */
    public function evolvePlant(Request $request, string $plantId): JsonResponse
    {
        try {
            $userId = $this->getCurrentUserId($request);
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            $plant = UserPlant::where('id', $plantId)
                            ->where('user_id', $userId)
                            ->with('plantType')
                            ->first();

            if (!$plant) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบพืชที่ระบุ'
                ], 404);
            }

            $garden = UserGarden::where('user_id', $userId)->first();
            if (!$garden) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบสวนของผู้ใช้'
                ], 404);
            }

            // Check evolution requirements
            $evolutionRequirements = $this->getEvolutionRequirements($plant);
            if (!$evolutionRequirements['can_evolve']) {
                return response()->json([
                    'success' => false,
                    'message' => $evolutionRequirements['reason']
                ], 400);
            }

            // Perform evolution
            $evolutionResult = $this->performEvolution($plant, $garden);

            return response()->json([
                'success' => true,
                'message' => "พืช {$plant->getDisplayName()} วิวัฒนาการสำเร็จ!",
                'data' => $evolutionResult
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถวิวัฒนาการพืชได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start plant breeding process
     */
    public function breedPlants(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'parent1_id' => 'required|string',
                'parent2_id' => 'required|string',
                'breeding_type' => 'required|string|in:trait_mix,color_variant,size_variant,special_hybrid'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ถูกต้อง',
                    'errors' => $validator->errors()
                ], 400);
            }

            $userId = $this->getCurrentUserId($request);
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $parent1 = UserPlant::where('id', $request->parent1_id)->where('user_id', $userId)->first();
            $parent2 = UserPlant::where('id', $request->parent2_id)->where('user_id', $userId)->first();

            if (!$parent1 || !$parent2) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบพืชพ่อแม่พันธุ์ที่ระบุ'
                ], 404);
            }

            // Check breeding compatibility
            $compatibility = $this->checkBreedingCompatibility($parent1, $parent2);
            if (!$compatibility['compatible']) {
                return response()->json([
                    'success' => false,
                    'message' => $compatibility['reason']
                ], 400);
            }

            // Start breeding process
            $breedingResult = $this->startBreeding($parent1, $parent2, $request->breeding_type);

            return response()->json([
                'success' => true,
                'message' => 'เริ่มการผสมพันธุ์สำเร็จ!',
                'data' => $breedingResult
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถผสมพันธุ์ได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate plant abilities based on type and stage
     */
    private function calculatePlantAbilities(UserPlant $plant): array
    {
        $abilities = [];
        $plantType = $plant->plantType->name;
        $stage = $plant->stage;

        // Base abilities available at different stages
        if ($stage >= 2) { // Sapling stage
            $abilities[] = [
                'type' => 'xp_boost',
                'name' => 'เร่งการเรียนรู้',
                'description' => 'เพิ่ม XP ที่ได้รับจากการเรียน 25% เป็นเวลา 2 ชั่วโมง',
                'boost_percentage' => 25,
                'duration_hours' => 2,
                'cooldown_hours' => 24,
                'available' => true,
                'icon' => '📚'
            ];
        }

        if ($stage >= 3) { // Pre-bloom stage
            $abilities[] = [
                'type' => 'star_seeds_boost',
                'name' => 'ดาวเพิ่มพูน',
                'description' => 'เพิ่ม Star Seeds ที่ได้รับ 50% เป็นเวลา 1 ชั่วโมง',
                'boost_percentage' => 50,
                'duration_hours' => 1,
                'cooldown_hours' => 12,
                'available' => true,
                'icon' => '⭐'
            ];
        }

        if ($stage >= 4) { // Mature stage
            // Type-specific abilities
            switch ($plant->plantType->category) {
                case 'fitness':
                    $abilities[] = [
                        'type' => 'garden_boost',
                        'name' => 'พลังแห่งการออกกำลังกาย',
                        'description' => 'เพิ่มประสิทธิภาพการรดน้ำทั้งสวน 100%',
                        'boost_percentage' => 100,
                        'duration_hours' => 3,
                        'cooldown_hours' => 48,
                        'available' => true,
                        'icon' => '💪'
                    ];
                    break;

                case 'mental':
                    $abilities[] = [
                        'type' => 'healing',
                        'name' => 'การบำบัดจิตใจ',
                        'description' => 'ฟื้นฟูสุขภาพพืชทั้งสวนให้เต็ม 100%',
                        'boost_percentage' => 100,
                        'duration_hours' => 0,
                        'cooldown_hours' => 72,
                        'available' => true,
                        'icon' => '💚'
                    ];
                    break;

                case 'nutrition':
                    $abilities[] = [
                        'type' => 'friend_boost',
                        'name' => 'พลังแห่งมิตรภาพ',
                        'description' => 'เพิ่มรางวัลจากการช่วยเพื่อน 200%',
                        'boost_percentage' => 200,
                        'duration_hours' => 4,
                        'cooldown_hours' => 36,
                        'available' => true,
                        'icon' => '🤝'
                    ];
                    break;

                case 'learning':
                    $abilities[] = [
                        'type' => 'xp_boost',
                        'name' => 'ปัญญาแห่งความรู้',
                        'description' => 'เพิ่ม XP ทุกกิจกรรม 75% เป็นเวลา 6 ชั่วโมง',
                        'boost_percentage' => 75,
                        'duration_hours' => 6,
                        'cooldown_hours' => 48,
                        'available' => true,
                        'icon' => '🧠'
                    ];
                    break;
            }
        }

        return $abilities;
    }

    /**
     * Get next evolution possibilities
     */
    private function getNextEvolution(UserPlant $plant): array
    {
        if ($plant->stage < 4) {
            return [
                'available' => false,
                'reason' => 'พืชต้องโตเต็มที่ (Stage 4) ก่อนจึงจะวิวัฒนาการได้'
            ];
        }

        return [
            'available' => true,
            'evolutions' => [
                [
                    'type' => 'golden_variant',
                    'name' => $plant->plantType->name . 'ทอง',
                    'description' => 'รุ่นพิเศษสีทองที่ให้รางวัลเพิ่มขึ้น 50%',
                    'requirements' => [
                        'level' => 10,
                        'star_seeds' => 500,
                        'days_mature' => 7
                    ]
                ],
                [
                    'type' => 'rainbow_variant',
                    'name' => $plant->plantType->name . 'รุ้ง',
                    'description' => 'รุ่นสีรุ้งหายากที่มีความสามารถทุกประเภท',
                    'requirements' => [
                        'level' => 20,
                        'star_seeds' => 1000,
                        'friend_help' => 10
                    ]
                ]
            ]
        ];
    }

    /**
     * Get breeding potential
     */
    private function getBreedingPotential(UserPlant $plant): array
    {
        if ($plant->stage < 3) {
            return [
                'can_breed' => false,
                'reason' => 'พืชต้องอยู่ในระยะก่อนบาน (Stage 3) ขึ้นไปจึงจะผสมพันธุ์ได้'
            ];
        }

        return [
            'can_breed' => true,
            'breeding_value' => $this->calculateBreedingValue($plant),
            'rare_traits' => $this->getPlantRareTraits($plant),
            'compatible_types' => $this->getCompatibleBreedingTypes($plant)
        ];
    }

    /**
     * Apply ability effects
     */
    private function applyAbilityEffects(UserPlant $plant, string $abilityType, array $ability): array
    {
        $effects = [];

        switch ($abilityType) {
            case 'healing':
                // Heal all plants in garden to 100%
                $healedPlants = UserPlant::where('garden_id', $plant->garden_id)
                                        ->where('health', '<', 100)
                                        ->update(['health' => 100]);
                $effects['plants_healed'] = $healedPlants;
                break;

            default:
                // For boost abilities, effects are applied during actual activities
                $effects['boost_applied'] = true;
                $effects['expires_at'] = now()->addHours($ability['duration_hours'])->toISOString();
                break;
        }

        return $effects;
    }

    /**
     * Helper methods for evolution and breeding
     */
    private function getEvolutionRequirements(UserPlant $plant): array
    {
        // Implementation for evolution requirements check
        return ['can_evolve' => false, 'reason' => 'ฟีเจอร์วิวัฒนาการกำลังพัฒนา'];
    }

    private function performEvolution(UserPlant $plant, UserGarden $garden): array
    {
        // Implementation for evolution process
        return ['evolution_complete' => true];
    }

    private function checkBreedingCompatibility(UserPlant $parent1, UserPlant $parent2): array
    {
        // Implementation for breeding compatibility check
        return ['compatible' => false, 'reason' => 'ฟีเจอร์ผสมพันธุ์กำลังพัฒนา'];
    }

    private function startBreeding(UserPlant $parent1, UserPlant $parent2, string $breedingType): array
    {
        // Implementation for breeding process
        return ['breeding_started' => true];
    }

    private function calculateBreedingValue(UserPlant $plant): int
    {
        return $plant->stage * 10 + $plant->health;
    }

    private function getPlantRareTraits(UserPlant $plant): array
    {
        return ['common_trait', 'healthy_growth'];
    }

    private function getCompatibleBreedingTypes(UserPlant $plant): array
    {
        return [$plant->plantType->category];
    }
}