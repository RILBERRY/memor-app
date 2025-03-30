<?php

namespace App\Http\Middleware;

use App\Models\PostGenerateData;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUsersPost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $post = $request->route('post');
        $postId = $request->route('id');
        $PostInfor = $request->get('PostInfor');

        if( $postId){
            $postFromDB = PostGenerateData::find($postId);
            if($postFromDB && $postFromDB->user_id === auth()->user()->id){
                return $next($request);
            }
        }
        if($post ){
            $postFromDB = PostGenerateData::whereJsonContains('post_path', $post)->first();
            if($postFromDB && $postFromDB->user_id === auth()->user()->id){
                return $next($request);
            }
        }
        if( $PostInfor){
            $postFromDB = PostGenerateData::find($PostInfor);
            if($postFromDB && $postFromDB->user_id === auth()->user()->id){
                return $next($request);
            }else{
                return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page');

            }
        }else{
            return $next($request);

        }
        return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page');
    }
}
