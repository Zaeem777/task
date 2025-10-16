<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;

use App\Http\Resources\PostResource;
use App\Http\Requests\UpdatePostRequest;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Posts::paginate(1);
        return response()->json([
             "response" => [
            $posts],200]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $field = $request->validated();

        $imagePath = null;


        if($request->hasFile('image')){
        $imagePath = $request->file('image')->store('posts_image', 'public');        
        }
     
        $post = Posts::create([
            'name' => $field['name'],
            'description' => $field['description'],
            'image_path' => $imagePath,
            'status' => $field['status'],
        ]
        );

        return new PostResource($post, "Post Created Successfully");
    }


    public function show(string $id)
    {
      
        $post = Posts::find($id);
        if(!$post){
        return response()->json([
             "response" => [
            'message' => 'Post Not Found',
            'status' => 404
             ]
        ],404); 
    return new PostResource($post);
    }
    }

public function update(UpdatePostRequest $request, string $id)
{
     $validated = $request->validated();
    $post = Posts::find($id);
    if(!$post){
        return response()->json([
             "response" => [
            'message' => 'Post Not Found',
            'status' => 404
             ]
        ],404);
    }

   
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('posts_image', 'public');
        $validated['image_path'] = $imagePath;
    }

    $post->update($validated);

 return new PostResource($post, "Post Updated Successfully");
 
}
    public function destroy(string $id)
    {
            $post = Posts::find($id);
        if(!$post){
        return response()->json([
             "response" => [
            'message' => 'Post Not Found',
            'status' => 404
             ]
        ],404);
            $post->delete();
        
        return [
             "response" => [
            'message' => 'Post Deleted Successfully',
            'status' => 200
             ]
        ];
    }
}
}
