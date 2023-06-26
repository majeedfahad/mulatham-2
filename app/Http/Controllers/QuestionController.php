<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerUser;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::all();
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('questions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $question = Question::create([
                'title' => $request['question'],
                'score' => $request['score'],
                'type' => $request['answer_option'],
                'status' => 0
            ]);

            if($request['answer_option'] == 2) {
                for($i = 1; $i <=4; $i++) {
                    $answer = new Answer();
                    $answer->question_id = $question->id;
                    $answer->title = $request["$i"];
                    $i == $request['correct_answer'] ? $answer->iscorrect = 1 : $answer->iscorrect = 0;
                    $answer->save();
                }
            }
        } catch (\Throwable $e) {
            throw $e;
        }
        return redirect()->route('settings.questions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::find($id);
        return view('questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $question = Question::find($id);
        return view('questions.edit', compact('question'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question = Question::find($id);
        $question->delete();
        return back();
    }

    public function activeQuestion($id)
    {
        DB::table('questions')->where('status', 1)->update(['status' => 0]);
        $question = Question::find($id);
        $question->active();
        return back();
    }
    public function deActiveQuestion($id)
    {
        $question = Question::find($id);
        $question->deActive();
        $users = User::where('role', 0)->get();
        foreach ($users as $user) {
            $user->assignQuestionScore($id);
        }
        return back();
    }

    public function correctAnswer($answer_id)
    {

        $answer = AnswerUser::find($answer_id);
        $answer->score = $answer->question->score;
        $answer->update();
        return back();
    }

    public function wrongAnswer($answer_id)
    {
        $answer = AnswerUser::find($answer_id);
        $answer->score = 0;
        $answer->update();
        return back();
    }
}
