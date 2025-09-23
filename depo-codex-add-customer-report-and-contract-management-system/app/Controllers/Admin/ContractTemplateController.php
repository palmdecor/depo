<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Services\AuthService;
use App\Services\ContractService;
use App\Support\Csrf;
use App\Support\View;
use RuntimeException;

class ContractTemplateController extends Controller
{
    public function __construct(AuthService $auth, private ContractService $contracts)
    {
        parent::__construct($auth);
    }

    public function show(): string
    {
        $this->requireAdmin();
        $template = $this->contracts->getTemplate();
        $errors = $_SESSION['errors'] ?? [];
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['errors'], $_SESSION['success']);

        return View::render('admin/contract_template', [
            'template' => $template,
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    public function update(): void
    {
        if (!Csrf::verify(Request::input('_token'))) {
            http_response_code(419);
            exit('Geçersiz oturum doğrulaması.');
        }

        $this->requireAdmin();
        $title = trim((string) (Request::input('title') ?? ''));
        $body = (string) (Request::input('body') ?? '');

        try {
            $this->contracts->saveTemplate($title, $body);
            $_SESSION['success'] = 'Sözleşme şablonu güncellendi.';
        } catch (RuntimeException $exception) {
            $_SESSION['errors'] = ['general' => [$exception->getMessage()]];
        }

        Response::redirect('/admin/contract-template');
    }
}
