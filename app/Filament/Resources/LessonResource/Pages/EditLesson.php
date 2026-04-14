<?php

namespace App\Filament\Resources\LessonResource\Pages;

use App\Filament\Resources\LessonResource;
use App\Jobs\ProcessVideoJob;
use App\Models\Video;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Get raw form state which contains the actual uploaded file path
        $rawData = $this->form->getRawState();
        $rawVideoUpload = $rawData['video_upload'] ?? null;

        \Log::info('EditLesson afterSave()', [
            'lesson_id' => $this->record->id,
            'raw_video_upload' => $rawVideoUpload,
        ]);

        if ($rawVideoUpload && !empty($rawVideoUpload)) {
            try {
                $filePath = null;

                if (is_array($rawVideoUpload)) {
                    // Filament FileUpload returns array like {uuid: "temp-videos/filename.mp4"}
                    foreach ($rawVideoUpload as $file) {
                        if (!empty($file) && is_string($file)) {
                            $filePath = $file;
                            break;
                        }
                    }
                } elseif (is_string($rawVideoUpload)) {
                    $filePath = $rawVideoUpload;
                }

                if ($filePath) {
                    \Log::info('Processing video upload', ['file_path' => $filePath]);
                    $this->processVideoUpload($filePath);
                }
            } catch (\Exception $e) {
                \Log::error('Error processing video upload: ' . $e->getMessage());
                Notification::make()->title('Error processing video upload: ' . $e->getMessage())->danger()->send();
            }
        }
    }

    protected function processVideoUpload(string $tempPath): void
    {
        // Local disk root is storage/app/private/ in Laravel 12
        $filePath = storage_path('app/private/' . $tempPath);
        if (!file_exists($filePath)) {
            // Fallback to storage/app/ for backward compat
            $filePath = storage_path('app/' . $tempPath);
        }

        if (file_exists($filePath)) {
            // Mark existing video as replaced
            $existingVideo = $this->record->primaryVideo;
            if ($existingVideo) {
                $existingVideo->update(['status' => 'replaced']);
                \Log::info('Marked existing video as replaced', ['video_id' => $existingVideo->id]);
            }

            // Create new video record
            $video = Video::create([
                'title' => $this->record->title . ' - Video',
                'lesson_id' => $this->record->id,
                'original_filename' => basename($tempPath),
                'original_path' => $tempPath,
                'mime_type' => mime_content_type($filePath),
                'file_size' => filesize($filePath),
                'status' => 'pending',
                'metadata' => [
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now()->toISOString(),
                ]
            ]);

            \Log::info('Video record created', ['video_id' => $video->id]);

            // Run processing synchronously for dev, dispatch for production
            if (config('queue.default') === 'sync') {
                ProcessVideoJob::dispatchSync($video);
            } else {
                // Run synchronously anyway to avoid needing queue worker
                (new ProcessVideoJob($video))->handle();
            }

            $video->refresh();
            \Log::info('Video processing done', [
                'video_id' => $video->id,
                'status' => $video->status,
            ]);

            Notification::make()->title('Video uploaded and processed successfully')->success()->send();
        } else {
            \Log::error('Video file not found', [
                'tried' => [
                    storage_path('app/private/' . $tempPath),
                    storage_path('app/' . $tempPath),
                ]
            ]);
            Notification::make()->title('Video file not found after upload')->danger()->send();
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove video_upload from data as it's not a database field
        unset($data['video_upload']);

        return $data;
    }
}
