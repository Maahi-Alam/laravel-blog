<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Notifications\AuthorPostApproved;
use App\Notifications\AuthorPostCreate;
use App\Notifications\NewPostCreateNotify;
use App\Post;
use App\Subscriber;
use App\Tag;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Image;
use Notification;
use Storage;
use Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->get();

        return view('backend.admin.post.index', compact('posts'));
    }

    public function pendingList()
    {
        $posts = Post::where('is_approved', false)->with('user')->latest()->get();

        return view('backend.admin.post.pending', compact('posts'));
    }

    public function changeApproveStatus(Post $post){

        if ($post->is_approved){
            return back()->with('successMsg', 'Post already approved by admin');
        }

        $approve = $post->is_approved ? 0 : 1;

        $post->update(['is_approved' => $approve]);

        //send notification to author
        $post->user->notify(new AuthorPostApproved($post));

        //send notification to subscriber
        $subscribers = Subscriber::all();

        foreach ($subscribers as $subscriber){
            Notification::send($subscriber, new NewPostCreateNotify($post));
        }

        return back()->with('successMsg', 'Post approve status changed successfully');
    }

    public function create()
    {
        $categories = Category::latest()->get();

        $tags = Tag::latest()->get();

        return view('backend.admin.post.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:191|unique:posts',
            'img' => 'mimes:jpg,jpeg,bmp,png|max:1024',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required'
        ]);

        $request['user_id'] = Auth::id();
        $slug = Str::slug($request->title);

        if ($request->hasFile('img')) {

            $image = $request->file('img');

            $currentDate = Carbon::now()->toDateString();

            $image_name = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            // check is exits directory
            if (!Storage::disk('public')->exists('post')){

                Storage::disk('public')->makeDirectory('post');
            }

            // resize image for post and upload
            $category_image = Image::make($image)->resize(1600, 1066)->stream();
            Storage::disk('public')->put('post/'.$image_name, $category_image);

            $request['image'] = $image_name;
        }

        $request['slug'] = $slug;
        $request['status'] = $request->status ? 1 : 0;
        $request['is_approved'] = true;

        $post = Post::create($request->all());

        $post->categories()->attach($request->categories);
        $post->tags()->attach($request->tags);

        //send notification to subscriber
        $subscribers = Subscriber::all();

        foreach ($subscribers as $subscriber){
            Notification::send($subscriber, new NewPostCreateNotify($post));
        }

        return redirect()->route('admin.posts.index')->with('successMsg', 'Post created successfully');
    }

    public function show(Post $post)
    {
        return view('backend.admin.post.view', compact('post'));
    }

    public function edit(Post $post)
    {
        $categories = Category::latest()->get();

        $tags = Tag::latest()->get();

        return view('backend.admin.post.edit', compact('post','categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|max:191|unique:posts,title,'.$post->id,
            'img' => 'mimes:jpg,jpeg,bmp,png|max:1024',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required'
        ]);

        $request['user_id'] = Auth::id();
        $slug = Str::slug($request->title);

        if ($request->hasFile('img')) {

            $image = $request->file('img');

            $currentDate = Carbon::now()->toDateString();

            $image_name = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            // check is exits directory
            if (!Storage::disk('public')->exists('post')){

                Storage::disk('public')->makeDirectory('post');
            }

            // resize image for post and upload
            $category_image = Image::make($image)->resize(1600, 1066)->stream();
            Storage::disk('public')->put('post/'.$image_name, $category_image);

            $request['image'] = $image_name;

            // delete old image
            if (Storage::disk('public')->exists('post/'.$post->image)){

                Storage::disk('public')->delete('post/'.$post->image);
            }
        }

        $request['slug'] = $slug;
        $request['status'] = $request->status ? 1 : 0;

        $post->update($request->all());

        $post->categories()->sync($request->categories);
        $post->tags()->sync($request->tags);

        return redirect()->route('admin.posts.index')->with('successMsg', 'Post updated successfully');
    }

    public function destroy(Post $post)
    {
        if (Storage::disk('public')->exists('post/'.$post->image)){

            Storage::disk('public')->delete('post/'.$post->image);
        }

        $post->categories()->detach();
        $post->tags()->detach();

        $post->delete();

        return back()->with('successMsg', 'Post delete successfully');
    }

    public function changeStatus(Post $post){

        $status = $post->status ? 0 : 1;

        $post->update(['status' => $status]);

        return back()->with('successMsg', 'Post publication status changed successfully');
    }
}