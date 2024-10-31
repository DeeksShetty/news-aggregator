<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleDetailValidation;
use App\Http\Requests\ArticleListValidation;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class ArticleController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/article/list",
     *     summary="Fetch a list of articles",
     *     tags={"Article"},
     *     security={{"bearer": {"Token"}}},
     *     @OA\Parameter(
     *         name="search_key",
     *         in="query",
     *         required=false,
     *         description="Key to search articles by title or content.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         description="Filter articles by category.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         required=false,
     *         description="Filter articles by source name.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="published_date",
     *         in="query",
     *         required=false,
     *         description="Filter articles by published date.",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="The page number for pagination.",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with article list",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="article list fetch successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=11),
     *                         @OA\Property(property="source_name", type="string", example="BBC News"),
     *                         @OA\Property(property="author", type="string", example="BBC News"),
     *                         @OA\Property(property="title", type="string", example="Budget 2024: Minimum wage to rise to £12.21 an hour next year"),
     *                         @OA\Property(property="published_at", type="string", format="date-time", example="2024-10-29T18:37:17Z"),
     *                         @OA\Property(property="category", type="string", example="none")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost:8083/api/article/list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost:8083/api/article/list?page=1"),
     *                 @OA\Property(property="links", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="url", type="string", nullable=true),
     *                         @OA\Property(property="label", type="string"),
     *                         @OA\Property(property="active", type="boolean")
     *                     )
     *                 ),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="path", type="string", example="http://localhost:8083/api/article/list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer", example=2),
     *                 @OA\Property(property="total", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/article/detail/{id}",
     *     summary="Fetch article details",
     *     tags={"Article"},
     *     security={{"bearer": {"Token"}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the article to fetch details for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with article detail",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="article detail fetch successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="source_id", type="string", example="education/2024/oct/30/how-to-pay-less-for-better-special-educational-needs-provision"),
     *                 @OA\Property(property="source_name", type="string", example="Guardian api"),
     *                 @OA\Property(property="author", type="string", nullable=true, example=null),
     *                 @OA\Property(property="title", type="string", example="How to pay less for better special educational needs provision | Letters"),
     *                 @OA\Property(property="description", type="string", example="How to pay less for better special educational needs provision | Letters"),
     *                 @OA\Property(property="url", type="string", example="https://www.theguardian.com/education/2024/oct/30/how-to-pay-less-for-better-special-educational-needs-provision"),
     *                 @OA\Property(property="url_to_image", type="string", nullable=true, example=null),
     *                 @OA\Property(property="published_at", type="string", format="date-time", example="2024-10-30T18:16:18Z"),
     *                 @OA\Property(property="content", type="string", nullable=true, example=null),
     *                 @OA\Property(property="category", type="string", example="Education"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-30T18:29:15.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-30T18:29:15.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Article not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function getArticleDetail($id){
        try{
            $article = Article::find($id);
            if(!$article){
                return response()->json([
                    'message' => 'Article not found',
                ], 400);
            }
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

    //Module where logic and query writen for fetch articles from database.
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
        if(!empty($preference)){
            $article = $article->where(function($q) use($preference){
                $q->whereIn('source_name',$preference['source'])
                ->orWhereIn('category',$preference['category'])
                ->orWhereIn('author',$preference['author']);
            });
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

    /**
     * @OA\Get(
     *     path="/api/article/user/prefered-list",
     *     summary="Fetch user preferred articles",
     *     tags={"Article"},
     *     security={{"bearer": {"Token"}}},
     *     @OA\Parameter(
     *         name="search_key",
     *         in="query",
     *         required=false,
     *         description="Search keyword for filtering articles",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         description="Filter articles by category",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         required=false,
     *         description="Filter articles by source",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="published_date",
     *         in="query",
     *         required=false,
     *         description="Filter articles by published date",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with user preferred article list",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user prefered article list fetch successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=7),
     *                         @OA\Property(property="source_name", type="string", example="Guardian api"),
     *                         @OA\Property(property="author", type="string", nullable=true, example=null),
     *                         @OA\Property(property="title", type="string", example="Why I would counsel against statutory regulation of psychotherapists | Letter"),
     *                         @OA\Property(property="published_at", type="string", format="date-time", example="2024-10-30 18:15:57"),
     *                         @OA\Property(property="category", type="string", example="Society")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost:8083/api/article/user/prefered-list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost:8083/api/article/user/prefered-list?page=1"),
     *                 @OA\Property(property="links", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example=null),
     *                         @OA\Property(property="label", type="string", example="Previous"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="path", type="string", example="http://localhost:8083/api/article/user/prefered-list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                 @OA\Property(property="to", type="integer", example=2),
     *                 @OA\Property(property="total", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="No preferences set yet."),
     *     @OA\Response(response=500, description="Failed to fetch user preferred article list"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function getUserArticleList(ArticleListValidation $request){
        try{
            $user = $request->user();
            $userPreference = UserPreference::where("user_id", $user->id)->first();
            if(!$userPreference){
                return response()->json([
                    'message' => 'No preferences set yet.',
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

    //This method converts given string to array using (,) as seperator.
    public function stringToArrayConvert($givenString){
        if (!empty(trim($givenString))) {
            return explode(',', trim($givenString));
        }else{
            return [];
        }
    }
}
