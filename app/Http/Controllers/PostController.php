<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function get_Random_Posts(){
        $posts = Post::inRandomOrder()->limit(9)->with("category" , "comments")-> withCount("comments") -> get();
        foreach( $posts as $post ){
            $post -> setAttribute("added_at" , $post -> created_at -> diffForHumans() ); 
        }
        return response() -> json( $posts );
    }

    public function get_Lasted_Posts(){
        $posts = Post::orderBy('id' , "desc") -> withCount("comments") -> paginate(10);
        foreach( $posts as $post ){
            $post -> setAttribute("added_at" , $post -> created_at -> diffForHumans() ); 
        }
        return response() -> json( $posts );
    }

    public function get_Related_Posts( $slug )   // if is not $id not add Post Model in params
    {
        $post  = Post::where('slug', $slug ) -> first();
        $posts = Post::where('category_id', $post -> category_id ) -> inRandomOrder()-> withCount('comments') -> limit(3)->get(); 
        foreach( $posts as $post ){
            $post -> setAttribute("added_at" , $post -> created_at -> diffForHumans() ); 
            $post -> setAttribute("category_title" , $post -> category -> title ); 
        } 
        return response() -> json( $posts );
    }

    public function search_Posts($searchVal){
        $posts = Post::where("title" ,"like" , '%'.$searchVal.'%' ) -> orderBy('id' , "desc") -> withCount("comments") -> get();
        foreach( $posts as $post ){
            $post -> setAttribute("added_at" , $post -> created_at -> diffForHumans() ); 
        }
        return response() -> json( $posts );
    }

    
    public function show( $slug )   // if is not $id not add Post Model in params
    {
        
        $post = Post::where("slug" , $slug )->with(["category", "comments"]) -> withCount("comments") -> first();
        $post -> setAttribute("added_at" , $post -> created_at -> diffForHumans() );


        $comments = $post -> comments;
        foreach( $comments as $comment ){
            $comment -> setAttribute("user" , $comment -> user );
            $comment -> setAttribute("added_at" , $comment -> created_at -> diffForHumans() );
        }

        return response() -> json( $post );
        
    }

}