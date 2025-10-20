from datetime import datetime
from decimal import Decimal

from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy import select
from sqlalchemy.orm import Session

from ..database import get_db
from ..deps import require_role
from ..models import DataRecord, DataUpload, FirmTransaction, OperatorFirm, SystemSetting, User

router = APIRouter(prefix="/operator", tags=["operator"])


DEFAULT_ADMIN_COMMISSION = Decimal("0.20")
DEFAULT_MEMBER_COMMISSION = Decimal("0.80")


def _mask_phone(phone: str) -> str:
    if len(phone) <= 4:
        return "*" * len(phone)
    return phone[:2] + "*" * (len(phone) - 4) + phone[-2:]


def _get_commission(db: Session) -> tuple[Decimal, Decimal]:
    settings = {s.key: Decimal(s.value) for s in db.execute(select(SystemSetting)).scalars()}
    admin_commission = settings.get("admin_commission", DEFAULT_ADMIN_COMMISSION)
    member_commission = settings.get("member_commission", DEFAULT_MEMBER_COMMISSION)
    if admin_commission + member_commission != Decimal("1"):
        total = admin_commission + member_commission
        admin_commission = admin_commission / total
        member_commission = member_commission / total
    return admin_commission, member_commission


@router.get("/records/{upload_id}")
def list_records(
    upload_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_role(["operator"])),
):
    firm = db.query(OperatorFirm).filter(OperatorFirm.owner_user_id == current_user.id).first()
    if not firm:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Operator firm not found")

    upload = db.get(DataUpload, upload_id)
    if not upload or not upload.is_approved:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Upload not available")

    records = (
        db.query(DataRecord)
        .filter(DataRecord.upload_id == upload_id)
        .order_by(DataRecord.id.asc())
        .all()
    )

    response = []
    for record in records:
        phone = record.phone
        if record.sold_to_operator_id != firm.id:
            phone = _mask_phone(phone)
        response.append(
            {
                "id": record.id,
                "first_name": record.first_name,
                "last_name": record.last_name,
                "phone": phone,
                "category": record.category,
                "is_sold": record.is_sold,
            }
        )
    return response


@router.post("/purchase/{upload_id}")
def purchase_upload(
    upload_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_role(["operator"])),
):
    firm = db.query(OperatorFirm).filter(OperatorFirm.owner_user_id == current_user.id).first()
    if not firm:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Operator firm not found")

    upload = db.get(DataUpload, upload_id)
    if not upload or not upload.is_approved:
        raise HTTPException(status_code=status.HTTP_404_NOT_FOUND, detail="Upload not available")

    unsold_records = [record for record in upload.records if not record.is_sold]
    if not unsold_records:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="No available records to purchase")

    total_price = (upload.price_per_record * Decimal(len(unsold_records))).quantize(Decimal("0.01"))

    if current_user.balance < total_price:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Insufficient balance")

    admin_commission, member_commission = _get_commission(db)
    member_share = (total_price * member_commission).quantize(Decimal("0.01"))
    admin_share = (total_price * admin_commission).quantize(Decimal("0.01"))

    owner = upload.owner
    admin_user = db.query(User).filter(User.role == "admin").first()

    try:
        with db.begin():
            current_user.balance -= total_price

            for record in unsold_records:
                record.is_sold = True
                record.sold_to_operator_id = firm.id
                record.sold_at = datetime.utcnow()

            owner.balance = (owner.balance or Decimal("0")) + member_share
            if admin_user:
                admin_user.balance = (admin_user.balance or Decimal("0")) + admin_share

            transaction = FirmTransaction(
                operator_firm_id=firm.id,
                upload_id=upload.id,
                purchased_by_user_id=current_user.id,
                total_price=total_price,
                member_share=member_share,
                admin_share=admin_share,
            )
            db.add(transaction)
    except Exception as exc:  # pragma: no cover - guard for transactional issues
        db.rollback()
        raise HTTPException(status_code=status.HTTP_500_INTERNAL_SERVER_ERROR, detail="Purchase failed") from exc

    db.refresh(transaction)
    return {
        "transaction_id": transaction.id,
        "total_price": str(total_price),
        "records": len(unsold_records),
        "member_share": str(member_share),
        "admin_share": str(admin_share),
    }
