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
        $options->set('defaultFont', 'Mplus 1p');

        // セッションからデータを取得
        $formData = session()->get('form', []);

        // セッションデータがない場合の処理
        if (empty($formData)) {
            return back()->with('error', 'セッションが切れています。もう一度入力してください。');
        }

        // ======= 送達文フォーマットPDF生成 =======
        $dompdf1 = new Dompdf($options);
        $html1 = '<h1>送達文フォーマット</h1>
            <p>' . ($formData['company_name'] ?? '会社名') . '</p>
            <p>' . ($formData['resignation_contact'] ?? '担当者') . '様</p>';

        $dompdf1->loadHtml($html1);
        $dompdf1->setPaper('A4', 'portrait');
        $dompdf1->render();
        $pdf1Path = storage_path('app/public/送達文フォーマット.pdf');
        file_put_contents($pdf1Path, $dompdf1->output());

        // ======= 退職届フォーマットPDF生成 =======
        $dompdf2 = new Dompdf($options);
        $html2 = '<h1>退職届フォーマット</h1>
            <p>' . ($formData['company_name'] ?? '会社名') . '</p>
            <p>' . ($formData['resignation_contact'] ?? '担当者') . '様</p>';

        $dompdf2->loadHtml($html2);
        $dompdf2->setPaper('A4', 'portrait');
        $dompdf2->render();
        $pdf2Path = storage_path('app/public/退職届フォーマット.pdf');
        file_put_contents($pdf2Path, $dompdf2->output());

        // ======= ZIPファイルに圧縮 =======
        $zipPath = storage_path('app/public/勤務先に送信されるデータ一式.zip');
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
