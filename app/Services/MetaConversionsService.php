<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MetaConversionsService
{
    private string $pixelId;
    private string $accessToken;
    private string $apiVersion;
    private ?string $testEventCode;

    public function __construct()
    {
        $this->pixelId = config('services.meta.pixel_id') ?? '';
        $this->accessToken = config('services.meta.access_token') ?? '';
        $this->apiVersion = config('services.meta.api_version') ?? 'v25.0';
        $this->testEventCode = config('services.meta.test_event_code');
    }

    /**
     * Check if Meta CAPI is configured and enabled
     */
    public function isEnabled(): bool
    {
        return !empty($this->pixelId) && !empty($this->accessToken);
    }

    /**
     * Send an event to Meta Conversions API
     *
     * @param string $eventName Standard or custom event name
     * @param array $userData Hashed user data (em, ph, fn, etc.)
     * @param array $customData Event-specific custom data
     * @param string|null $eventId UUID for deduplication with Pixel
     * @param string|null $sourceUrl The URL where the event occurred
     */
    public function sendEvent(
        string $eventName,
        array $userData,
        array $customData = [],
        ?string $eventId = null,
        ?string $sourceUrl = null
    ): void {
        if (!$this->isEnabled()) {
            return;
        }

        $eventPayload = [
            'event_name' => $eventName,
            'event_time' => time(),
            'event_id' => $eventId ?? Str::uuid()->toString(),
            'action_source' => 'website',
            'user_data' => $userData,
        ];

        if (!empty($customData)) {
            $eventPayload['custom_data'] = $customData;
        }

        if ($sourceUrl) {
            $eventPayload['event_source_url'] = $sourceUrl;
        }

        $requestBody = [
            'data' => [$eventPayload],
            'access_token' => $this->accessToken,
        ];

        if (!empty($this->testEventCode)) {
            $requestBody['test_event_code'] = $this->testEventCode;
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->pixelId}/events";

        try {
            $response = Http::timeout(5)->post($url, $requestBody);

            if (!$response->successful()) {
                Log::warning('Meta CAPI: API returned error', [
                    'event' => $eventName,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Meta CAPI: HTTP request failed', [
                'event' => $eventName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build user_data array from User model and Request
     *
     * @param User $user The authenticated user
     * @param Request $request The current HTTP request
     * @return array Formatted user_data for Meta CAPI
     */
    public function buildUserData(User $user, Request $request): array
    {
        $userData = [
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
        ];

        if ($user->email) {
            $userData['em'] = [$this->hashValue($user->email)];
        }

        if ($user->phone) {
            $userData['ph'] = [$this->hashValue(preg_replace('/[^0-9]/', '', $user->phone))];
        }

        if ($user->full_name) {
            // Split full name into first and last for better match quality
            $nameParts = explode(' ', $user->full_name, 2);
            $userData['fn'] = [$this->hashValue($nameParts[0])];
            if (isset($nameParts[1])) {
                $userData['ln'] = [$this->hashValue($nameParts[1])];
            }
        }

        // Facebook click ID and browser ID from cookies
        $fbc = $request->cookie('_fbc');
        if ($fbc) {
            $userData['fbc'] = $fbc;
        }

        $fbp = $request->cookie('_fbp');
        if ($fbp) {
            $userData['fbp'] = $fbp;
        }

        // External ID (user's UUID) for cross-device matching
        $userData['external_id'] = [$this->hashValue($user->id)];

        return $userData;
    }

    /**
     * Hash a value using SHA-256 as required by Meta
     * Values are lowercased and trimmed before hashing
     */
    private function hashValue(string $value): string
    {
        return hash('sha256', strtolower(trim($value)));
    }

    /**
     * Convenience: Send CompleteRegistration event
     */
    public function trackRegistration(User $user, Request $request, ?string $eventId = null, string $method = 'email'): void
    {
        $this->sendEvent(
            eventName: 'CompleteRegistration',
            userData: $this->buildUserData($user, $request),
            customData: [
                'value' => 0,
                'currency' => 'THB',
                'content_name' => $method . '_registration',
                'status' => 'complete',
            ],
            eventId: $eventId,
            sourceUrl: config('app.frontend_url') . '/auth'
        );
    }

    /**
     * Convenience: Send AddToCart event (course enrollment)
     */
    public function trackEnrollment(User $user, Request $request, $course, ?string $eventId = null): void
    {
        $this->sendEvent(
            eventName: 'AddToCart',
            userData: $this->buildUserData($user, $request),
            customData: [
                'content_type' => 'product',
                'content_ids' => [$course->id],
                'content_name' => $course->title,
                'value' => $course->price ?? 0,
                'currency' => 'THB',
                'content_category' => $course->category ?? 'course',
            ],
            eventId: $eventId,
            sourceUrl: config('app.frontend_url') . '/courses/' . $course->id
        );
    }

    /**
     * Convenience: Send Purchase event (course completion)
     */
    public function trackCourseCompletion(User $user, Request $request, $course, ?string $eventId = null): void
    {
        $this->sendEvent(
            eventName: 'Purchase',
            userData: $this->buildUserData($user, $request),
            customData: [
                'content_type' => 'product',
                'content_ids' => [$course->id],
                'content_name' => $course->title,
                'value' => $course->price ?? 0,
                'currency' => 'THB',
                'num_items' => 1,
            ],
            eventId: $eventId,
            sourceUrl: config('app.frontend_url') . '/courses/' . $course->id
        );
    }

    /**
     * Convenience: Send custom LessonComplete event
     */
    public function trackLessonComplete(User $user, Request $request, $lesson, array $rewards = [], ?string $eventId = null): void
    {
        $this->sendEvent(
            eventName: 'LessonComplete',
            userData: $this->buildUserData($user, $request),
            customData: array_filter([
                'lesson_id' => $lesson->id,
                'lesson_title' => $lesson->title,
                'course_id' => $lesson->course_id ?? $lesson->course?->id,
                'xp_earned' => $rewards['xp'] ?? null,
                'star_seeds_earned' => $rewards['star_seeds'] ?? null,
            ]),
            eventId: $eventId,
            sourceUrl: config('app.frontend_url') . '/courses/' . ($lesson->course_id ?? $lesson->course?->id)
        );
    }
}
