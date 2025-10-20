from datetime import datetime
from decimal import Decimal
from typing import List, Optional

from pydantic import BaseModel, EmailStr


class Token(BaseModel):
    access_token: str
    token_type: str = "bearer"


class TokenData(BaseModel):
    user_id: int
    role: str


class UserBase(BaseModel):
    email: EmailStr
    full_name: str
    role: str
    balance: Decimal

    class Config:
        orm_mode = True


class UserCreate(BaseModel):
    email: EmailStr
    full_name: str
    password: str
    role: str = "user"


class UserResponse(UserBase):
    id: int
    created_at: datetime


class OperatorFirmBase(BaseModel):
    name: str


class OperatorFirmCreate(OperatorFirmBase):
    owner_user_id: int


class OperatorFirmResponse(OperatorFirmBase):
    id: int
    balance: Decimal
    owner_user_id: int
    created_at: datetime

    class Config:
        orm_mode = True


class DataRecordBase(BaseModel):
    first_name: str
    last_name: str
    phone: str
    category: Optional[str] = None


class DataRecordResponse(DataRecordBase):
    id: int
    is_sold: bool

    class Config:
        orm_mode = True


class DataUploadBase(BaseModel):
    title: str
    description: Optional[str] = None
    price_per_record: Decimal


class DataUploadCreate(DataUploadBase):
    records: List[DataRecordBase]


class DataUploadResponse(DataUploadBase):
    id: int
    user_id: int
    is_approved: bool
    total_records: int
    created_at: datetime

    class Config:
        orm_mode = True


class FirmTransactionResponse(BaseModel):
    id: int
    operator_firm_id: int
    upload_id: int
    total_price: Decimal
    member_share: Decimal
    admin_share: Decimal
    created_at: datetime

    class Config:
        orm_mode = True


class SettingUpdate(BaseModel):
    key: str
    value: str


class SettingResponse(BaseModel):
    key: str
    value: str

    class Config:
        orm_mode = True


class LoginRequest(BaseModel):
    email: EmailStr
    password: str
