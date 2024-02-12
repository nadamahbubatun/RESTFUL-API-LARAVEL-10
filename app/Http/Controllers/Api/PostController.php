<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
   /**
    * index
    *
    * @return void
    */

   public function index()
   {
      //get all posts

      $posts = Post::latest()->paginate(5);

      // return collection of posts as a resource
      return new PostResource(true, 'List Data Posts', $posts);

      /**
       * store
       *
       * @param mixed $request
       *  @return void
       */
   }
   public function store(Request $request)
   {
      //define validation rules
      $validator = Validator::make($request->all(), [
         'image' =>
         'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         'title' => 'required',
         'content' => 'required',
      ]);
      //check if validation fails
      if ($validator->fails()) {
         return response()->json($validator->errors(), 422);
      }
      //upload image
      $image = $request->file('image');
      $image->storeAs('public/posts', $image->hashName());
      //create post
      $post = Post::create([
         'image' => $image->hashName(),
         'title' => $request->title,
         'content' => $request->content,
      ]);
      //return response
      return new PostResource(
         true,
         'Data Post Berhasil Ditambahkan!',
         $post
      );
   }

   // public function update(Request $request, $id)
   // {
   //    $post = post::find($id);

   //    if (!$post) {
   //       return response()->json(['error' => 'Item not found'], 404);
   //    }

   //    $post->update($request->all());

   //    return response()->json(['message' => 'Item updated successfully', 'item' => $post]);
   // }

   /**
    * show
    * @param mixed $request
    * @return void
    */

   public function show($id)
   {
      $post = Post::find($id);

      return new PostResource(true, 'detail data post', $post);
   }



   /**
    * show
    * @param mixed $request
    * @return void
    */

   public function edit(string $id): View
   {
      //get post by ID
      $post = Post::findOrFail($id);

      //render view with post
      return view('posts.edit', compact('post'));
   }


   /**
    * update
    *
    * @param  mixed $request
    * @param  mixed $id
    * @return RedirectResponse
    */

   public function update(Request $request, $id)
   {
      //validate form
      $this->validate($request, [
         'image'     => 'image|mimes:jpeg,jpg,png|max:2048',
         'title'     => 'required|min:5',
         'content'   => 'required|min:10'
      ]);

      //get post by ID
      $post = Post::findOrFail($id);

      //check if image is uploaded
      if ($request->hasFile('image')) {

         //upload new image
         $image = $request->file('image');
         $image->storeAs('public/posts', $image->hashName());

         //delete old image
         Storage::delete('public/posts/' . $post->image);


         //update post with new image
         $post->update([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
         ]);
      } else {

         //update post without image
         $post->update([
            'title'     => $request->title,
            'content'   => $request->content
         ]);
      }
      return new PostResource(true, 'data berhasil di ubah!', $post);
   }

   /**
    * destroy
    *
    * @param  mixed $post
    * @return void
    *
    */

   public function destroy($id)
   {
      //get post by ID
      $post = Post::findOrFail($id);

      //delete image
      Storage::delete('public/posts/' . $post->image);

      //delete post
      $post->delete();

      //redirect to index
      return new PostResource(true, 'data berhasil di hapus', null);
   }
}
