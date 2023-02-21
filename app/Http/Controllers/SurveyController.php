<?php

namespace App\Http\Controllers;

use App\Models\Age;
use App\Models\Gender;
use App\Models\Lang;
use App\Models\PollsQuestion;
use Illuminate\Http\Request;

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
        print_r($request->input('questions'));
        print_r($request->input('respondent_profile'));
    }
}
