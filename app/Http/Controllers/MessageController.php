<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\User;
use App\Conversation;
use OneSignal;

class MessageController extends Controller
{
    public function getMessages($id, $conversation_id) {
    	$messages = Message::where([
    					['conversation_id', '=', $conversation_id],
    					['is_deleted', '=', 0]
    				])
    				->orderBy('created_at', 'DESC')
    				->paginate(50);
    	return response()->json($messages);
    }

    public function createMessage($id, $conversation_id, Request $request) {
    	$input = $request->all();
    	$input['author_id'] = $id;
			$input['conversation_id'] = $conversation_id;

			$conversation = Conversation::where('id', $conversation_id)->first();

			$_message = Message::create($input);

			$message = Message::where('id', $_message->id)->first();

			if($conversation->last_updated_by == $id) {
				$conversation->unread_count += 1;
			} else {
				$conversation->unread_count = 1;
			}
			
			$conversation->last_updated_by = $id;
			
			if( $message ) {
				$conversation->save();
				$message->conversation = $conversation;

				// send push notification
				$this->sendNotification($id, $input['recipient_id'], $input['message'], $conversation->id);

				return response()->json($message);
			}

			return response()->json(['status' => 'failed', 'message' => 'Something went wrong']);
		}
		
		public function readMessage($id, $conversation_id, Request $request) {
			$input = $request->all();

			$message = Message::where([
				['conversation_id', '=', $conversation_id],
				['recipient_id', '=', $id]
			])->update(['status' => 1]);

			$conversation = Conversation::where('id', $conversation_id)->first();			

			if($conversation->last_updated_by != $id) {
				$conversation->unread_count = 0;
				$conversation->save();
				// $message->conversation = $conversation;
			}

			return response()->json($conversation);
		}

		public function sendNotification($author_id, $user_id, $message, $conversation_id) {
			$author = User::where('id', $author_id)->first();
			
			OneSignal::sendNotificationUsingTags(
				"{$author->name}: {$message}", 
				array(
					array('key' => 'user_id', 'relation' => '=', 'value' => $user_id,)
				),
				$url = env('APP_URL') . '/inbox/' . $conversation_id);
		}
}
