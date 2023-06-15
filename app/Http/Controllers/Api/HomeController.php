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

        foreach ($vegetables as $vegetable) {
            $isSaved = Saved::where('user_id', $user->id)->where('vegetable_id', $vegetable->id)->exists();
            $vegetable['is_saved'] = $isSaved;
        }

        return response()->json([
            'histories' => $vegetables,
            'types' => $types
        ]);
    }

    public function homeHistories(): JsonResponse
    {
        $user = Auth::user();
        $vegetables = User::find($user->id)->vegetable_histories()->with(['types' => function ($query) {
            $query->select('id', 'name', 'type_group_id');
            $query->with('type_group:id,name');
        }])->withPivot('created_at')->orderBy('histories.created_at', "DESC")->take(5)->get();

        foreach ($vegetables as $vegetable) {
            $isSaved = Saved::where('user_id', $user->id)->where('vegetable_id', $vegetable->id)->exists();
            $vegetable['is_saved'] = $isSaved;
        }

        return response()->json($vegetables);
    }


    public function homeTypes(): JsonResponse
    {
        $types = Type::select('id', 'name', 'description', 'thumbnail', 'type_group_id')->with('type_group:id,name')->inRandomOrder()->take(4)->get();
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
        }])->withPivot('created_at AS history_created_at')->orderBy('histories.created_at', "DESC")->get();

        foreach ($vegetables as $vegetable) {
            $isSaved = Saved::where('user_id', $user->id)->where('vegetable_id', $vegetable->id)->exists();
            $vegetable['is_saved'] = $isSaved;
        }

        return response()->json($vegetables);
    }

    public function saveds(): JsonResponse
    {
        $user = Auth::user();
        $vegetables = User::find($user->id)->vegetable_saveds()
            ->orderBy('saveds.created_at', "DESC")
            ->with(['types' => function ($query) {
                $query->select('id', 'name', 'type_group_id');
                $query->with('type_group:id,name');
            }])
            ->get();

        foreach ($vegetables as $vegetable) {
            $isSaved = Saved::where('user_id', $user->id)
                ->where('vegetable_id', $vegetable->id)
                ->exists();
            $vegetable['is_saved'] = $isSaved;
        }

        return response()->json($vegetables);
    }
}
