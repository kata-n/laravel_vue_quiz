<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Quiz;

class QuizController extends Controller
{
  public function index(Request $request){
    $category = $request->input('categories');
    if($category){
      $category = explode(',', $category);
    }else{
      return[];//からの配列を返却
    }

    $quiz = Quiz::with(['answer','category'])
      ->wherein('quizzes.categories_id', $category)
      ->inRandomOrder()
      ->limit(10)
      ->get();
    
    return $quiz;
  }
}
