<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $categories = Category::count();

        $posts = Post::count();

        $sliders = Slider::count();

        $users = User::count();

        $post_views = PostView::select([
            DB::raw('count(id) as `count`'),
            DB::raw('DATE(created_at) as day'),
        ])->groupBy('day')->where('created_at', '>=', Carbon::now()->subDays(30))->get();

        if(count($post_views)) {
            foreach($post_views as $result){
                $count[] = (int) $result->count;
                $day[] = $result->day;
            }
        }else{
            $count[] = "";
            $day[] = "";
        }

        return response()->json([
            'success'   => true,
            'message'   => 'List Data on Dashboard',
            'data'      => [
                'categories' => $categories,
                'posts'      => $posts,
                'sliders'    =>  $sliders,
                'users'      => $users,
                'post_views' => [
                    'count' => $count,
                    'days'   => $day
                ]   
            ]
        ]);
    }
}
