<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceSetValidation;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
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
    public function getUserArticlePreference(Request $request){
        try{
            $user = $request->user();
            $userPreference = UserPreference::where('user_id', $user->id)->first();
            if(!$userPreference){
                return response()->json([
                    'message' => 'user article preference not found!',
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
