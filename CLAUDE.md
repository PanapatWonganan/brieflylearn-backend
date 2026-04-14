# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**BrieflyLearn Backend API** — Laravel 12 backend for BrieflyLearn LMS platform targeting AI users and business people.

- **Tech Stack**: Laravel 12 + PHP 8.2+ + MySQL 8.0+ + Filament 3.3 admin panel
- **Monorepo**: This is the backend (`fitness-lms-admin/`). Frontend is at `../fitness-lms/` (Next.js 15)
- **Language**: Thai-first UI/validation messages, English code

## Commands

```bash
php artisan serve --port=8001          # Start dev server (port 8001)
php artisan migrate                    # Run migrations
php artisan migrate:status             # Check migration status
php artisan db:seed                    # Seed default users only (admin + test user)
php artisan db:seed --class=CategorySeeder         # Seed course categories (6 AI categories)
php artisan db:seed --class=CourseSeeder            # Seed courses
php artisan db:seed --class=ExamSeeder              # Seed exam questions
php artisan db:seed --class=BlogPostSeeder          # Seed blog posts
php artisan db:seed --class=WellnessGardenSeeder    # Required for garden plant types, achievements, challenges
php artisan route:list                 # List all routes
php artisan test                       # Run tests (PHPUnit, SQLite :memory:)
composer test                          # Alternative: clears config then runs tests
php artisan make:filament-user         # Create Filament admin user
php artisan tinker                     # REPL for quick DB queries
```

**Important**: `php artisan db:seed` (DatabaseSeeder) only creates 2 users (admin@example.com + test@example.com). Other seeders must be run individually.

### Email Automation Commands
```bash
php artisan emails:onboarding          # Send onboarding email series
php artisan emails:weekly-progress     # Send weekly progress summaries
php artisan emails:inactive-reminders  # Send reminders to inactive users (3, 7, 30 days)
php artisan emails:streak-milestones   # Send streak milestone celebration emails
php artisan emails:drip-sequence       # Send drip marketing emails (storytelling sequence)
```

## Architecture

### Authentication (Custom Token — NOT Sanctum)

1. User logs in via email/password or Google Sign-In (`google/apiclient` ID token verification)
2. Backend generates `api_token` (60-char random string), returns `base64(userId|api_token)`
3. All authenticated requests use `Authorization: Bearer <token>` header
4. Middleware `auth.api` (`ApiTokenAuth.php`) decodes base64, splits by `|`, finds user by UUID, verifies token, checks `token_expires_at`
5. Tokens expire after 30 days
6. Middleware sets `Auth::setUser($user)` and merges `auth_user` into request
7. Activity tracking: `last_active_at` updated every 5 minutes, `updateStreak()` called on each auth

### User Model Quirks

- Uses `password_hash` column (not `password`) — `getAuthPassword()` returns `password_hash`
- `getAuthIdentifierName()` returns `'email'` (not `'id'`)
- `HasUuids` trait for UUID primary keys
- `canAccessPanel()` checks `role === 'admin'` for Filament access
- `getOrCreateGarden()` — auto-creates garden with 100 star seeds on first access
- Onboarding fields: `goals` (JSON array), `interests` (JSON array), `experience_level` (string), `onboarding_completed` (boolean)

### API Response Pattern

All API responses follow this format:
```json
{
  "success": true,
  "message": "string",
  "data": {},
  "errors": {}
}
```
HTTP 401 for auth failures, 422 for validation (Thai message: "ข้อมูลไม่ถูกต้อง").

### Form Request Validation

All API form requests extend `ApiFormRequest` which overrides `failedValidation()` to return JSON (not redirect). Validation messages are in Thai. Phone validation uses Thai format (9-10 digits).

### Route Groups (routes/api.php)

| Group | Prefix | Auth | Throttle |
|-------|--------|------|----------|
| Auth public | `v1/auth` | No | login: 5/min, register: 3/min |
| Auth protected | `v1/auth` | `auth.api` | -- |
| Public API | `v1` | No | 60/min |
| Protected API | `v1` | `auth.api` | 60/min |
| Garden | `v1/garden` | `auth.api` | 60/min |
| Exams (public) | `v1/exams` | No | -- |
| Exams (protected) | `v1/exams` | `auth.api` | -- |
| Blog | `v1/blog` | No | -- |
| Video upload | `video/upload` | `auth.api` | 10/hr |
| Contact | `v1/contact` | No | -- |
| Email unsub | `v1/email/unsubscribe` | No | -- |

Rate limiters defined in `AppServiceProvider::configureRateLimiting()`.

### Backend Structure (app/)

- `Http/Controllers/Api/` — 20 API controllers (incl. EmailSubscriptionController)
- `Http/Middleware/` — ApiTokenAuth (custom token), ForceHttps, TrustProxies
- `Http/Requests/` — 14 FormRequest classes extending `ApiFormRequest` base
- `Models/` — 28 Eloquent models (UUID primary keys, `$incrementing = false`)
- `Mail/` — 16 mail classes (SendGrid SMTP, incl. DripSequenceMail)
- `Console/Commands/` — 7 commands (5 email automation, video status, admin creation)
- `Policies/` — UserGardenPolicy, UserPlantPolicy, EnrollmentPolicy
- `Filament/` — Admin panel at `/admin` (Course, Lesson, User, Garden, Email Marketing resources)
- `Providers/AppServiceProvider.php` — Rate limiting config + event listeners for Garden-Course integration
- `Events/` + `Listeners/` — LessonCompleted/CourseCompleted events trigger Garden XP rewards
- `Services/CourseProgressService.php` — XP calculation, achievement checks, garden reward logic
- `Jobs/ProcessVideoJob.php` — Video processing (direct MP4 copy, no HLS)

### Video Upload & Streaming System

Videos are stored on the **local disk** (`storage/app/private/`). No S3 in use.

**Upload flow** (`VideoUploadController`):
1. Upload validates mp4/mov/avi/webm, max 500MB, stores in `temp-videos/`
2. Creates `Video` model with status `pending`
3. `ProcessVideoJob` runs (synchronous, not queued) — copies MP4 to `videos/{videoId}/`, sets status `ready`
4. No HLS/FFmpeg conversion — direct MP4 streaming

**Streaming flow**:
1. Frontend requests `GET /api/v1/lessons/{id}/stream-url` → returns HMAC-SHA256 signed URL (30-min expiry)
2. Frontend loads `GET /api/video/stream/{videoId}?token=...&expires=...` → validates token, serves with HTTP 206 range requests (8KB chunks)

**Models**: `Video` (status: pending/processing/ready/failed/replaced), `VideoAccessLog` (tracks watch_duration, seek_count, speed_changes, flags suspicious_activity at >50 seeks or >20 speed changes)

**Filament**: `LessonResource` has file upload (max 500MB), manual "Upload Video" action, video status display, video/no-video filter

### Course Integration & Scoring System

Two separate progress endpoints:
- **Basic**: `PUT /api/v1/progress/lessons/{id}` (`ProgressController`) — saves watch_time only, no XP
- **Rewards**: `POST /api/v1/course-integration/lessons/{id}/complete` (`CourseIntegrationController`) — dispatches events, calculates XP, awards achievements

**XP per lesson** (`CourseProgressService::calculateLessonXp`):
```
Base 20 + min(duration_minutes, 60) + category_bonus(5-15)
Star Seeds = 30% of XP
```
Category bonuses: fitness=15, nutrition=12, mental-health=10, pregnancy=15, postpartum=12, hormonal=10, default=5

**XP per course completion** (`calculateCourseCompletionBonus`):
```
Base 100 + (total_lessons * 10) + (duration_weeks * 25)
Star Seeds = 50% of XP
```

**Event chain**: `completeLessonWithRewards()` → creates Enrollment + LessonProgress → fires `LessonCompleted` event → `AwardGardenRewardsForLesson` listener → `CourseProgressService::onLessonCompleted()` → adds XP/Seeds to garden, logs `GardenActivity` → checks course completion → optionally fires `CourseCompleted` event

**Achievement check**: Hard-coded thresholds in `CourseProgressService::checkLearningAchievements()` — 1 lesson = "นักปลูกมือใหม่", 10 lessons = "นักเรียนขยัน", 3 courses = "ปราชญ์แห่งสุขภาพ". Also checked in `CourseIntegrationController::checkAchievementsAfterLessonCompletion()` which uses DB-driven `Achievement::checkCriteria()`.

**Additional routes**:
- `GET /api/v1/course-integration/learning-progress` — garden stats for current user
- `GET /api/v1/course-integration/courses/{id}/rewards-preview` — potential rewards before completing

### Onboarding System

- **Endpoint**: `POST /api/v1/auth/onboarding` — saves goals, interests, experience_level, sets `onboarding_completed = true`
- **Course recommendations**: `GET /api/v1/auth/onboarding/courses` — filters published courses by matching category slugs from user's interests, sorted by experience level match
- **Category slugs used as interest IDs**: ai-fundamentals, ai-business, ai-organization, prompt-engineering, ai-automation, ai-strategy (from CategorySeeder)
- Auto-subscribe to default email drip sequence on registration

### Database

- **UUID primary keys** on all tables (`HasUuids` trait, `$incrementing = false`)
- **Users table**: `password_hash` (not `password`), `full_name`, `api_token`, `google_id`, `token_expires_at`, `onboarding_step`, `goals`, `interests`, `experience_level`, `onboarding_completed`, `current_streak`, `last_streak_date`, `weekly_email_sent_at`, `inactive_email_sent_at`
- **JSON columns**: `growth_stages`, `care_requirements`, `criteria`, `options`, `answers`, `progress_data`, `garden_layout`, `activity_data`, `requirements`, `tags`, `goals`, `interests`
- **Unique constraints**: `[user_id, course_id]` on enrollments, `[user_id, lesson_id]` on lesson_progress, `[user_id, achievement_id]` on user_achievements, `[user_id, challenge_id]` on user_challenge_progress, `[user_id, sequence_id]` on email_sequence_subscriptions

### Email System

- **SendGrid SMTP** via Laravel Mail (config in `.env` MAIL_* variables)
- All mail sends wrapped in try-catch — failures never break the main flow
- Blade templates in `resources/views/emails/` with BrieflyLearn branding (Thai content)
- Base layout: `emails/layout.blade.php`
- **Event-triggered**: Welcome, CourseEnrollment, CourseCompleted, LevelUp, ExamResult, AchievementEarned
- **Scheduled** (routes/console.php, timezone adjusted for Thailand UTC+7):
  - `emails:onboarding` — hourly 08:00-09:00 Thai time
  - `emails:weekly-progress` — Monday 08:00 Thai time
  - `emails:inactive-reminders` — daily 18:00 Thai time
  - `emails:streak-milestones` — daily 08:30 Thai time
  - `emails:drip-sequence` — hourly 09:00-10:00 Thai time
- Production requires cron: `* * * * * php artisan schedule:run`

### Drip Email Marketing (Storytelling Sequence)

Content-driven email sequences managed via Filament admin panel, sent automatically on a per-user schedule.

- **Tables**: `email_sequences` → `email_sequence_steps` (1:N) → `email_sequence_subscriptions` (tracks user progress)
- **Flow**: New user registers → auto-subscribes to default sequence (`is_default = true`) → cron sends steps based on `delay_days` per step → marks `completed` after last step
- **Content in DB**: Subject + body_html stored in `email_sequence_steps`, editable via Filament RichEditor
- **Placeholders**: `{name}`, `{first_name}`, `{email}`, `{app_url}` — replaced at send time by `DripSequenceMail`
- **Unsubscribe**: `GET /api/v1/email/unsubscribe?token=base64(userId|sequenceId)` — public endpoint, renders confirmation page
- **Auto-subscribe hook**: In `AuthController::register()` and `AuthController::googleLogin()` for new users

### Filament Admin Panel

- Path: `/admin`, requires `role === 'admin'`
- Navigation groups (Thai): ผู้ใช้งาน, คอร์สเรียน, Wellness Garden, อีเมลมาร์เก็ตติ้ง, การเงิน, รายงาน, ระบบ
- Garden-specific resources: PlantTypeResource, AchievementResource, DailyChallengeResource, UserGardenResource
- Email marketing resources: EmailSequenceResource (CRUD sequences + inline steps), EmailSequenceSubscriptionResource (subscriber management)

## Environment Variables

Key `.env` variables:
```
APP_URL=http://localhost:8001
APP_FRONTEND_URL=http://localhost:3000     # Used in email templates
DB_CONNECTION=mysql                        # Default .env.example uses sqlite
DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD
MAIL_HOST=smtp.sendgrid.net               # SendGrid SMTP
MAIL_PASSWORD=SG.xxxxx                    # SendGrid API key
GOOGLE_CLIENT_ID=xxx                      # Google OAuth
GOOGLE_CLIENT_SECRET=xxx
```

## Test Credentials

- **Email**: test@example.com / **Password**: password123
- **Admin panel**: /admin (user role must be `admin`, admin@example.com created by DatabaseSeeder)

### Testing

- **Framework**: PHPUnit 11.5 (not Pest)
- **Test DB**: SQLite `:memory:` (configured in phpunit.xml)
- **Test suite is minimal** — only example tests in `tests/Feature/` and `tests/Unit/`
- Run: `php artisan test` or `composer test`

## Known Quirks

1. `GardenActivity::logActivity()` has PHP deprecated warnings for nullable params — cosmetic, not breaking
2. `growth_stages` in PlantType can be `number | Record<string, any>` — handle both in API responses
3. First MySQL request ~500ms (connection overhead), subsequent requests are fast
4. `getAuthIdentifierName()` returns `'email'` — this affects how Laravel Auth identifies the user internally
5. Garden `getOrCreateGarden()` initializes with theme `'tropical'` and 100 star seeds
6. Video processing runs synchronously (not queued) — `ProcessVideoJob` dispatched directly in upload controller
7. `lesson_progress.enrollment_id` is nullable — controller looks up enrollment but it may not exist
8. Two duplicate achievement-check paths: `CourseProgressService::checkLearningAchievements()` (hard-coded thresholds) and `CourseIntegrationController::checkAchievementsAfterLessonCompletion()` (DB-driven) — both may run on the same lesson completion
9. `UpdateLessonProgressRequest` uses `sometimes|boolean` for `is_completed` — frontend interval updates only send `watch_time`
10. `DatabaseSeeder` only creates 2 users — it does NOT call other seeders. Run `CategorySeeder`, `CourseSeeder`, `WellnessGardenSeeder`, etc. individually
