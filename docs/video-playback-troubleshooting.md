# Video Playback Troubleshooting Guide

## Common Issues and Solutions

### MEDIA_ERR_SRC_NOT_SUPPORTED Error

**Symptoms:**
- Browser shows "The video format is not supported" 
- Console error: `NotSupportedError: The element has no supported sources`
- Error code 4 in video player

**Root Causes & Solutions:**

#### Cause 1: No Video Files Exist (Post-Revert)
Most common after code restore/revert operations.

**Check:**
```bash
curl https://boostme-backend-production.up.railway.app/api/v1/debug/list-videos
curl https://boostme-backend-production.up.railway.app/api/v1/debug/list-lessons
```

**Fix:** Re-upload videos through admin panel:
1. Go to `/admin/lessons`
2. Click "Upload Video" action for each lesson
3. Upload video files again
4. Verify status changes to "Ready"

#### Cause 2: Incorrect Video Format Metadata
ProcessVideoJob incorrectly setting `hls_path` to MP4 files.

**Fix:**
1. Check ProcessVideoJob.php:193 - ensure `hls_path` is null for MP4 files
2. Use `processed_path` field for direct MP4 playback
3. Set metadata `is_hls: false` and `format: mp4_direct`

### 403 Forbidden Errors

**Symptoms:**
- `Failed to load resource: the server responded with a status of 403`
- Video thumbnails not loading

**Root Cause:**
Video records exist in database but actual files are missing from storage.

**Fix:**
```bash
curl https://boostme-backend-production.up.railway.app/api/v1/debug/delete-broken-videos
```

### Video Status Display Issues

**Symptoms:**
- Admin panel shows "completed" instead of "ready"
- Status not updating after upload

**Fix:**
Update LessonResource.php line 256:
```php
return match($record->primaryVideo->status) {
    'ready' => 'Ready',        // Changed from 'completed'
    'processing' => 'Processing',
    'failed' => 'Failed',
    default => 'Pending'
};
```

## Debug Commands

### Check Video Records
```bash
# List all videos with status
curl -s https://boostme-backend-production.up.railway.app/api/v1/debug/videos | jq '.videos[] | {id, lesson_id, status, original_path, hls_path}'

# Check specific video by ID
curl -s https://boostme-backend-production.up.railway.app/api/v1/debug/videos/{id}
```

### Clean Up Broken Videos
```bash
# Delete videos without actual files
curl -s https://boostme-backend-production.up.railway.app/api/v1/debug/delete-broken-videos

# Check queue status
curl -s https://boostme-backend-production.up.railway.app/api/v1/debug/queue-status
```

## Key Files to Check

1. **ProcessVideoJob.php** - Core video processing logic
2. **VideoUploadController.php** - Video streaming endpoint
3. **LessonResource.php** - Admin panel video upload
4. **WorkingSecureVideoPlayer.tsx** - Frontend video player

## Prevention Steps

1. Always test video upload in dev environment first
2. Check logs after ProcessVideoJob runs
3. Verify video status changes from 'pending' → 'processing' → 'ready'
4. Test video playback immediately after upload
5. Monitor for 403 errors in browser console

## Emergency Restore Process

If video system is completely broken:
1. Identify last working commit: `git log --oneline`
2. Create backup branch: `git checkout -b backup-$(date +%Y%m%d)`
3. Restore to working state: `git reset --hard [working-commit]`
4. Force push if needed: `git push --force-with-lease`
5. Redeploy to Railway

## Testing Checklist

- [ ] Upload new video in admin panel
- [ ] Check video status shows "Ready"
- [ ] Test video playback in frontend
- [ ] Verify no console errors
- [ ] Check video URL returns 200 status
- [ ] Test on different browsers (Chrome, Safari, Firefox)

Last Updated: $(date +"%Y-%m-%d %H:%M:%S")