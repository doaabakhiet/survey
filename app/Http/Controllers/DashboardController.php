<?php

namespace App\Http\Controllers;

use App\Http\Resources\SurveyAnswerResource;
use App\Http\Resources\SurveyDashboardResource;
use App\Http\Resources\SurveyResource;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user =$request->user();

        //total number of surveys
        $totalSurveys=Survey::query()->where('user_id',$user->id)->count();
        $latestSurveys=Survey::query()->where('user_id',$user->id)->latest('created_at')->first();

        $totalAnswerSurveys=SurveyAnswer::query()
        ->join('surveys','survey_answers.survey_id','=','surveys.id')
        ->where('surveys.user_id',$user->id)->count();
        
        $latestAnswerSurveys=SurveyAnswer::query()
        ->join('surveys','survey_answers.survey_id','=','surveys.id')
        ->where('surveys.user_id',$user->id)
        ->orderBy('end_date','DESC')
        ->limit(5)->getModels('survey_answers.*');


        return [
            'totalSurveys'=>$totalSurveys,
            'latestSurveys'=>$latestSurveys ? new SurveyDashboardResource($latestSurveys): null,
            'totalAnswerSurveys'=>$totalAnswerSurveys,
            'latestAnswerSurveys'=>SurveyAnswerResource::collection($latestAnswerSurveys),
        ];
    }
}
