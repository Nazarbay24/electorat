<?php

namespace App\Http\Controllers;

use App\Models\GeoCountry;
use App\Models\GeoLocal;
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
        $userModel->last_visit = date('Y-m-d H:i:s');
        $userModel->save();
        $geo = $userModel->getGeo();
        $statistics = $userModel->getStatistics();
        $regDate = Carbon::create($userModel->regdate)->isoFormat('D MMMM Y г.');

        $appVersion = config('app.version');

        return response()->json([
            'app_version' => (int)$appVersion,
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
                'local_id' => $userModel->local_id,
                'country' => $geo->country,
                'region' => $geo->region,
                'punkt' => $geo->punkt,
                'local' => $geo->local,
                'regdate' => $regDate,
            ],
            'statistics' => $statistics
        ], 200);
    }

    public function getGeoData($locale) {
        $countries = GeoCountry::select('id', 'text_'.$locale.' as name')->get()->all();
        $regions = GeoRegion::select('id', 'country_id', 'text_'.$locale.' as name')->orderBy('sortby')->get()->all();
        $punkts = GeoPunkt::select('id', 'region_id', 'text_'.$locale.' as name')->orderBy('region_id')->orderBy('sortby')->get()->all();
        $locals = GeoLocal::select('id', 'punkt_id', 'text_'.$locale.' as name')->orderBy('punkt_id')->orderBy('sortby')->get()->all();

        return response()->json([
            'countries' => $countries,
            'regions' => $regions,
            'punkts' => $punkts,
            'locals' => $locals
        ], 200);
    }

    public function getRegions($locale, $country_id) {
        $data = GeoRegion::select('id', 'country_id as parent_id', 'text_'.$locale.' as name')->where('country_id', $country_id)->orderBy('sortby')->get()->all();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function getPunkts($locale, $region_id) {
        $data = GeoPunkt::select('id', 'region_id as parent_id', 'text_'.$locale.' as name')->where('region_id', $region_id)->orderBy('sortby')->get()->all();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function getlocals($locale, $punkt_id) {
        $data = GeoLocal::select('id', 'punkt_id as parent_id', 'text_'.$locale.' as name')->where('punkt_id', $punkt_id)->orderBy('sortby')->get()->all();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function setGeo($locale, Request $request) {
        $request->validate([
            "local_id" => "required|integer",
        ]);

        $local = GeoLocal::findOrFail($request->local_id);
        $update = ProfileManager::where('id', '=', $request->user()->id)->update(['local_id' => $local->id, 'punkt_id' => $local->punkt_id]);

        if($update) {
            return response()->json([
                'message' => __('Успешно изменено')
            ], 200);
        }
        else {
            return response()->json([
                'message' => __('Не удалось изменить')
            ], 400);
        }
    }
}
