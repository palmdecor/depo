<?php

namespace App\Controllers\Customer;

use App\Controllers\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Services\AuthService;
use App\Services\ContractService;
use App\Support\Csrf;
use App\Support\Validator;
use App\Support\View;

class ContractController extends Controller
{
    public function __construct(AuthService $auth, private ContractService $contracts)
    {
        parent::__construct($auth);
    }

    public function show(): string
    {
        $user = $this->requireUser();
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['contract_old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['contract_old']);

        $template = $this->contracts->getTemplate();

        return View::render('customer/contract', [
            'user' => $user,
            'template' => $template,
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function generate(): void
    {
        if (!Csrf::verify(Request::input('_token'))) {
            http_response_code(419);
            exit('Geçersiz oturum doğrulaması.');
        }

        $user = $this->requireUser();
        $template = $this->contracts->getTemplate();

        if (!$template) {
            $_SESSION['errors'] = ['general' => ['Henüz sözleşme şablonu tanımlanmadı. Lütfen daha sonra tekrar deneyiniz.']];
            Response::redirect('/customer/contract');
        }

        $data = Request::only([
            'first_name',
            'last_name',
            'address',
            'national_id',
            'phone',
            'email',
            'agree',
        ]);

        $errors = Validator::validate($data, [
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'national_id' => 'required|digits:11',
            'phone' => 'required',
            'email' => 'required|email',
        ]);

        if (empty($data['agree'])) {
            $errors['agree'][] = 'Sözleşmeyi onaylamanız gerekmektedir.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['contract_old'] = $data;
            Response::redirect('/customer/contract');
        }

        $payload = [
            'first_name' => trim((string) $data['first_name']),
            'last_name' => trim((string) $data['last_name']),
            'address' => trim((string) $data['address']),
            'national_id' => preg_replace('/\D+/', '', (string) $data['national_id']),
            'phone' => trim((string) $data['phone']),
            'email' => strtolower(trim((string) $data['email'])),
        ];

        unset($_SESSION['contract_old']);

        try {
            $this->contracts->recordSubmission((int) $user['id'], $payload);
            $html = $this->contracts->renderTemplate($template['body'], $payload);
            $fileName = 'sozlesme-' . date('Ymd-His') . '.pdf';
            $pdf = $this->contracts->generatePdf($html, $fileName);
        } catch (\RuntimeException $exception) {
            $_SESSION['errors'] = ['general' => [$exception->getMessage()]];
            $_SESSION['contract_old'] = $data;
            Response::redirect('/customer/contract');
        }

        Response::pdf($pdf, $fileName);
    }
}
