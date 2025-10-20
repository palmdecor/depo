from datetime import datetime
from decimal import Decimal

from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session

from .. import schemas
from ..database import get_db
from ..deps import require_role
from ..models import DataUpload, SystemSetting, User

router = APIRouter(prefix="/admin", tags=["admin"])


@router.post("/settings", response_model=schemas.SettingResponse)
def upsert_setting(
    payload: schemas.SettingUpdate,
    db: Session = Depends(get_db),
    _: User = Depends(require_role(["admin"])),
):
    setting = db.query(SystemSetting).filter(SystemSetting.key == payload.key).one_or_none()
    if setting:
        setting.value = payload.value
    else:
        setting = SystemSetting(key=payload.key, value=payload.value)
        db.add(setting)
    db.commit()
    db.refresh(setting)
    return setting


@router.post("/users/{user_id}/balance", response_model=schemas.UserResponse)
def update_user_balance(
    user_id: int,
    amount: Decimal,
    db: Session = Depends(get_db),
    _: User = Depends(require_role(["admin"])),
):
    user = db.get(User, user_id)
    if not user:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="User not found")
    user.balance = amount
    db.commit()
    db.refresh(user)
    return user


@router.post("/uploads/{upload_id}/approve", response_model=schemas.DataUploadResponse)
def approve_upload(
    upload_id: int,
    db: Session = Depends(get_db),
    _: User = Depends(require_role(["admin"])),
):
    upload = db.get(DataUpload, upload_id)
    if not upload:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Upload not found")
    upload.is_approved = True
    upload.approved_at = datetime.utcnow()
    db.commit()
    db.refresh(upload)
    return upload
