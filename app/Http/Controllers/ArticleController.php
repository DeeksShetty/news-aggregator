<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleDetailValidation;
use App\Http\Requests\ArticleListValidation;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    public function getArticleList(ArticleListValidation $request){
        try{
            $articles = $this->getArticleListModule($request);
            return response()->json([
                'message' => 'article list fetch successfully',
                'data' => $articles,
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to fetch article list: '.$e->getMessage());
    
            // Return error response
            return response()->json([
                'message' => 'Failed to fetch article list',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getArticleDetail(ArticleDetailValidation $request){
        try{
            $article = Article::find($request->id);
            return response()->json([
                'message' => 'article detail fetch successfully',
                'data' => $article,
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to fetch article details: '.$e->getMessage());
    
            // Return error response
            return response()->json([
                'message' => 'Failed to fetch article detail',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getArticleListModule($request,$preference=[]){
        $article = Article::select('id','source_name','author','title','published_at','category');
        if($request->search_key){
            $article = $article->where(function($q) use($request){
                $q->where('title','like','%'.$request->search_key.'%')
                ->orWhere('source_name','like','%'.$request->search_key.'%')
                ->orWhere('author','like','%'.$request->search_key.'%')
                ->orWhere('category','like','%'.$request->search_key.'%');
            });
        }
        if(!empty($preference) && !empty($preference['source'])){
            $article = $article->whereIn('source_name',$preference['source']);
        }
        if(!empty($preference) && !empty($preference['category'])){
            $article = $article->whereIn('category',$preference['category']);
        }
        if(!empty($preference) && !empty($preference['author'])){
            $article = $article->whereIn('author',$preference['author']);
        }
        if($request->category){
            $article = $article->where('category',$request->category);
        }
        if($request->source){
            $article = $article->where('source_name',$request->source);
        }
        if($request->published_date){
            $article = $article->whereDate('published_at', $request->published_date);
        }
        $articles = $article->paginate(10);
        return $articles;
    }

    public function getUserArticleList(ArticleListValidation $request){
        try{
            $user = $request->user();
            $userPreference = UserPreference::where("user_id", $user->id)->first();
            if(!$userPreference){
                return response()->json([
                    'message' => 'user prefered article list not found!',
                ], 400);
            }
            $preference['source'] = $this->stringToArrayConvert($userPreference->prefer_source);
            $preference['category'] = $this->stringToArrayConvert($userPreference->prefer_categories);
            $preference['author'] = $this->stringToArrayConvert($userPreference->prefer_author);
            $articles = $this->getArticleListModule($request,$preference);
            return response()->json([
                'message' => 'user prefered article list fetch successfully',
                'data' => $articles,
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to fetch user prefered article list: '.$e->getMessage());
    
            // Return error response
            return response()->json([
                'message' => 'Failed to fetch user prefered article list',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function stringToArrayConvert($givenString){
        if (!empty(trim($givenString))) {
            return explode(',', trim($givenString));
        }else{
            return [];
        }
    }
}
