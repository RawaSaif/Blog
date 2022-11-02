<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
//use Illuminate\Support\File;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->category) {
            return PostResource::collection(Category::where('name', $request->category)->firstOrFail()->posts()->latest()->paginate(1)->withQueryString());
        } else if ($request->search) {
            return  PostResource::collection(Post::where('title', 'like', '%' . $request->search . '%')
                ->orWhere('body', 'like', '%' . $request->search . '%')->latest()->paginate(1)->withQueryString());
        }

        return PostResource::collection(Post::latest()->paginate(1));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'category_id' => 'required'
        ]);

        $title = $request->title;
        $category_id = $request->category_id;

        if (!Post::count()) {
            $postId = 1;
        } else {
            $postId = Post::latest()->first()->id + 1;
        }

        $slug = Str::slug($title, '-') . '-' . $postId;
        $user_id = auth()->user()->id;
        $body = $request->input('body');
        $imagePath="";
        if($request->hasFile('file'))
        {
            $file=$request->file('file');
            $file_name = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images'), $file_name);
            $imagePath= 'images/'.$file_name;
           
        }
    
       // create and save post
        $post = new Post();
        $post->id=$postId;
        $post->title = $title;
        $post->category_id = $category_id;
        $post->slug = $slug;
        $post->user_id = $user_id;
        $post->body = $body;
        $post->imagePath = $imagePath;
        $post->save();
      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        if (auth()->user()->id !== $post->user->id) {
            return abort(403);
        }
        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
      if (auth()->user()->id !== $post->user->id) {
            return abort(403);
        }
        $request->validate([
            'title' => 'required',
            'file' => 'nullable | image',
            'body' => 'required',
            'category_id' => 'required'
        ]);

        $title = $request->title;
        $category_id = $request->category_id;


        $slug = Str::slug($title, '-') . '-' . $post->id;
        $body = $request->input('body');

        if ($request->file('file')) {
            File::delete($post->imagePath);
           // $imagePath = 'storage/' . $request->file('file')->store('postsImages', 'public');
           $file=$request->file('file');
           $file_name = time().'_'.$file->getClientOriginalName();
           $file->move(public_path('images'), $file_name);
           $imagePath= 'images/'.$file_name;
          $post->imagePath = $imagePath;
        }

        // create and save post
        $post->id= $post->id;
        $post->title = $title;
        $post->category_id = $category_id;
        $post->slug = $slug;
        $post->body = $body;
        return $post->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if (auth()->user()->id !== $post->user->id) {
            return abort(403);
        }

        return $post->delete();
    }
}