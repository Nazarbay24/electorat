<?php

namespace App\Http\Controllers;

use App\Models\Age;
use App\Models\Gender;
use App\Models\GeoLocal;
use App\Models\Lang;
use App\Models\PollsQuestion;
use App\Models\PollsRespondent;
use App\Models\PollsResponses;
use App\Models\ProfileManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Profiler\Profile;

class SurveyController extends Controller
{
    public function getSurveyData(Request $request, PollsQuestion $pollsQuestion)
    {
        $questions = $pollsQuestion->getQuestionsWithAnswers();

        $ages = Age::select('id', 'text_kk', 'text_ru')->get()->all();
        $langs = Lang::select('id', 'text_kk', 'text_ru')->get()->all();
        $genders = Gender::select('id', 'text_kk', 'text_ru')->get()->all();

        return response()->json([
            'questions' => $questions,
            'respondent_profile' => [
                'ages' => $ages,
                'genders' => $genders,
                'langs' => $langs,
            ]
        ], 200);
    }

    public function saveSurvey(Request $request) {
        $request->validate([
            "responses" => "required|array",
            "responses.*.question_id" => "required|integer",
            "responses.*.answer_ids" => "required|array",
            "responses.*.answer_ids.*" => "required|integer",
            "responses.*.comment" => "string|max:255",
            "respondent_profile.local_id" => "required|integer",
            "respondent_profile.age_id" => "required|integer",
            "respondent_profile.gender_id" => "required|integer",
            "respondent_profile.lang_id" => "required|integer",
            "respondent_profile.coordinates" => "max:255",
        ]);

        $responses = $request->input('responses');
        $respondentProfile = $request->input('respondent_profile');

        $profileManager = ProfileManager::findOrFail($request->user()->id);
        $local = GeoLocal::findOrFail($profileManager->local_id);

        $pollsRespondent = new PollsRespondent();
        $pollsRespondent->manager_id = $profileManager->id;
        $pollsRespondent->local_id = $profileManager->local_id;
        $pollsRespondent->punkt_id = $local->punkt_id;
        $pollsRespondent->age_id = $respondentProfile['age_id'];
        $pollsRespondent->gender_id = $respondentProfile['gender_id'];
        $pollsRespondent->lang_id = $respondentProfile['lang_id'];
        $pollsRespondent->from_local_id = $respondentProfile['local_id'];
        $pollsRespondent->coordinates = $respondentProfile['coordinates'] ?: '';
        $pollsRespondent->regdate = date('Y-m-d H:i:s');

        if(!$pollsRespondent->save()) {
            return response()->json([
                'message' => __('Не удалось сохранить опрос')
            ], 400);
        }

        $responseRows = [];
        foreach ($responses as $response) {
            foreach ($response['answer_ids'] as $answer_id) {
                $item = [];
                $item['respondent_id'] = $pollsRespondent->id;
                $item['manager_id'] = $profileManager->id;
                $item['question_id'] = $response['question_id'];
                $item['regdate'] = date('Y-m-d H:i:s');
                $item['answer_id'] = $answer_id;
                $item['comment'] = '';
                $responseRows[] = $item;
            }

            if(isset($response['comment'])) {
                $item = [];
                $item['comment'] = $response['comment'];
                $item['respondent_id'] = $pollsRespondent->id;
                $item['manager_id'] = $profileManager->id;
                $item['question_id'] = $response['question_id'];
                $item['answer_id'] = 0;
                $item['regdate'] = date('Y-m-d H:i:s');
                $responseRows[] = $item;
            }
        }

        if(!PollsResponses::insert($responseRows)) {
            return response()->json([
                'message' => __('Не удалось сохранить опрос')
            ], 400);
        }

        return response()->json([
            'message' => __('Успешно сохранено')
        ], 200);
    }
}
