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
        //  return Posts::all();
        $posts = Posts::paginate(1);

        return response()->json($posts);
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

        return (new PostResource($post))->additional(['message' => 'Post Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Posts::find($id);
    }


//    public function update(Request $request, string $id)
// {

//     $post = Posts::findOrFail($id);
//     $name = $request->name;
//     $description = $request->description;
  
//     // $validated= [$request];
//     // $validated = $request->validate([
//     //     'name'        => 'required|string|max:255',
//     //     'description' => 'required|string',
//     //     'image'       => 'required|image|mimes:jpg,jpeg,png|max:2048',
//     //     'status'      => 'required|in:0,1',
//     // ]);
    
//     if ($request->hasFile('image')) {
//         $imagePath = $request->file('image')->store('posts_image', 'public');
//         $validated['image_path'] = $imagePath; 
//     }

//     $post->update($validated);

//     return response()->json([
//         'message' => 'Post updated successfully',
//         'post'    => $post
//     ]);
// }


public function update(UpdatePostRequest $request, string $id)
{
     $validated = $request->validated();
    $post = Posts::findOrFail($id);

   
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('posts_image', 'public');
        $validated['image_path'] = $imagePath;
    }

    $post->update($validated);

    return response()->json([
        'message' => 'Post updated successfully',
        'post'    => $post
    ]);
}
    public function destroy(string $id)
    {
            $post = Posts::findOrFail($id);
            $post->delete();

        return ['message' => 'Post Deleted Successfully'];
    }
}
