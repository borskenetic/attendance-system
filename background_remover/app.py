from fastapi import FastAPI, UploadFile, File, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from rembg import remove, new_session
from PIL import Image, ImageFilter, ImageOps
import numpy as np
import base64
import io

session = new_session("isnet-general-use")
MAX_PROCESSING_EDGE = 1800
MIN_OUTPUT_EDGE = 1200


app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.get("/health")
def health():
    return {"ok": True}


def prepare_input_image(input_bytes: bytes) -> bytes:
    image = Image.open(io.BytesIO(input_bytes))
    image = ImageOps.exif_transpose(image).convert("RGBA")

    if max(image.size) > MAX_PROCESSING_EDGE:
        image.thumbnail((MAX_PROCESSING_EDGE, MAX_PROCESSING_EDGE), Image.Resampling.LANCZOS)

    buffer = io.BytesIO()
    image.save(buffer, format="PNG")

    return buffer.getvalue()


def enhance_subject(subject: Image.Image) -> Image.Image:
    if max(subject.size) < MIN_OUTPUT_EDGE:
        scale = MIN_OUTPUT_EDGE / max(subject.size)
        subject = subject.resize(
            (int(subject.width * scale), int(subject.height * scale)),
            Image.Resampling.LANCZOS,
        )

    alpha = subject.getchannel("A")
    sharpened = subject.convert("RGB").filter(
        ImageFilter.UnsharpMask(radius=1.2, percent=125, threshold=3)
    )
    sharpened.putalpha(alpha)

    return sharpened


@app.post("/remove-bg")
async def remove_bg_endpoint(photo: UploadFile = File(...)):
    if not photo.filename:
        raise HTTPException(status_code=400, detail="No photo provided")

    input_bytes = await photo.read()
    prepared_bytes = prepare_input_image(input_bytes)
    output_bytes = remove(
        prepared_bytes,
        session=session,
        alpha_matting=True,
        alpha_matting_foreground_threshold=240,
        alpha_matting_background_threshold=10,
    )
    subject = Image.open(io.BytesIO(output_bytes)).convert("RGBA")

    # Remove gray residue left by background removal
    data = np.array(subject)
    r, g, b, a = data[..., 0], data[..., 1], data[..., 2], data[..., 3]
    gray_diff = (np.abs(r.astype(int) - g.astype(int)) +
                 np.abs(g.astype(int) - b.astype(int)) +
                 np.abs(r.astype(int) - b.astype(int)))
    gray_mask = (gray_diff < 30) & (a < 220)
    data[gray_mask] = [0, 0, 0, 0]
    subject = Image.fromarray(data)

    bbox = subject.getbbox()
    if bbox:
        subject = subject.crop(bbox)

    subject = enhance_subject(subject)

    buffer = io.BytesIO()
    subject.save(buffer, format="PNG")
    encoded = base64.b64encode(buffer.getvalue()).decode("utf-8")

    return JSONResponse(
        {
            "image": f"data:image/png;base64,{encoded}",
            "width": subject.width,
            "height": subject.height,
        }
    )

