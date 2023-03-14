<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class SurveyDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'slug'=>$this->slug,
            'image_url' => $this->image ? URL::to($this->image) : null,
            'status '=>$this->status !=='draft',
            'created_at'=>(new DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'expire_date'=>$this->expire_date,
            'questions'=>$this->questions()->count(),
            'answers'=>$this->answers()->count()
        ];
    }
}
