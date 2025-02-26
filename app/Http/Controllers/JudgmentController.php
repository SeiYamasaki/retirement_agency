<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JudgmentController extends Controller
{
    public function show()
    {
        return view('judgment');
    }

    public function submit(Request $request)
    {
        $responses = $request->all();

        // Yesが1つでもあれば、お引き受けできませんのページへ
        foreach ($responses as $key => $value) {
            if ($value === 'yes') {
                return redirect()->route('denied');
            }
        }

        // セッションにデータを保存
        session(['judgment' => $responses]);

        // すべてNoなら情報入力フォームページへ遷移
        return redirect()->route('form.show');
    }
}
