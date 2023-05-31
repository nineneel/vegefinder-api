<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'vegetable' => $vegetables
        ]);
    }

    /**
     * getDetailVegetable
     *
     * @param  mixed $vegetable_id
     * @return JsonResponse
     */
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

    /**
     * saveVegetable
     *
     * @param  mixed $vegetable_id
     * @return JsonResponse
     */
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

    /**
     * predict
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function predict(Request $request): JsonResponse
    {
        $image = $request->file('image');

        if ($image === null || $image->getClientOriginalName() === "") {
            return response()->json(['status' => 'failed', 'message' => 'No file'], 400);
        }

        try {
            $imageBytes = file_get_contents($image->getRealPath());

            $predictUrl = "http://127.0.0.1:5000";

            $client = new Client();

            $response = $client->post($predictUrl, [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $imageBytes,
                        'filename' => $image->getClientOriginalName()
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
        }

        $responseData = json_decode($response->getBody(), true);

        if ($responseData['status'] === 'failed') {
            return response()->json(['status' => 'failed', 'message' => "These Vegetable do not match our records."], 404);
        }
        $className =  $responseData["prediction"];
        $vegetable = Vegetable::where('class_name', $className)->with('types')->first();

        return response()->json(['status' => 'success', "vegetable" => $vegetable], 200);
    }
}
