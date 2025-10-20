from functools import lru_cache
from pydantic import BaseSettings


class Settings(BaseSettings):
    database_url: str = "mysql+pymysql://datauser:gizlisifre@localhost:3306/datahub"
    jwt_secret_key: str = "supersecret"
    jwt_algorithm: str = "HS256"
    access_token_expire_minutes: int = 60 * 24

    class Config:
        env_file = ".env"
        env_file_encoding = "utf-8"


@lru_cache
def get_settings() -> Settings:
    return Settings()
