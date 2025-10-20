from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.exc import IntegrityError
from sqlalchemy.orm import Session

from .. import schemas
from ..database import get_db
from ..deps import require_role
from ..models import OperatorFirm, User

router = APIRouter(prefix="/operator-firms", tags=["operator_firms"])


@router.post("/", response_model=schemas.OperatorFirmResponse, status_code=status.HTTP_201_CREATED)
def create_operator_firm(
    payload: schemas.OperatorFirmCreate,
    db: Session = Depends(get_db),
    _: User = Depends(require_role(["admin"])),
):
    owner = db.get(User, payload.owner_user_id)
    if not owner or owner.role != "operator":
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Owner must be an operator user")

    firm = OperatorFirm(name=payload.name, owner_user_id=payload.owner_user_id)
    db.add(firm)
    try:
        db.commit()
    except IntegrityError as exc:
        db.rollback()
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Firm name already exists") from exc
    db.refresh(firm)
    return firm


@router.get("/", response_model=list[schemas.OperatorFirmResponse])
def list_firms(
    db: Session = Depends(get_db),
    _: User = Depends(require_role(["admin"])),
):
    return db.query(OperatorFirm).all()
