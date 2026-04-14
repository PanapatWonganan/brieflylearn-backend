<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * List published blog posts with filters
     * GET /v1/blog/posts
     */
    public function index(Request $request)
    {
        try {
            $query = BlogPost::where('is_published', true);

            // Search filter
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // Category filter
            if ($request->has('category')) {
                $query->where('category', $request->input('category'));
            }

            // Pagination
            $perPage = $request->input('per_page', 12);
            $posts = $query->orderBy('published_at', 'desc')
                          ->paginate($perPage);

            return response()->json([
                'success' => true,
                'posts' => $posts->items(),
                'pagination' => [
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'from' => $posts->firstItem(),
                    'to' => $posts->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blog posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get blog post by slug
     * GET /v1/blog/posts/{slug}
     */
    public function show($slug)
    {
        try {
            $post = BlogPost::where('slug', $slug)
                           ->where('is_published', true)
                           ->firstOrFail();

            // Increment views count
            $post->increment('views_count');

            return response()->json([
                'success' => true,
                'post' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'content' => $post->content,
                    'cover_image_url' => $post->cover_image_url,
                    'author_name' => $post->author_name,
                    'category' => $post->category,
                    'tags' => $post->tags,
                    'views_count' => $post->views_count,
                    'published_at' => $post->published_at,
                    'created_at' => $post->created_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Increment view count
     * PUT /v1/blog/posts/{id}/view
     */
    public function incrementViews($id)
    {
        try {
            $post = BlogPost::findOrFail($id);
            $post->increment('views_count');

            return response()->json([
                'success' => true,
                'message' => 'View count updated',
                'views_count' => $post->views_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update view count',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
