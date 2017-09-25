<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Conversation;

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

				$this->sendNotification($input['recipient_id'], $input['message'], $conversation->id);
			
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

		public function sendNotification($user_id, $message, $conversation_id) {
			$content = array(
					"en" => $message
			);

			$heading = array(
				'en' => 'New message'
			);
		
			$fields = array(
					'app_id' => env('ONESIGNAL_APP_ID'),
					'filters' => array(array("field" => "tag", "key" => "user_id", "relation" => "=", "value" => "$user_id")),
					'contents' => $content,
					'headings' => $heading,
					'url' => env('APP_URL') . '/inbox/' . $conversation_id
			);
		
			$fields = json_encode($fields);
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
					'Authorization: Basic ' . env('ONESIGNAL_API_KEY')));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		
			$response = curl_exec($ch);
			curl_close($ch);

			return $response;
		}
}
