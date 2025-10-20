<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Registry;
use App\Models\DataRecord;
use App\Models\Transaction;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MemberController extends Controller
{
    private DataRecord $dataRecord;
    private Transaction $transactionModel;

    public function __construct()
    {
        parent::__construct();
        $this->ensureRole(['member']);
        $this->dataRecord = new DataRecord();
        $this->transactionModel = new Transaction();
    }

    public function dashboard(): void
    {
        $user = $this->auth->user();
        $records = $this->dataRecord->byMember($user['id']);
        $transactions = $this->transactionModel->byUser($user['id']);

        $this->view->render('member/dashboard.php', [
            'user' => $user,
            'records' => $records,
            'transactions' => $transactions,
        ]);
    }

    public function upload(): void
    {
        $user = $this->auth->user();
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_FILES['data_file']) || $_FILES['data_file']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Dosya yüklenemedi.';
            } else {
                $tmpName = $_FILES['data_file']['tmp_name'];
                $records = [];

                try {
                    $spreadsheet = IOFactory::load($tmpName);
                    $sheet = $spreadsheet->getActiveSheet();

                    foreach ($sheet->getRowIterator(2) as $row) {
                        $cells = [];
                        foreach ($row->getCellIterator() as $cell) {
                            $cells[] = trim((string)$cell->getValue());
                        }

                        if (count($cells) < 4) {
                            continue;
                        }

                        $records[] = [
                            'first_name' => $cells[0],
                            'last_name' => $cells[1],
                            'phone' => preg_replace('/\D+/', '', $cells[2]),
                            'category' => $cells[3],
                        ];
                    }

                    if ($records) {
                        $price = (float)(Registry::get('settings')->get('record_price', 10));
                        $this->dataRecord->createMany($records, $user['id'], $price);
                        $success = count($records) . ' kayıt yüklendi ve onay bekliyor.';
                    } else {
                        $error = 'Excel dosyasında uygun veri bulunamadı.';
                    }
                } catch (\Throwable $exception) {
                    $error = 'Excel dosyası okunurken hata oluştu: ' . $exception->getMessage();
                }
            }
        }

        $this->view->render('member/upload.php', compact('user', 'error', 'success'));
    }
}
