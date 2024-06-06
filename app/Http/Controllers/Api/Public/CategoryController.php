<?php

namespace App\Http\Controllers\Api\Public;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        $categories = Category::latest()->paginate(10);

        //return with Api Resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }
    
    /**
     * show
     *
     * @param  mixed $slug
     * @return void
     */
    public function show($slug)
    {
        $category = Category::with('posts.category')->where('slug', $slug)->first();

        if($category) {
            //return with Api Resource
            return new CategoryResource(true, 'List Data Post By Category', $category);
        }

        //return with Api Resource
        return new CategoryResource(false, 'Data Category not found!', null);
    }
}