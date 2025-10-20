from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

from app.database import Base, engine
from app.routers import admin, auth, operator, operator_firms, uploads, users

Base.metadata.create_all(bind=engine)

app = FastAPI(title="DataHub API")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(auth.router)
app.include_router(users.router)
app.include_router(admin.router)
app.include_router(operator_firms.router)
app.include_router(uploads.router)
app.include_router(operator.router)


@app.get("/")
def root():
    return {"message": "DataHub API running"}
