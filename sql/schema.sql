CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS credit_profiles (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    income DECIMAL(12,2) NOT NULL,
    credit_type VARCHAR(50) NOT NULL,
    term INTEGER NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    ai_score INTEGER,
    recommendations TEXT,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS idx_credit_profiles_user ON credit_profiles (user_id);
