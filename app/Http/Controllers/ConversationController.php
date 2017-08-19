<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conversation;
use DB;

class ConversationController extends Controller
{
    public function getConversations($id) {
			return Conversation::infoWithMessages()
								->where('user_id_1', $id)
    						->orWhere('user_id_2', $id)
    						->orderBy('created_at', 'desc')
								->paginate(env('INBOX_PER_PAGE'));
    	// return DB::raw('SELECT * FROM conversations');
    }

    public function createConversation(Request $request) {

			$data = $request->all();

			$_conversation = Conversation::firstOrCreate([
				'job_id' => $data['job_id'],
				'user_id_1' => $data['user_id_1'],
				'user_id_2' => $data['user_id_2']
			]);

			$conversation = Conversation::info()->where('id', $_conversation->id)->first();


			if($conversation) {
				return response()->json($conversation);
			}

			return response()->json(['status' => 'failed', 'message' => 'Something went wrong']);
    }
}
