<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class AdminPostsController extends Controller
{

    private $rules = [
        'title' => 'required|max:200',
        'slug' => 'required|max:200',
        'excerpt' => 'required|max:300',
        'category_id' => 'required|numeric',
        'thumbnail' => 'required|file|mimes:jpg,png,webp,svg,jpeg',
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
        $tags = explode(',', $request->input('tags'));
        $tags_ids = [];
        foreach ($tags as $tag) {
            $tag_ob  = Tag::create(['name' => $tag]);
            $tags_ids[] = $tag_ob->id;
        }
        if (count($tags_ids) > 0)
            $post->tags()->sync($tags_ids);
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
        $tags = '';
        foreach ($post->tags as $key => $tag) {
            $tags .= $tag->name;
            if ($key !== count($post->tags) - 1)
                $tags .= ', ';
        }


        return view('admin_dashboard.posts.edit', [
            'post' => $post,
            'tags' => $tags,
            'categories' => Category::pluck('name', 'id')
        ]);
    }


    public function update(Request $request, Post $post)
    {
        $this->rules['thumbnail'] = 'nullable|file|mimes:jpg,png,webp,svg,jpeg|dimensions:max_width=800,max_height=300';
        $validated  = $request->validate($this->rules);
        $validated['approved'] = $request->input('approved') !== null;

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

        $tags = explode(',', $request->input('tags'));
        $tags_ids = [];
        foreach ($tags as $tag) {
            $tag_ob  = Tag::create(['name' => trim($tag)]);
            $tags_ids[] = $tag_ob->id;
        }
        if (count($tags_ids) > 0)
            $post->tags()->sync($tags_ids);
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
        $post->tags()->delete();
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post has been delete');
    }
}
