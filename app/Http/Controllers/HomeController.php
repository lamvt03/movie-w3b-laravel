<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {
        $trendingVideos = Video::orderBy('view', 'desc')->limit(4)->get();
        $videos = Video::orderBy('createdAt', 'desc')->paginate(12);
        return view('web.home')->with([
                'trendingVideos' => $trendingVideos,
                'videos' => $videos
            ]);
    }

    public function videoDetails(Request $request){
        $models = [];
        $href = $request->v;
        $video = Video::where('href', $href)->first();
        $models['video'] = $video;
        if(Auth::check()){
            $user = Auth::user();
            $order = Order::where('userId', $user->id)
                ->where('videoId', $video->id)
                ->first();
            if($order){
                $models['order'] = $order;
            }
        }
        $models['flagLikeButton'] = true;
        return view('web.video-details')->with($models);
    }

    public function videoWatch(Request $request){
        $href = $request->v;
        $video = Video::where('href', $href)->first();
        // $models['video'] = $video;
        return view('web.video-watch')->with(['video' => $video]);
    }

    public function search(Request $request){
        $videos = Video::join('categories', 'videos.categoryId', '=', 'categories.id')
                        ->select('videos.*')
                        ->where(DB::raw("UPPER(CONCAT(videos.title, ' ', videos.director, ' ', categories.name))"), 'LIKE', '%' . strtoupper($request->keyword) . '%')
                        ->paginate(12);
        return view('web.search')->with(['videos' => $videos]);
    }

    public function about(){
        return view('web.about');
    }
}
