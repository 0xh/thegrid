<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conversation;
use DB;

class ConversationController extends Controller
{
    public function getConversations($id) {
    	return Conversation::where('user_id_1', $id)
    						->orWhere('user_id_2', $id)
    						->with('job')
    						->with('user1')
    						->with('user2')
    						->get();
    	// return DB::raw('SELECT * FROM conversations');
    }

    public function createConversation(Request $request) {

    }
}
