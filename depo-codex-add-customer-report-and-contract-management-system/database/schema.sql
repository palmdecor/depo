CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    phone TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'customer',
    is_blocked INTEGER NOT NULL DEFAULT 0,
    last_login_at TEXT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT NOT NULL,
    domain_name TEXT NOT NULL,
    domain_expires_at TEXT NOT NULL,
    domain_renewal_period_months INTEGER NULL,
    domain_price REAL NULL,
    hosting_service TEXT NULL,
    hosting_expires_at TEXT NULL,
    hosting_renewal_period_months INTEGER NULL,
    hosting_price REAL NULL,
    notes TEXT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_customers_domain_expires_at ON customers (domain_expires_at);
CREATE INDEX IF NOT EXISTS idx_customers_hosting_expires_at ON customers (hosting_expires_at);
CREATE INDEX IF NOT EXISTS idx_customers_email ON customers (email);
