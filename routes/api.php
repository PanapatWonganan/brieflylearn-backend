<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VideoUploadController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\GardenController;
use App\Http\Controllers\Api\AchievementController;
use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\CourseIntegrationController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\GardenThemeController;
use App\Http\Controllers\Api\CommunityGardenController;
use App\Http\Controllers\Api\AdvancedPlantController;
use App\Http\Controllers\Api\SeasonalEventController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', function (Request $request) {
    return response()->json(['message' => 'User endpoint working']);
});

// Authentication routes
Route::prefix('v1/auth')->group(function () {
    // Public routes (no auth required)
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register'])->middleware('throttle:register');
    Route::post('/guest-signup', [App\Http\Controllers\Api\AuthController::class, 'guestSignup'])->middleware('throttle:register');
    Route::post('/google-login', [App\Http\Controllers\Api\AuthController::class, 'googleLogin']);

    // Password reset routes (public)
    Route::post('/forgot-password', [App\Http\Controllers\Api\PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [App\Http\Controllers\Api\PasswordResetController::class, 'resetPassword']);

    // Protected routes (require auth)
    Route::middleware('auth.api')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'profile']);
        Route::post('/onboarding', [App\Http\Controllers\Api\AuthController::class, 'submitOnboarding']);
        Route::get('/onboarding/courses', [App\Http\Controllers\Api\AuthController::class, 'getOnboardingCourses']);
    });
});

// Public API routes (no authentication required)
Route::prefix('v1')->middleware('throttle:api')->group(function () {
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'BoostMe Admin API',
            'version' => '1.0.6-restored'
        ]);
    });

    // Course and Lesson routes (public)
    Route::get('/courses', [LessonController::class, 'getCourses']);
    Route::get('/courses/{courseId}/lessons', [LessonController::class, 'getCourseLessons']);
    Route::get('/lessons/{lessonId}', [LessonController::class, 'show']);
});

// Protected API routes (require authentication)
Route::prefix('v1')->middleware(['auth.api', 'throttle:api'])->group(function () {

    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/stats', [UserController::class, 'stats']);
        Route::get('/{id}', [UserController::class, 'show']);
    });

    // Lesson streaming (protected)
    Route::get('/lessons/{lessonId}/stream-url', [LessonController::class, 'getStreamUrl']);

    // Enrollment routes
    Route::get('/enrollments/my', [EnrollmentController::class, 'getMyEnrollments']);
    Route::post('/enrollments', [EnrollmentController::class, 'enrollInCourse']);

    // Progress tracking routes
    Route::get('/progress/my-summary', [ProgressController::class, 'getMySummary']);
    Route::put('/progress/lessons/{lessonId}', [ProgressController::class, 'updateLessonProgress']);
    Route::get('/progress/courses/{courseId}', [ProgressController::class, 'getCourseProgress']);

    // Wellness Garden API Routes
    Route::prefix('garden')->group(function () {

        // Garden management
        Route::get('/my-garden', [GardenController::class, 'index']);
        Route::put('/water-garden', [GardenController::class, 'waterGarden']);

        // Plant management
        Route::get('/plant-types', [GardenController::class, 'getPlantTypes']);
        Route::post('/plant/{plantTypeId}', [GardenController::class, 'plantSeed']);
        Route::put('/plants/{userPlantId}/water', [GardenController::class, 'waterPlant']);
        Route::post('/plants/{userPlantId}/harvest', [GardenController::class, 'harvestPlant']);

        // Achievements
        Route::get('/achievements', [AchievementController::class, 'index']);
        Route::get('/achievements/category/{category}', [AchievementController::class, 'getByCategory']);
        Route::post('/achievements/check', [AchievementController::class, 'checkAchievements']);
        Route::get('/achievements/my-achievements', [AchievementController::class, 'getUserAchievements']);

        // Daily Challenges
        Route::get('/challenges/today', [ChallengeController::class, 'getTodayChallenges']);
        Route::post('/challenges/create-sample', [ChallengeController::class, 'createSampleChallenges']);
        Route::get('/challenges/history', [ChallengeController::class, 'getChallengeHistory']);
        Route::put('/challenges/{challengeId}/progress', [ChallengeController::class, 'updateProgress']);
        Route::get('/challenges/leaderboard', [ChallengeController::class, 'getLeaderboard']);

        // Friend System
        Route::prefix('friends')->group(function () {
            Route::get('/', [FriendController::class, 'getFriendsList']);
            Route::get('/pending', [FriendController::class, 'getPendingRequests']);
            Route::get('/search', [FriendController::class, 'searchUsers']);
            Route::post('/request', [FriendController::class, 'sendFriendRequest']);
            Route::put('/accept/{requestId}', [FriendController::class, 'acceptFriendRequest']);
            Route::delete('/reject/{requestId}', [FriendController::class, 'rejectFriendRequest']);
            Route::delete('/remove/{friendId}', [FriendController::class, 'removeFriend']);
            Route::get('/{friendId}/garden', [FriendController::class, 'visitFriendGarden']);
            Route::post('/{friendId}/plants/{plantId}/water', [FriendController::class, 'helpWaterPlant']);
        });

        // Garden Themes
        Route::prefix('themes')->group(function () {
            Route::get('/', [GardenThemeController::class, 'getAvailableThemes']);
            Route::get('/current', [GardenThemeController::class, 'getCurrentTheme']);
            Route::post('/apply', [GardenThemeController::class, 'applyTheme']);
        });

        // Community Garden
        Route::prefix('community')->group(function () {
            Route::get('/', [CommunityGardenController::class, 'getCommunityOverview']);
            Route::get('/leaderboard', [CommunityGardenController::class, 'getCommunityLeaderboard']);
            Route::get('/gardens/{gardenId}', [CommunityGardenController::class, 'visitPublicGarden']);
            Route::post('/gardens/{gardenId}/like', [CommunityGardenController::class, 'likeGarden']);
            Route::post('/gardens/{gardenId}/plants/{plantId}/water', [CommunityGardenController::class, 'waterPublicPlant']);
            Route::post('/projects/{projectId}/join', [CommunityGardenController::class, 'joinCommunityProject']);
        });

        // Advanced Plants
        Route::prefix('advanced-plants')->group(function () {
            Route::get('/{plantId}/abilities', [AdvancedPlantController::class, 'getPlantAbilities']);
            Route::post('/{plantId}/activate-ability', [AdvancedPlantController::class, 'activateAbility']);
            Route::post('/{plantId}/evolve', [AdvancedPlantController::class, 'evolvePlant']);
            Route::post('/breed', [AdvancedPlantController::class, 'breedPlants']);
        });

        // Seasonal Events
        Route::prefix('seasonal')->group(function () {
            Route::get('/events', [SeasonalEventController::class, 'getCurrentEvents']);
            Route::get('/weather', [SeasonalEventController::class, 'getWeatherStatus']);
            Route::post('/events/{eventId}/participate', [SeasonalEventController::class, 'participateEvent']);
            Route::post('/plants/activate', [SeasonalEventController::class, 'activateSeasonalPlant']);
        });
    });

    // Course Integration with Garden
    Route::prefix('course-integration')->group(function () {
        Route::post('/lessons/{lessonId}/complete', [CourseIntegrationController::class, 'completeLessonWithRewards']);
        Route::get('/learning-progress', [CourseIntegrationController::class, 'getLearningProgress']);
        Route::get('/courses/{courseId}/rewards-preview', [CourseIntegrationController::class, 'getCourseRewardsPreview']);
    });
});

// Video-related routes
Route::prefix('video')->group(function () {
    // Public routes for video streaming (with token validation)
    Route::get('/stream/{video}', [VideoUploadController::class, 'stream'])->name('api.video.stream');
    Route::get('/status/{video}', [VideoUploadController::class, 'status']);

    // Protected routes
    Route::middleware('auth.api')->group(function () {
        Route::post('/upload', [VideoUploadController::class, 'upload'])->middleware('throttle:upload');
        Route::get('/{video}/generate-url', [VideoUploadController::class, 'generateStreamUrl']);
        Route::put('/access-log/{log}', [VideoUploadController::class, 'updateAccessLog']);
    });
});

// Exam routes
Route::prefix('v1')->group(function () {
    Route::get('/exams', [ExamController::class, 'index']);
    Route::get('/exams/{id}', [ExamController::class, 'show']);

    // Protected exam routes (require authentication)
    Route::middleware('auth.api')->group(function () {
        Route::get('/exams/results', [ExamController::class, 'results']);
        Route::get('/exams/results/{id}', [ExamController::class, 'resultDetail']);
        Route::post('/exams/{id}/start', [ExamController::class, 'start']);
        Route::post('/exams/{id}/submit', [ExamController::class, 'submit']);
    });
});

// Blog routes
Route::prefix('v1/blog')->group(function () {
    Route::get('/posts', [BlogController::class, 'index']);
    Route::get('/posts/{slug}', [BlogController::class, 'show']);
    Route::put('/posts/{id}/view', [BlogController::class, 'incrementViews']);
});

// Contact route
Route::post('/v1/contact', [ContactController::class, 'store']);

// Email subscription routes (public — accessed from email links)
Route::get('/v1/email/unsubscribe', [App\Http\Controllers\Api\EmailSubscriptionController::class, 'unsubscribe']);

// Payment routes (Pay Solutions / ThaiePay)
Route::prefix('v1/payments/paysolutions')->group(function () {
    // Public — gateway callback + browser return
    Route::post('/postback', [PaymentController::class, 'postback']);
    Route::match(['get', 'post'], '/return', [PaymentController::class, 'return']);

    // Authenticated — checkout + status poll
    Route::middleware('auth.api')->group(function () {
        Route::post('/checkout', [PaymentController::class, 'checkout']);
        Route::get('/status', [PaymentController::class, 'status']);
    });
});
