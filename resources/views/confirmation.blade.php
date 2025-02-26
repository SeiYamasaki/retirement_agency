@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">入力内容の確認</h2>
        <p>以下の内容で問題がなければ「送信」ボタンを押してください。</p>

        <h3 class="text-primary">判定フォームの入力内容</h3>
        <table class="table table-bordered">
            @foreach (session('judgment', []) as $key => $value)
                <tr>
                    <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </table>

        <h3 class="text-success">情報入力フォームの入力内容</h3>
        <table class="table table-bordered">
            @foreach (session('form', []) as $key => $value)
                <tr>
                    <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </table>

        <h3 class="text-warning">同意フォーム</h3>
        @php
            $consent = session('consent', []);
        @endphp
        <p>個人情報取扱いの同意及び合意: <strong>{{ isset($consent['consent']) && $consent['consent'] == 1 ? '同意及び合意済み' : '未同意及び未合意' }}</strong></p>

        <form action="{{ route('confirmation.submitFinal') }}" method="POST">
            @csrf
            <div class="text-center mt-4">
                <a href="{{ route('judgment.show') }}" class="btn btn-secondary">判定フォームを修正</a>
                <a href="{{ route('form.show') }}" class="btn btn-secondary">情報入力を修正</a>
                <a href="{{ route('consent.show') }}" class="btn btn-secondary">同意内容を修正</a>
                <button type="submit" class="btn btn-primary">送信</button>
            </div>
        </form>
    </div>
@endsection
