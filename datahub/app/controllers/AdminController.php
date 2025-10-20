<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Registry;
use App\Models\DataRecord;
use App\Models\Transaction;
use App\Models\User;

class AdminController extends Controller
{
    private DataRecord $dataRecord;
    private User $userModel;
    private Transaction $transactionModel;

    public function __construct()
    {
        parent::__construct();
        $this->ensureRole(['admin']);
        $this->dataRecord = new DataRecord();
        $this->userModel = new User();
        $this->transactionModel = new Transaction();
    }

    public function dashboard(): void
    {
        $pending = $this->dataRecord->byStatus('pending');
        $approved = $this->dataRecord->byStatus('approved');
        $sold = $this->dataRecord->byStatus('sold');

        $this->view->render('admin/dashboard.php', [
            'pendingCount' => count($pending),
            'approvedCount' => count($approved),
            'soldCount' => count($sold),
        ]);
    }

    public function users(): void
    {
        $users = $this->userModel->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $action = $_POST['action'] ?? '';

            if ($userId && $action === 'balance') {
                $amount = (float)($_POST['amount'] ?? 0);
                $this->userModel->incrementBalance($userId, $amount);
                $this->transactionModel->log([
                    'buyer_id' => $userId,
                    'seller_id' => $this->auth->user()['id'],
                    'amount' => $amount,
                    'commission' => 0,
                    'type' => 'admin_balance_topup',
                    'description' => 'Admin bakiye yüklemesi',
                ]);
            }

            if ($userId && $action === 'role') {
                $role = $_POST['role'] ?? 'member';
                $this->userModel->updateRole($userId, $role);
            }

            $this->redirect('admin/users');
        }

        $this->view->render('admin/users.php', compact('users'));
    }

    public function data(): void
    {
        $pending = $this->dataRecord->byStatus('pending');
        $approved = $this->dataRecord->byStatus('approved');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recordId = (int)($_POST['record_id'] ?? 0);
            $action = $_POST['action'] ?? '';

            if ($recordId && $action === 'approve') {
                $this->dataRecord->approve($recordId);
            }

            if ($recordId && $action === 'reject') {
                $this->dataRecord->reject($recordId);
            }

            $this->redirect('admin/data');
        }

        $this->view->render('admin/data.php', compact('pending', 'approved'));
    }

    public function settings(): void
    {
        $settings = Registry::get('settings');
        $commission = $settings->get('commission_rate', Registry::get('config')['default_commission']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commissionRate = (float)($_POST['commission_rate'] ?? 0);
            $settings->set('commission_rate', $commissionRate);
            $this->redirect('admin/settings');
        }

        $this->view->render('admin/settings.php', compact('commission'));
    }
}
