<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    use HasFactory;
    const CREATED_AT=NULL;
    const UPDATED_AT=NULL;
    protected $fillable=['survey_id','end_date','start_date'];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
