<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Skill;

class SkillController extends Controller
{
    public function getSkills() {
      $skills = Skill::all();

      return response()->json($skills);
    }

    public function addSkill(Request $request) {
      
    }
}
