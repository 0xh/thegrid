<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conversation;
use DB;
use Carbon\Carbon;

class ConversationController extends Controller
{
    public function getConversations($id) {
			return Conversation::infoWithMessages()
				->where(function($q) use($id) {
					$q->where('user_id_1', $id);
					$q->where('user_1_deleted_at', null);
				})
				->orWhere(function($q) use($id) {
					$q->where('user_id_2', $id);
					$q->where('user_2_deleted_at', null);
				})
				->orderBy('created_at', 'desc')
				->paginate(env('INBOX_PER_PAGE'));
    }

    public function createConversation(Request $request) {

			$data = $request->all();

			$_conversation = Conversation::firstOrCreate([
				'job_id' => $data['job_id'],
				'user_id_1' => $data['user_id_1'],
				'user_id_2' => $data['user_id_2']
			]);

			$conversation = Conversation::infoWithMessages()->where('id', $_conversation->id)->first();

			if($conversation) {
				return response()->json($conversation);
			}

			return response()->json(['status' => 'failed', 'message' => 'Something went wrong']);
		}
		
		public function delete($id, $conversation_id) {
			$conversation = Conversation::where('id', $conversation_id)->first();
			if( $conversation->user_id_1 == $id ) {
				$conversation->user_1_deleted_at = Carbon::now();
			}
			if( $conversation->user_id_2 == $id ) {
				$conversation->user_2_deleted_at = Carbon::now();
			}
			$conversation->save();

			return response()->json($conversation);
		}
}
