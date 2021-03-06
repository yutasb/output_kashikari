<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Kashikari;
use App\Message;



use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KashikariController extends Controller
{
    public function new()
    {
        return view('kashikari.new');
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'place' => 'required|string|max:255',
            'price' => 'required|integer',
            'comment' => 'required|string|max:255',
        ]);
        $kashikari = new Kashikari;
        $kashikari->title = $request->title;
        $kashikari->category_id = $request->category_id;
        $kashikari->place = $request->place;
        $kashikari->price = $request->price;
        $kashikari->comment = $request->comment;
        $kashikari->method_id = $request->method_id;

        //s3アップロード開始
        $pic1upload = $kashikari->pic1 = $request->file('pic1');
        // バケットの`myprefix`フォルダへアップロード。第一引数はバゲット内の階層。第二引数は受け取った画像が入った変数。第三引数は原則publicを指定（ファイルのURLによるアクセスが可能に）。
        $path = Storage::disk('s3')->putFile('myprefix', $pic1upload, 'public');
        // アップロードした画像のフルパスを取得
        $kashikari->pic1 = Storage::disk('s3')->url($path);

        if ($request->file('pic2')) {
            $pic2upload = $kashikari->pic2 = $request->file('pic2');
            $path = Storage::disk('s3')->putFile('myprefix', $pic2upload, 'public');
            $kashikari->pic2 = Storage::disk('s3')->url($path);
        }

        if ($request->file('pic3')) {
            $pic3upload = $kashikari->pic3 = $request->file('pic3');
            $path = Storage::disk('s3')->putFile('myprefix', $pic3upload, 'public');
            $kashikari->pic3 = Storage::disk('s3')->url($path);
        }

        $kashikari->user_id = Auth::user()->id;
        $kashikari->save();


        return redirect('/lent')->with('flash_message', __('Registered.'));
    }

    public function index()
    {
        $kashikaris = Kashikari::all();
        // 全レコードを取得
        return view('kashikari.index', ['kashikaris' => $kashikaris]);
        //index.blade.phpの$kashikaris部分が、$kashikari(今回の場合Kashikari::all)に置き換えられる。

    }

    public function show($id)
    {
        if (!\ctype_digit($id)) {
            return redirect('/lent/new')->with('flash_message', __('Invalid operation was perfomed.'));
        }
        $kashikari = Kashikari::find($id);
        $messages = Message::where('kashikari_id', $id)->get();

        return view('kashikari.show', ['kashikari' => $kashikari], ['messages' => $messages]);
    }


    public function mypage()
    {
        $user = Auth::user();
        $kashikaris = Kashikari::get();

        $path = Storage::disk('s3')->url('/');

        return view('kashikari.mypage', ['kashikaris' => $kashikaris, 'user' => $user, 'pic' => $path,]);
        // str_replace("検索を行う文字列", "置き換えを行う文字列", "対象の文字列");
    }

    public function edit($id)
    {
        if (!\ctype_digit($id)) {
            return redirect('/lent/new')->with('flash_message', __('Invalid operation was perfomed'));
        }
        $kashikari = Auth::user()->kashikaris()->find($id);
        return view('kashikari.edit', ['kashikari' => $kashikari,]);
    }

    public function update(Request $request, $id)
    {
        if (!\ctype_digit($id)) {
            return redirect('/lent/new')->with('flash_message', __('Invalid operation was perfomed'));
        }
        $kashikari = Kashikari::find($id);

        $kashikari->title = $request->title;
        $kashikari->category_id = $request->category_id;
        $kashikari->place = $request->place;
        $kashikari->price = $request->price;
        $kashikari->comment = $request->comment;
        $kashikari->method_id = $request->method_id;

        if ($request->file('pic1')) {

            $pic1upload = $kashikari->pic1 = $request->file('pic1');
            $path = Storage::disk('s3')->putFile('myprefix', $pic1upload, 'public');
            $kashikari->pic1 = Storage::disk('s3')->url($path);
        }

        if ($request->file('pic2')) {

            $pic2upload = $kashikari->pic2 = $request->file('pic2');
            $path = Storage::disk('s3')->putFile('myprefix', $pic2upload, 'public');
            $kashikari->pic2 = Storage::disk('s3')->url($path);
        }

        if ($request->file('pic3')) {

            $pic3upload = $kashikari->pic3 = $request->file('pic3');
            $path = Storage::disk('s3')->putFile('myprefix', $pic3upload, 'public');
            $kashikari->pic3 = Storage::disk('s3')->url($path);
        }

        $kashikari->user_id = Auth::user()->id;
        $kashikari->save();
        return redirect('/lent')->with('flash_message', __('Updated'));
    }

    public function delete($id)
    {
        if (!ctype_digit($id)) {
            return redirect('/lent/new')->with('flash_message', __('Invalid operation was perfomed'));
        }
        Auth::user()->kashikaris()->find($id)->delete();
        return redirect('/mypage')->with('flash_message', __('Deleted.'));
    }

    public function getCategory()
    {
        $categories = config('category');
        return view('kashikari.new')->with(['categories' => $categories]);
    }

    public function getCategoryEdit()
    {
        $categories = config('category');
        return view('kashikari.edit')->with(['categories' => $categories]);
    }
}
