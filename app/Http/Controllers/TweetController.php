<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;

class TweetController extends Controller
{
    /**
     * Display a listing of the resource.
     * 一覧画面への移動
     */
    public function index()
    {
        //追加
        $tweets = Tweet::with(['user', 'liked'])->latest()->get();

        // ページネーションを追加（1ページに10件表示）
        $query = Tweet::query();
        $tweets = $query
            ->latest()
            ->paginate(10);

        return view('tweets.index', compact('tweets'));
    }

    /**
     * Show the form for creating a new resource.
     * 入力画面への移動
     */
    public function create()
    {
        //追加
        return view('tweets.create');
    }

    /**
     * Store a newly created resource in storage.
     * 入力されたデータを保存
     */
    public function store(Request $request)
    {
        // 正しいデータかどうかの確認（バリデーション）
        $request->validate([
            'tweet' => 'required|max:255',
        ]);

        $request->user()->tweets()->create($request->only('tweet'));

        return redirect()->route('tweets.index');
    }

    /**
     * Display the specified resource.
     * 詳細画面への移動
     */
    public function show(Tweet $tweet)
    {
        //
        $tweet->load('comments');
        return view('tweets.show', compact('tweet'));
    }

    /**
     * Show the form for editing the specified resource.
     * 編集画面への移動
     */
    public function edit(Tweet $tweet)
    {
        //
        return view('tweets.edit', compact('tweet'));
    }

    /**
     * Update the specified resource in storage.
     * 更新したデータを保存
     */
    public function update(Request $request, Tweet $tweet)
    {
        //正しいデータが入力されているかの確認
        $request->validate([
            'tweet' => 'required|max:255',
        ]);

        $tweet->update($request->only('tweet'));

        return redirect()->route('tweets.show', $tweet);
    }

    /**
     * Remove the specified resource from storage.
     * 入力されたデータを削除
     */
    public function destroy(Tweet $tweet)
    {
        //
        $tweet->delete();

        return redirect()->route('tweets.index');
    }


    /**
     * Search for tweets containing the keyword.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {

        $query = Tweet::query();

        // キーワードが指定されている場合のみ検索を実行
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('tweet', 'like', '%' . $keyword . '%');
        }

        // ページネーションを追加（1ページに10件表示）
        $tweets = $query
            ->latest()
            ->paginate(10);

        return view('tweets.search', compact('tweets'));
    }
}
