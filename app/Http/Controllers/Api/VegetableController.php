<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Saved;
use App\Models\Vegetable;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Request;

use Illuminate\Http\Request;

class VegetableController extends Controller
{
    /**
     * getAllVegetable
     *
     * @return JsonResponse
     */
    public function getAllVegetable(): JsonResponse
    {
        $user = Auth::user();

        $vegetables = Vegetable::with(['types' => function ($query) {
            $query->select('id', 'name', 'type_group_id');
            $query->with('type_group:id,name');
        }])->orderBy('created_at', 'asc')->get();

        foreach ($vegetables as $vegetable) {
            $isSaved = Saved::where('user_id', $user->id)->where('vegetable_id', $vegetable->id)->exists();
            $vegetable['is_saved'] = $isSaved;
        }

        if (count($vegetables) == 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Vegetable Not Found',
            ]);
        }

        return response()->json($vegetables);
    }

    /**
     * getDetailVegetable
     *
     * @param  mixed $vegetable_id
     * @return JsonResponse
     */
    public function getDetailVegetable($vegetable_key): JsonResponse
    {
        $user = Auth::user();
        $vegetable = Vegetable::with(['types' => function ($query) {
            $query->select('id', 'name', 'type_group_id');
            $query->with('type_group:id,name');
        }])->where('id', $vegetable_key)->orWhere('class_name', $vegetable_key)->first();

        if ($vegetable == null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Vegetable not found'
            ], 404);
        }

        $isSaved = Saved::where('user_id', $user->id)->where('vegetable_id', $vegetable->id)->exists();
        $vegetable['is_saved'] = $isSaved;


        return response()->json($vegetable);
    }

    public function saveHistory(int $vegetable_id, int $user_id): JsonResponse
    {
        History::create([
            'user_id' => $user_id,
            'vegetable_id' => $vegetable_id
        ]);

        return response()->json(true);
    }

    /**
     * saveVegetable
     *
     * @param  mixed $vegetable_id
     * @return JsonResponse
     */
    public function saveVegetable(int $vegetable_id): JsonResponse
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
