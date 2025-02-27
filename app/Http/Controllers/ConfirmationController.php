<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class ConfirmationController extends Controller
{
    public function show(Request $request)
    {
        // 各フォームのデータをセッションに保存
        session([
            'judgment' => $request->only([
                'conflict',
                'public_servant',
                'licensed_professional',
                'contractor',
                'foreign_trainee',
                'mental_illness',
                'trouble'
            ]),
            'form' => $request->only([
                'name',
                'name_furigana',
                'gender',
                'birth_date',
                'age',
                'line_name',
                'postal_code',
                'prefecture',
                'address',
                'residence',
                'contact_email',
                'contact_phone',
                'current_status',
                'desired_resignation_date',
                'final_work_date',
                'paid_leave_preference',
                'company_name',
                'work_postal_code',
                'work_prefecture',
                'work_address',
                'work_email',
                'work_contact_phone',
                'work_superior_phone',
                'employment_type',
                'job_type',
                'years_of_service',
                'bank_name',
                'account_type',
                'account_number',
                'resignation_contact'
            ]),
            'consent' => $request->only(['consent'])
        ]);

        return view('confirmation', [
            'judgment' => session('judgment'),
            'form' => session('form'),
            'consent' => session('consent')
        ]);
    }
    public function generatePdf()
    {
        $options = new Options();
        $options->set('defaultFont', 'ipaexg'); // ← 日本語フォントを明示的に指定
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', storage_path());

        // Dompdf インスタンスを作成
        $dompdf1 = new Dompdf($options);
        $dompdf2 = new Dompdf($options);

        // セッションからデータを取得
        $formData = session()->get('form', []);

        // セッションデータがない場合の処理
        if (empty($formData)) {
            return back()->with('error', 'セッションが切れています。もう一度入力してください。');
        }

        // ======= 送達文フォーマットPDF生成 =======
        $html1 = '<html><head><meta charset="UTF-8">
            <style> 
                body { font-family: "ipaexg"; font-size: 14px; }
                h1 { font-size: 20px; font-weight: bold; text-align: center; }
                p { font-size: 14px; line-height: 1.5; margin-bottom: 10px; }
            </style>
            </head><body>
            <h1>送達文フォーマット</h1>
            <p>会社名: ' . ($formData['company_name'] ?? '会社名') . '</p>
            <p>担当者: ' . ($formData['resignation_contact'] ?? '担当者') . ' 様</p>
            <p>前略</p>
            <p>退職代行モーアカン®と申します。</p>
            <p>この度は，御社に勤務中の ' . ($formData['name'] ?? '従業員名') . ' 様よりご依頼を受けてご連絡を差し上げました。</p>
            <p>添付ファイルの退職届の通り御社へ御伝達申し上げます。</p>
            <p>草々</p>
            <p>記</p>
            <p>http://localhost/login</p>
            <p>以上</p>
            </body></html>';

        $dompdf1->loadHtml($html1);
        $dompdf1->setPaper('A4', 'portrait');
        $dompdf1->render();
        $pdf1Path = storage_path('app/public/送達文フォーマット.pdf');
        file_put_contents($pdf1Path, $dompdf1->output());

        // ======= 退職届フォーマットPDF生成 =======
        $html2 = '<html><head><meta charset="UTF-8">
            <style> 
                body { font-family: "ipaexg"; font-size: 14px; }
                h1 { font-size: 20px; font-weight: bold; text-align: center; }
                p { font-size: 14px; line-height: 1.5; margin-bottom: 10px; }
            </style>
            </head><body>
            <h1>退職届フォーマット</h1>
            <p>会社名: ' . ($formData['company_name'] ?? '会社名') . '</p>
            <p>担当者: ' . ($formData['resignation_contact'] ?? '担当者') . ' 様</p>
            <p>前略</p>
            <p>私、' . ($formData['name'] ?? '従業員名') . ' は一身上の都合により、' . ($formData['desired_resignation_date'] ?? '退職日') . ' をもちまして退職いたしたく、ここに届出いたします。</p>
            <p>なお、「離職票」及び「給与所得者の源泉徴収票」並びに「社会保険資格喪失証明書」のご依頼をいたしますので、上記住所宛てにお手配のほどよろしくお願いいたします。</p>
            <p>併せて、給与のお振込先は以下の通りですのでお振込の程よろしくお願い申し上げます。</p>
            <p>在職中は格別のご厚情を賜り、誠にありがとうございました。</p>
            <p>貴社のますますのご発展をお祈り申し上げます。</p>
            <p>敬具</p>
            <p>記</p>
            <p>以上</p>
            </body></html>';

        $dompdf2->loadHtml($html2);
        $dompdf2->setPaper('A4', 'portrait');
        $dompdf2->render();
        $pdf2Path = storage_path('app/public/退職届フォーマット.pdf');
        file_put_contents($pdf2Path, $dompdf2->output());

        // ======= ZIPファイルに圧縮 =======
        $zipPath = storage_path('app/public/フォーマット.zip');
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            $zip->addFile($pdf1Path, '送達文フォーマット.pdf');
            $zip->addFile($pdf2Path, '退職届フォーマット.pdf');
            $zip->close();
        } else {
            return back()->with('error', 'ZIPファイルの作成に失敗しました');
        }

        // ZIPをダウンロード
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
