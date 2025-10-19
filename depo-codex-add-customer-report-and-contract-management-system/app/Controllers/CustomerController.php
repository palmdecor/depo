<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\AuthService;
use App\Services\CustomerService;
use App\Support\Flash;
use App\Support\Validator;
use App\Support\View;

class CustomerController
{
    private AuthService $auth;
    private CustomerService $customers;

    public function __construct(AuthService $auth, CustomerService $customers)
    {
        $this->auth = $auth;
        $this->customers = $customers;
    }

    public function index(): string
    {
        $this->requireAuth();

        $customers = $this->customers->listCustomers();

        return View::render('customer/index', [
            'customers' => $customers,
        ]);
    }

    public function create(): string
    {
        $this->requireAuth();

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        return View::render('customer/form', [
            'title' => 'Yeni Müşteri',
            'action' => '/customers',
            'method' => 'POST',
            'button' => 'Kaydet',
            'errors' => $errors,
            'customer' => $old,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        $data = Request::only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'domain_name',
            'domain_expires_at',
            'domain_renewal_period_months',
            'domain_price',
            'hosting_service',
            'hosting_expires_at',
            'hosting_renewal_period_months',
            'hosting_price',
            'notes',
        ]);

        $errors = $this->validateCustomer($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            Response::redirect('/customers/create');
        }

        $this->customers->create($data);
        Flash::success('Müşteri başarıyla oluşturuldu.');

        Response::redirect('/customers');
    }

    public function edit(): string
    {
        $this->requireAuth();

        $id = (int) Request::input('id');
        $customer = $this->customers->find($id);

        if (!$customer) {
            Flash::error('Müşteri bulunamadı.');
            Response::redirect('/customers');
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        if (!empty($old)) {
            $customer = array_merge($customer, $old);
        }

        return View::render('customer/form', [
            'title' => 'Müşteri Düzenle',
            'action' => '/customers/update',
            'method' => 'POST',
            'button' => 'Güncelle',
            'errors' => $errors,
            'customer' => $customer,
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();

        $id = (int) Request::input('id');
        $customer = $this->customers->find($id);

        if (!$customer) {
            Flash::error('Müşteri bulunamadı.');
            Response::redirect('/customers');
        }

        $data = Request::only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'domain_name',
            'domain_expires_at',
            'domain_renewal_period_months',
            'domain_price',
            'hosting_service',
            'hosting_expires_at',
            'hosting_renewal_period_months',
            'hosting_price',
            'notes',
        ]);

        $errors = $this->validateCustomer($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = array_merge($data, ['id' => $id]);
            Response::redirect('/customers/edit?id=' . $id);
        }

        $this->customers->update($id, $data);
        Flash::success('Müşteri bilgileri güncellendi.');

        Response::redirect('/customers');
    }

    public function destroy(): void
    {
        $this->requireAuth();

        $id = (int) Request::input('id');
        if ($id <= 0) {
            Flash::error('Geçersiz müşteri isteği.');
            Response::redirect('/customers');
        }

        $customer = $this->customers->find($id);
        if (!$customer) {
            Flash::error('Müşteri bulunamadı.');
            Response::redirect('/customers');
        }

        $this->customers->delete($id);
        Flash::success('Müşteri silindi.');

        Response::redirect('/customers');
    }

    private function validateCustomer(array $data): array
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'domain_name' => 'required',
            'domain_expires_at' => 'required|date_format:Y-m-d',
            'domain_renewal_period_months' => 'integer|min_value:1',
            'domain_price' => 'numeric|min_value:0',
            'hosting_service' => '',
            'hosting_expires_at' => 'date_format:Y-m-d',
            'hosting_renewal_period_months' => 'integer|min_value:1',
            'hosting_price' => 'numeric|min_value:0',
            'notes' => '',
        ];

        $rules = array_filter($rules, fn($value) => $value !== '');

        return Validator::validate($data, $rules);
    }

    private function requireAuth(): array
    {
        $user = $this->auth->user();
        if (!$user) {
            Response::redirect('/login');
        }

        return $user;
    }
}
