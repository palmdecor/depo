<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Registry;
use App\Models\DataRecord;
use App\Models\Transaction;
use App\Models\User;
use PDO;
use Throwable;

class OperatorController extends Controller
{
    private DataRecord $dataRecord;
    private Transaction $transactionModel;
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->ensureRole(['operator']);
        $this->dataRecord = new DataRecord();
        $this->transactionModel = new Transaction();
        $this->userModel = new User();
    }

    public function dashboard(): void
    {
        $user = $this->auth->user();
        $available = $this->dataRecord->getAvailable();
        $transactions = $this->transactionModel->byUser($user['id']);
        $purchased = $this->dataRecord->purchasedByOperator($user['id']);

        $this->view->render('operator/dashboard.php', [
            'user' => $user,
            'available' => $available,
            'transactions' => $transactions,
            'purchased' => $purchased,
        ]);
    }

    public function purchase(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('operator/dashboard');
        }

        $recordId = (int)($_POST['record_id'] ?? 0);
        $user = $this->auth->user();
        $record = $this->dataRecord->findById($recordId);

        if (!$record || $record['status'] !== 'approved' || $record['sold_to']) {
            $this->redirect('operator/dashboard');
        }

        $settings = Registry::get('settings');
        $commissionRate = (float)$settings->get('commission_rate', Registry::get('config')['default_commission']);
        $price = (float)($record['price'] ?? 0);
        $commissionAmount = $price * $commissionRate;
        $memberAmount = max($price - $commissionAmount, 0);

        if ($user['balance'] < $price) {
            $this->redirect('operator/dashboard');
        }

        /** @var PDO $pdo */
        $pdo = Registry::get('db');
        $pdo->beginTransaction();

        try {
            $this->userModel->decrementBalance($user['id'], $price);
            $this->userModel->incrementBalance((int)$record['member_id'], $memberAmount);

            $admin = $this->findAdmin();
            if ($admin) {
                $this->userModel->incrementBalance($admin['id'], $commissionAmount);
            }

            $this->dataRecord->markAsSold($recordId, $user['id'], $price, $commissionAmount);

            $this->transactionModel->log([
                'buyer_id' => $user['id'],
                'seller_id' => $record['member_id'],
                'record_id' => $recordId,
                'amount' => $price,
                'commission' => $commissionAmount,
                'type' => 'purchase',
                'description' => 'Operatör satın alması',
            ]);

            if ($admin) {
                $this->transactionModel->log([
                    'buyer_id' => $user['id'],
                    'seller_id' => $admin['id'],
                    'record_id' => $recordId,
                    'amount' => $commissionAmount,
                    'commission' => 0,
                    'type' => 'commission',
                    'description' => 'Admin komisyonu',
                ]);
            }

            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }

        $this->redirect('operator/dashboard');
    }

    private function findAdmin(): ?array
    {
        $stmt = Registry::get('db')->query("SELECT * FROM users WHERE role = 'admin' ORDER BY id ASC LIMIT 1");
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        return $admin ?: null;
    }
}
