from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from .. import schemas
from ..database import get_db
from ..deps import get_current_user, require_role
from ..models import User

router = APIRouter(prefix="/users", tags=["users"])


@router.get("/me", response_model=schemas.UserResponse)
def read_me(current_user: User = Depends(get_current_user)):
    return current_user


@router.get("/", response_model=list[schemas.UserResponse])
def list_users(
    db: Session = Depends(get_db),
    _: User = Depends(require_role(["admin"])),
):
    return db.query(User).all()
