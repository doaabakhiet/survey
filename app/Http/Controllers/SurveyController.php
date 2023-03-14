<?php

namespace App\Http\Controllers;

use App\Http\Requests\Survey\StoreSurveyAnswerRequest;
use App\Http\Requests\Survey\StoreSurveyRequest;
use App\Http\Requests\Survey\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        // return Survey::where('user_id',$user->id)->paginate();
        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate(5));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();
        if (isset($data['image'])) {
            $relativePath  = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }
        $data['user_id'] = request()->user()->id;
        $survey = Survey::create($data);
        //questions
        foreach($data['questions'] as $question){
            $question['survey_id']=$survey->id;
            $this->createQuestion($question);
        }
        return new SurveyResource($survey);
    }
    private function createQuestion($data)
    {
        if(is_array($data['data'])){
            $data['data']=json_encode($data['data']);
        }
        $validator=Validator::make($data,[
            'question'=>'required|string',
            'type'=>['required',Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
            ])],
            'description'=>'nullable|string',
            'data'=>'present',
            'survey_id'=>'exists:App\Models\Survey,id'
        ]);
        return SurveyQuestion::create($validator->validated());
    }
    /**
     * Display the specified resource.
     */
    public function show(Survey $survey, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $survey->user_id) {
            return abort(403, 'Unauthorized Action');
        }
        return new SurveyResource($survey);
    }

    public function showForGuest(Survey $survey)
    {
        return new SurveyResource($survey);
    }
    public function storeAnswer(StoreSurveyAnswerRequest $request,Survey $survey)
    {
        $validated=$request->validated();
        $surveyAnswer=SurveyAnswer::create([
            'survey_id'=>$survey->id,
            'start_date'=>date('Y-m-d H:i:s'),
            'end_date'=>date('Y-m-d H:i:s'),
        ]);

        foreach($validated['answers'] as $questionId=>$answer){
            $question=SurveyQuestion::where(['id'=>$questionId ,'survey_id'=>$survey->id])->get();
            if(!$question){
                return response('invalid question id',400);
            }
            $data=[
                'survey_question_id'=>$questionId,
                'survey_answer_id'=>$surveyAnswer->id,
                'answer'=>is_array($answer)?json_encode($answer):$answer
            ];
            SurveyQuestionAnswer::create($data);
        }
        return response('',201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        // return $request->image;
        $data=$request->validated();
        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            // If there is an old image, delete it
            if ($survey->image) {
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            }
        }
        // $survey->update($request->validated());
        $survey->update($data);

        $questionIds=$survey->questions()->pluck('id')->toArray();
        $newIds=Arr::pluck($data['questions'],'id');

        $todelete=array_diff($questionIds,$newIds);

        $toAdd=array_diff($newIds,$questionIds);

        SurveyQuestion::destroy($todelete);

        foreach($data['questions'] as $question){
            if(in_array($question['id'],$toAdd)){
                $question['survey_id']=$survey->id;
                $this->createQuestion($question);
            }
        }
        $questionMap=collect($data['questions'])->keyBy('id');
        foreach($survey->questions as $question){
            if(isset($questionMap[$question->id])){
                $this->updateQuestion($question,$questionMap[$question->id]);
            }
        }
        return new SurveyResource($survey);
    }
    private function updateQuestion(SurveyQuestion $question,$data)
    {
        if(is_array($data['data'])){
            $data['data']=json_encode($data['data']);
        }
        $validator=Validator::make($data,[
            'id'=>'exists:App\Models\SurveyQuestion,id',
            'question'=>'required|string',
            'type'=>['required',Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
            ])],
            'description'=>'nullable|string',
            'data'=>'present',
        ]);

        return $question->update($validator->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $survey->user_id) {
            return abort(403, 'Unauthorized Action');
        }
        // If there is an old image, delete it
        if ($survey->image) {
            $absolutePath = public_path($survey->image);
            File::delete($absolutePath);
        }
        $survey->delete();
        return response('', 204);
    }
    private function saveImage($image)
    {
        // Check if image is valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);
            // Get file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // Check if file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }
}
