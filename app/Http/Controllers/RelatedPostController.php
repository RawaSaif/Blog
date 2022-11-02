<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class RelatedPostController extends Controller
{
    public function index(Post $post)
    {
        $category = $post->category;

        $relatedPosts = $category->posts()->where('id', '!=', $post->id)->latest()->take(3)->get();
        return PostResource::collection($relatedPosts);
    }
}