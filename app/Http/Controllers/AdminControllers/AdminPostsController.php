<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminPostsController extends Controller
{

    private $rules = [
        'title' => 'required|max:200',
        'slug' => 'required|max:200',
        'excerpt' => 'required|max:300',
        'category_id' => 'required|numeric',
        'thumbnail' => 'required|file|mimes:jpg,png,webp,svg,jpeg|dimensions:max_width=300,max_height=227',
        'body' => 'required',
    ];


    public function index()
    {
        return view('admin_dashboard.posts.index', [
            'posts' => Post::with('category')->get(),
        ]);
    }



    public function create()
    {
        return view('admin_dashboard.posts.create', [
            'categories' => Category::pluck('name', 'id')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated  = $request->validate($this->rules);
        $validated['user_id'] = auth()->id();
        $post = Post::create($validated);

        if ($request->has('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = $thumbnail->getClientOriginalName();
            $file_extension = $thumbnail->getClientOriginalExtension();
            $path = $thumbnail->store('images', 'public');

            $post->image()->create([
                'name' => $filename,
                'extension' => $file_extension,
                'path' => $path
            ]);
        }
        return redirect()->route('admin.posts.create')->with('success', 'Post has been created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('admin_dashboard.posts.edit', [
            'post' => $post,
            'categories' => Category::pluck('name', 'id')
        ]);
    }


    public function update(Request $request, Post $post)
    {
        $this->rules['thumbnail'] = 'nullable|file|mimes:jpg,png,webp,svg,jpeg';
        $validated  = $request->validate($this->rules);
        $post->update($validated);

        if ($request->has('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $filename = $thumbnail->getClientOriginalName();
            $file_extension = $thumbnail->getClientOriginalExtension();
            $path = $thumbnail->store('images', 'public');

            $post->image()->update([
                'name' => $filename,
                'extension' => $file_extension,
                'path' => $path
            ]);
        }
        return redirect()->route('admin.posts.edit', $post)->with('success', 'Post has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post has been delete');
    }
}
