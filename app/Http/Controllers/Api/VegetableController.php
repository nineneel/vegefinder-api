<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saved;
use App\Models\Vegetable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VegetableController extends Controller
{
    public function getAllVegetable(): JsonResponse
    {
        $vegetables = Vegetable::with('types')->orderBy('created_at', 'asc')->get();

        if (count($vegetables) == 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Vegetable Not Found',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Vegetables fetch Successfully',
        ]);
    }

    public function getDetailVegetable($vegetable_id): JsonResponse
    {
        $user = Auth::user();
        $vegetable = Vegetable::with('types')->where('id', $vegetable_id)->first();

        $isSaved = Saved::where('user_id', $user->id)->where('vegetable_id', $vegetable->id)->exists();
        $vegetable['is_saved'] = $isSaved;

        if (!$vegetable) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Vegetable not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Vegetable fetch successfully',
            'vegetable' => $vegetable
        ], 200);
    }

    public function saveVegetable($vegetable_id): JsonResponse
    {
        $user = Auth::user();
        $vegetableId = $vegetable_id;

        $isSaved = Saved::where('user_id', $user->id)->where('vegetable_id', $vegetableId)->first();

        DB::beginTransaction();
        try {
            if ($isSaved) {
                $isSaved->delete();
                $message = "Vegetable unsaved successfully";
            } else {
                Saved::create([
                    'user_id' => $user->id,
                    'vegetable_id' => $vegetableId
                ]);
                $message = "Vegetable saved successfully";
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to save Vegetable'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }
}
