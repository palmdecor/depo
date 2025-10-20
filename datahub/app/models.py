from datetime import datetime
from enum import Enum as PyEnum

from sqlalchemy import (
    Boolean,
    Column,
    DateTime,
    ForeignKey,
    Integer,
    Numeric,
    String,
    Text,
    UniqueConstraint,
)
from sqlalchemy.orm import relationship

from .database import Base


class UserRole(str, PyEnum):  # type: ignore[misc]
    ADMIN = "admin"
    OPERATOR = "operator"
    USER = "user"


class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, index=True)
    email = Column(String(255), unique=True, index=True, nullable=False)
    full_name = Column(String(255), nullable=False)
    hashed_password = Column(String(255), nullable=False)
    role = Column(String(50), nullable=False, default=UserRole.USER.value)
    balance = Column(Numeric(12, 2), nullable=False, default=0)
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)

    operator_firm = relationship("OperatorFirm", back_populates="owner", uselist=False)
    uploads = relationship("DataUpload", back_populates="owner")
    purchases = relationship("FirmTransaction", back_populates="purchased_by")


class OperatorFirm(Base):
    __tablename__ = "operator_firms"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), unique=True, nullable=False)
    owner_user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    balance = Column(Numeric(12, 2), nullable=False, default=0)
    created_at = Column(DateTime, default=datetime.utcnow)

    owner = relationship("User", back_populates="operator_firm")
    transactions = relationship("FirmTransaction", back_populates="operator_firm")


class DataUpload(Base):
    __tablename__ = "data_uploads"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    title = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    price_per_record = Column(Numeric(12, 2), nullable=False, default=0)
    is_approved = Column(Boolean, default=False)
    total_records = Column(Integer, default=0)
    approved_at = Column(DateTime, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)

    owner = relationship("User", back_populates="uploads")
    records = relationship("DataRecord", back_populates="upload", cascade="all, delete-orphan")
    transactions = relationship("FirmTransaction", back_populates="upload")


class DataRecord(Base):
    __tablename__ = "data_records"
    __table_args__ = (
        UniqueConstraint("upload_id", "phone", name="uq_record_phone_per_upload"),
    )

    id = Column(Integer, primary_key=True, index=True)
    upload_id = Column(Integer, ForeignKey("data_uploads.id"), nullable=False)
    first_name = Column(String(255), nullable=False)
    last_name = Column(String(255), nullable=False)
    phone = Column(String(50), nullable=False)
    category = Column(String(255), nullable=True)
    is_sold = Column(Boolean, default=False)
    sold_to_operator_id = Column(Integer, ForeignKey("operator_firms.id"), nullable=True)
    sold_at = Column(DateTime, nullable=True)

    upload = relationship("DataUpload", back_populates="records")
    sold_to_operator = relationship("OperatorFirm")


class FirmTransaction(Base):
    __tablename__ = "firm_transactions"

    id = Column(Integer, primary_key=True, index=True)
    operator_firm_id = Column(Integer, ForeignKey("operator_firms.id"), nullable=False)
    upload_id = Column(Integer, ForeignKey("data_uploads.id"), nullable=False)
    purchased_by_user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    total_price = Column(Numeric(12, 2), nullable=False)
    member_share = Column(Numeric(12, 2), nullable=False)
    admin_share = Column(Numeric(12, 2), nullable=False)
    created_at = Column(DateTime, default=datetime.utcnow)

    operator_firm = relationship("OperatorFirm", back_populates="transactions")
    upload = relationship("DataUpload", back_populates="transactions")
    purchased_by = relationship("User", back_populates="purchases")


class SystemSetting(Base):
    __tablename__ = "system_settings"

    id = Column(Integer, primary_key=True, index=True)
    key = Column(String(255), unique=True, nullable=False)
    value = Column(String(255), nullable=False)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
