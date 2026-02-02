<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a paginated list of active posts.
     */
    public function index(): JsonResponse
    {
        $posts = Post::active()
            ->with('user')
            ->latest('published_at')
            ->paginate(20);

        return response()->json($posts);
    }

    /**
     * Show the form for creating a new post.
     * Only authenticated users can access this.
     */
    public function create(): Response
    {
        return response('posts.create');
    }

    /**
     * Store a newly created post in storage.
     * Only authenticated users can create posts.
     */
    public function store(PostRequest $request): JsonResponse
    {
        $post = $request->user()->posts()->create($request->validated());

        return response()->json($post->load('user'), 201);
    }

    /**
     * Display the specified post.
     * Returns 404 if post is draft or scheduled.
     */
    public function show(Post $post): JsonResponse
    {
        // Check if post is active (published)
        if ($post->isDraft() || $post->isScheduled()) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($post->load('user'));
    }

    /**
     * Show the form for editing the specified post.
     * Only the post author can access this.
     */
    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        return response('posts.edit');
    }

    /**
     * Update the specified post in storage.
     * Only the post author can update the post.
     */
    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return response()->json($post->load('user'));
    }

    /**
     * Remove the specified post from storage.
     * Only the post author can delete the post.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->noContent();
    }
}
