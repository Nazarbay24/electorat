<?php

namespace App\Http\Controllers;

use App\Models\ProfileManager;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainPageController extends Controller
{
    public function main(Request $request, ProfileManager $profileManager)
    {
        $user = $request->user();

        $userModel = $profileManager->findOrFail($user->id);
        $geo = $userModel->getGeo();
        $statistics = $userModel->getStatistics();
        $regDate = Carbon::create($userModel->regdate)->isoFormat('D MMMM Y г.');

        return response()->json([
            'profile' => [
                'name' => $userModel->name,
                'surname' => $userModel->surname,
                'lastname' => $userModel->lastname,
                'birthday' => $userModel->birthday,
                'gender' => $userModel->gender,
                'image' => $userModel->image,
                'position' => $userModel->position,
                'email' => $userModel->email,
                'mobile' => $userModel->mobile,
                'punkt_id' => $userModel->punkt_id,
                'country' => $geo->country,
                'region' => $geo->region,
                'punkt' => $geo->punkt,
                'regdate' => $regDate,
            ],
            'statistics' => $statistics
        ], 200);
    }
}
