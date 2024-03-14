<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollsQuestion extends Model
{
    use HasFactory;

    protected $table = 'polls_question';

    public function getQuestionsWithAnswers() {
        $questions = $this->select('id', 'text_kk', 'text_ru', 'answer_type', 'comment_enabled')->orderBy('sortby')->get()->all();
        $answers = PollsQuestionAnswer::select('id', 'question_id', 'text_kk', 'text_ru')->orderBy('question_id')->orderBy('sortby')->get()->all();

        $sorted = [];

        foreach ($questions as $question) {
            $q = [
                'id' => $question->id,
                'text_kk' => $question->text_kk,
                'text_ru' => $question->text_ru,
                'answer_type' => $question->answer_type,
                'comment_enabled' => (bool)$question->comment_enabled,
                'answers' => []
            ];

            foreach ($answers as $answer) {
                if ($answer->question_id == $question->id) {
                    unset($answer->question_id);
                    $q['answers'][] = $answer;
                }
            }
            $sorted[] = $q;
        }

        return $sorted;
    }
}
