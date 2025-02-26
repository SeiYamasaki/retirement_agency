<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '情報入力フォーム')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #e6ffe6;
            /* 背景を薄い緑 */
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            width: 100%;
        }

        /* ナビゲーションバー中央寄せ */
        .navbar {
            width: 100%;
            justify-content: center;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            text-align: center;
            margin: auto;
        }

        /* 📌 テーブルデザインの調整 */
        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            /* デフォルトではカラム幅を自動調整 */
        }

        /* 📌 th, td の基本デザイン */
        .table-bordered th,
        .table-bordered td {
            padding: 8px;
            font-size: 15px;
            border: 1px solid #ddd;
            text-align: left;
            white-space: nowrap;
            /* デフォルトでは折り返しなし */
        }

        /* 📌 スマホ対応（画面幅に収める） */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
                /* 横スクロールを許可 */
                -webkit-overflow-scrolling: touch;
            }

            .table-bordered {
                table-layout: auto;
                /* スマホではカラム幅を自動調整 */
                width: 100%;
            }

            .table-bordered th,
            .table-bordered td {
                white-space: normal;
                /* スマホでは折り返す */
                word-wrap: break-word;
            }

            /* 📌 スマホでは `th` を上、`td` を下に配置 */
            .table-bordered tbody tr {
                display: flex;
                flex-direction: column;
                padding: 5px;
            }

            .table-bordered th {
                background-color: #f8f9fa;
                font-weight: bold;
                text-align: left;
                padding-top: 10px;
            }

            .table-bordered td {
                padding-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container d-flex justify-content-center">
            <a class="navbar-brand mx-auto" href="#">退職代行モーアカン®</a>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
