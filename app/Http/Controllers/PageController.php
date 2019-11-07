<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Post;
use App\Category;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['except' => ['index','show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$posts = Post::all();
        $posts = Post::orderBy('id','desc')->get();
        return view('post.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dump category names from database
        $categories = Category::all();
        //dd($categories);

        return view('post.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);

        // Validation
        $request->validate([
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'photo' => 'required|mimes:jpeg,jpg,png',
            'category' => 'required',
        ]);

        // File Upload
        if($request->hasfile('photo')){
            $photo = $request->file('photo');
            //$name = $photo->getClientOriginalName();
            $name = time().'.'.$photo->getClientOriginalExtension();
            $photo->move(public_path().'/storage/image/',$name);
            $photo = '/storage/image/'.$name;
        }


        // Data insert
        $post = new Post();
        $post->title = request('title');
        $post->body = request('content');
        $post->image = $photo;
        $post->category_id = request('category');
        $post->user_id = Auth::id();
        // $post->status = true;

        $post->save();

        // Redirect
        return redirect()->route('firstpage');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        //$post = Post::where('status',1)->first();
        return view('post.detail',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);    // old value
        $categories = Category::all();  // 
        return view('post.edit',compact('categories','post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request)

        // Validation
        $request->validate([
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'photo' => 'sometimes|mimes:jpeg,jpg,png',
            'category' => 'required',
        ]);

        // File Upload
        if($request->hasfile('photo')){
            $photo = $request->file('photo');
            //$name = $photo->getClientOriginalName();
            $name = time().'.'.$photo->getClientOriginalExtension();
            $photo->move(public_path().'/storage/image/',$name);
            $photo = '/storage/image/'.$name;
        }else{
            $photo = request('oldphoto');
        }


        // Data Update
        $post = Post::find($id);    // import is $id beacuse without it, it will insert
        $post->title = request('title');
        $post->body = request('content');
        $post->image = $photo;
        $post->category_id = request('category');
        $post->user_id = Auth::id();;
        // $post->status = true;

        $post->save();

        // Redirect
        return redirect()->route('firstpage');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();

        //Redirect
        return redirect()->route('firstpage');
    }
}
