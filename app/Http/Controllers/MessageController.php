<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;

class MessageController extends Controller
{
    public function getMessages($id, $conversation_id) {
    	$messages = Message::where([
    					['conversation_id', '=', $conversation_id],
    					['is_deleted', '=', 0]
    				])
    				->orderBy('created_at', 'DESC')
    				->paginate(25);
    	return $messages;
    }

    public function createMessage($id, $conversation_id, Request $request) {
    	$input = $request->all();
    	$input['author_id'] = $id;
    	$input['conversation_id'] = $conversation_id;

    	return Message::create($input);
    }
}
