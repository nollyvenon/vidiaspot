from fastapi import FastAPI, UploadFile, File, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import uvicorn
import numpy as np
from PIL import Image
import io
import os
from typing import Dict, List, Optional

# Import AI/ML models
from services.ai_ml_service.image_analyzer import detect_objects, analyze_quality
from services.ai_ml_service.text_analyzer import extract_keywords, classify_ad_text
from services.ai_ml_service.recommendation_engine import recommend_ads
from services.ai_ml_service.nlp_processor import process_nlp_request

app = FastAPI(
    title="VidiaSpot AI/ML Service",
    description="AI/ML service for the VidiaSpot marketplace platform",
    version="1.0.0"
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # In production, specify actual origins
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

class TextAnalysisRequest(BaseModel):
    text: str
    task: str = "classification"  # Options: classification, keyword_extraction, sentiment_analysis

class RecommendationRequest(BaseModel):
    user_id: int
    item_ids: List[int] = []
    category: Optional[str] = None
    limit: int = 10

class ImageAnalysisResponse(BaseModel):
    objects_detected: List[str]
    quality_score: float
    confidence_level: float
    suggestions: List[str]

class TextAnalysisResponse(BaseModel):
    keywords: List[str]
    category: str
    sentiment_score: float
    tags: List[str]

@app.get("/")
async def root():
    return {"message": "VidiaSpot AI/ML Service is running!"}

@app.post("/analyze/image", response_model=ImageAnalysisResponse)
async def analyze_image(file: UploadFile = File(...)):
    """
    Analyze an uploaded image to detect objects, assess quality, and provide suggestions.
    """
    try:
        contents = await file.read()
        image = Image.open(io.BytesIO(contents)).convert("RGB")
        
        # Perform object detection
        objects = detect_objects(image)
        
        # Analyze image quality
        quality_score = analyze_quality(image)
        
        # Generate suggestions
        suggestions = []
        if quality_score < 0.7:
            suggestions.append("Consider improving lighting conditions")
        if len(objects) == 0:
            suggestions.append("No prominent objects detected, verify image content")
        
        return ImageAnalysisResponse(
            objects_detected=objects,
            quality_score=quality_score,
            confidence_level=np.random.uniform(0.7, 1.0),  # Placeholder
            suggestions=suggestions
        )
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@app.post("/analyze/text", response_model=TextAnalysisResponse)
async def analyze_text(request: TextAnalysisRequest):
    """
    Analyze text for classification, keyword extraction, and sentiment analysis.
    """
    try:
        if request.task == "classification":
            category = classify_ad_text(request.text)
        else:
            category = "general"
        
        keywords = extract_keywords(request.text)
        
        # Placeholder for sentiment analysis
        sentiment_score = np.random.uniform(-1, 1)  # Placeholder
        
        tags = []
        if len(keywords) > 0:
            tags.extend(keywords[:5])  # Top 5 keywords as tags
        
        return TextAnalysisResponse(
            keywords=keywords,
            category=category,
            sentiment_score=sentiment_score,
            tags=tags
        )
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@app.post("/recommendations")
async def get_recommendations(request: RecommendationRequest):
    """
    Get personalized ad recommendations for a user.
    """
    try:
        recommendations = recommend_ads(
            user_id=request.user_id,
            item_ids=request.item_ids,
            category=request.category,
            limit=request.limit
        )
        return {"recommendations": recommendations}
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@app.post("/nlp/process")
async def process_nlp(text: str):
    """
    General NLP processing endpoint.
    """
    try:
        result = process_nlp_request(text)
        return result
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

@app.get("/health")
async def health_check():
    """
    Health check endpoint.
    """
    return {"status": "healthy", "service": "ai_ml_service"}

if __name__ == "__main__":
    port = int(os.getenv("PORT", 8001))
    uvicorn.run(app, host="0.0.0.0", port=port)