<?php

namespace App\Http\Controllers;

use App\Models\GeoCountry;
use App\Models\GeoPunkt;
use App\Models\GeoRegion;
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

    public function getGeoData($locale) {
        $countries = GeoCountry::select('id', 'text_'.$locale.' as name')->get()->all();
        $regions = GeoRegion::select('id', 'country_id', 'text_'.$locale.' as name')->orderBy('sortby')->get()->all();
        $punkts = GeoPunkt::select('id', 'region_id', 'text_'.$locale.' as name')->orderBy('region_id')->orderBy('sortby')->get()->all();

        return response()->json([
            'countries' => $countries,
            'regions' => $regions,
            'punkts' => $punkts
        ], 200);
    }

    public function setGeo($locale, Request $request) {
        $request->validate([
            "punkt_id" => "integer",
        ]);

        GeoPunkt::findOrFail($request->punkt_id);
        $update = ProfileManager::where('id', '=', $request->user()->id)->update(['punkt_id' => $request->punkt_id]);

        if($update) {
            return response()->json([
                'message' => _('Успешно изменено')
            ], 200);
        }
        else {
            return response()->json([
                'message' => _('Не удалось изменить')
            ], 400);
        }
    }
}
