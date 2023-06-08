<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Saved;
use App\Models\Type;
use App\Models\User;
use App\Models\Vegetable;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $vegetables = User::find($user->id)->vegetable_histories()->with(['types' => function ($query) {
            $query->select('id', 'name', 'type_group_id');
            $query->with('type_group:id,name');
        }])->withPivot('created_at')->orderBy('histories.created_at', "DESC")->take(2)->get();

        $types = Type::select('id', 'name', 'description', 'type_group_id')->with('type_group:id,name')->get();

        return response()->json([
            'histories' => $vegetables,
            'types' => $types
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
            return response()->json([
                'status' => 'failed',
                'message' => 'No file',
                'image' => $image,
                'request' => $request
            ], 400);
        }

        try {
            $imageBytes = file_get_contents($image->getPathname());

            $predictUrl = "https://vege-image-classifier-pl6a2qwedq-et.a.run.app";

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
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 500);
        }

        $responseData = json_decode($response->getBody(), true);

        if ($responseData['status'] === 'failed') {
            return response()->json([
                'status' => 'failed',
                'message' => "Class name not found"
            ], 404);
        }

        $className =  $responseData["prediction"];
        $vegetable = Vegetable::where('class_name', $className)->with(['types' => function ($query) {
            $query->select('id', 'name');
        }])->first();

        if (!$vegetable) {
            return response()->json([
                'status' => 'failed',
                'message' => "These Vegetable do not match our records."
            ], 404);
        }

        $user = Auth::user();

        History::create([
            'user_id' => $user->id,
            'vegetable_id' => $vegetable->id
        ]);

        return response()->json([
            'status' => 'success',
            "vegetable" => $vegetable
        ], 200);
    }

    public function homeHistories(): JsonResponse
    {
        $user = Auth::user();
        $vegetables = User::find($user->id)->vegetable_histories()->with(['types' => function ($query) {
            $query->select('id', 'name', 'type_group_id');
            $query->with('type_group:id,name');
        }])->withPivot('created_at')->orderBy('histories.created_at', "DESC")->take(2)->get();

        return response()->json($vegetables);
    }


    public function homeTypes(): JsonResponse
    {
        $types = Type::select('id', 'name', 'description', 'thumbnail', 'type_group_id')->with('type_group:id,name')->get();
        return response()->json($types);
    }


    /**
     * histories
     *
     * @return JsonResponse
     */
    public function histories(): JsonResponse
    {
        $user = Auth::user();
        $vegetables = User::find($user->id)->vegetable_histories()->with(['types' => function ($query) {
            $query->select('id', 'name', 'type_group_id');
            $query->with('type_group:id,name');
        }])->withPivot('created_at')->orderBy('histories.created_at', "DESC")->get();

        return response()->json($vegetables);
    }

    public function saveds(): JsonResponse
    {
        $user = Auth::user();
        $vegetables = User::find($user->id)->vegetable_saveds()->with(['types' => function ($query) {
            $query->select('id', 'name', 'type_group_id');
            $query->with('type_group:id,name');
        }])->orderBy('created_at')->get();

        return response()->json($vegetables);
    }
}
