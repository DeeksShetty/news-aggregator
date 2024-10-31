<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceSetValidation;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/article/set-preference",
     *     summary="Set user article preferences",
     *     tags={"User"},
     *     security={{"bearer": {"Token"}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="source", type="array",
     *                 @OA\Items(type="string", example="BBC News")),
     *             @OA\Property(property="category", type="array",
     *                 @OA\Items(type="string", example="Society")),
     *             @OA\Property(property="author", type="array",
     *                 @OA\Items(type="string", example="BBC News")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with saved user article preference",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user article preference saved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="prefer_source", type="string", example="BBC News"),
     *                 @OA\Property(property="prefer_categories", type="string", example="Society"),
     *                 @OA\Property(property="prefer_author", type="string", example="BBC News,The New York Times"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-31T10:47:16.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-31T10:57:18.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data."),
     *     @OA\Response(response=500, description="Failed to save user article preference"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function setUserArticlePreference(UserPreferenceSetValidation $request){
        try{
            $user = $request->user();
            $userPreference = UserPreference::where("user_id", $user->id)->first();
            if(!$userPreference){
                $userPreference = new UserPreference();
                $userPreference->user_id = $user->id;
            }
            $userPreference->prefer_source = implode(',',$request->source);
            $userPreference->prefer_categories = implode(',',$request->category);
            $userPreference->prefer_author = implode(',',$request->author);
            $userPreference->save();

            return response()->json([
                'message' => 'user article preference saved successfully',
                'data' => $userPreference,
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to save user article preference : '.$e->getMessage());
    
            // Return error response
            return response()->json([
                'message' => 'Failed to save user article preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/article/get-preference",
     *     summary="Get user article preferences",
     *     tags={"User"},
     *     security={{"bearer": {"Token"}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with fetched user article preference",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user article preference fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="prefer_source", type="string", example="BBC News"),
     *                 @OA\Property(property="prefer_categories", type="string", example="Society"),
     *                 @OA\Property(property="prefer_author", type="string", example="BBC News,The New York Times"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-31T10:47:16.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-31T10:57:18.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="User article preference not set."),
     *     @OA\Response(response=500, description="Failed to fetch user article preference"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function getUserArticlePreference(Request $request){
        try{
            $user = $request->user();
            $userPreference = UserPreference::where('user_id', $user->id)->first();
            if(!$userPreference){
                return response()->json([
                    'message' => 'user article preference set.',
                ], 400);
            }
            return response()->json([
                'message' => 'user article preference fetched successfully',
                'data' => $userPreference,
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to fetch user article preference : '.$e->getMessage());
    
            // Return error response
            return response()->json([
                'message' => 'Failed to fetch user article preference',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
