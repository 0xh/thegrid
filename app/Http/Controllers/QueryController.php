<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Query;
use App\QueryLike;
use App\Reply;
use App\Job;

class QueryController extends Controller
{
    public function create(Request $request, $id, $job_id) {
        $data = $request->all();
        $this->validate($request, [
            'query' => 'required'
        ]);

        $new_query = Query::create([
            'job_id' => $job_id,
            'user_id' => $id,
            'query' => $data['query']
        ]);

        $queries = Query::info()->where('job_id', $job_id)->get();
        
        return response()->json($queries);
    }

    public function like(Request $request, $id, $query_id) {
        $like = QueryLike::firstOrCreate([
            'query_id' => $query_id,
            'user_id' => $id,
        ]);
        $like->is_liked = true;
        $like->save();
        $queries = Query::info()->where('job_id', $request->job_id)->get();
        
        return response()->json($queries);
    }
    
    public function dislike(Request $request, $id, $query_id) {
        $dislike = QueryLike::firstOrCreate([
            'query_id' => $query_id,
            'user_id' => $id,
        ]);
        $dislike->is_liked = false;
        $dislike->save();

        $queries = Query::info()->where('job_id', $request->job_id)->get();
        
        return response()->json($queries);
    }

    public function deleteLike(Request $request, $id, $query_like_id) {
        QueryLike::destroy($query_like_id);
        // if( $query_like ) {
        //     $query_like->delete();
        // }

        $queries = Query::info()->where('job_id', $request->job_id)->get();
        
        return response()->json($queries);
    }
    
    public function reply(Request $request, $id, $query_id) {
        $dislike = Reply::create([
            'query_id' => $query_id,
            'reply' => $request->reply
        ]);

        $queries = Query::info()->where('job_id', $request->job_id)->get();

        return response()->json($queries);
    }

}
