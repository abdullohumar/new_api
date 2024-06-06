<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;

class PostController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $posts = Post::with('user', 'category')->withCount('views')->when(request()->search, function($posts) {
            $posts = $posts->where('title', 'like', '%'. request()->search . '%');
        })->latest()->paginate(6);

        //return with Api Resource
        return new PostResource(true, 'List Data Posts', $posts);
    }
    
    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        //get post by slug
        $post = Post::with('user', 'category')->withCount('views')->where('slug', $slug)->first();
        
        //insert views
        $post->views()->create([
            'views' => 1
        ]);

        if($post) {
            //return with Api Resource
            return new PostResource(true, 'Detail Data Post', $post);
        }

        //return with Api Resource
        return new PostResource(true, 'Detail Data Post Tidak Ditemukan!', null);

    }
    
    /**
     * postHomepage
     *
     * @return void
     */
    public function postHomepage()
    {
        $posts = Post::with('user', 'category')->take(5)->latest()->get();

        //return with Api Resource
        return new PostResource(true, 'List Data Posts Homepage', $posts);
    }
    
    /**
     * storeImagePost
     *
     * @param  mixed $request
     * @return void
     */
    public function storeImagePost(Request $request)
    {
        //upload new image
        $image = $request->file('image');
        $image->storeAs('public/post_images', $image->hashName());

        return response()->json([
            'url'       => asset('storage/post_images/'.$image->hashName())
        ]);
    }

}