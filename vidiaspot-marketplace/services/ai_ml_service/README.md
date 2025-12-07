# VidiaSpot AI/ML Service

FastAPI-based microservice for AI and Machine Learning features in the VidiaSpot marketplace.

## Features

- Image analysis and object detection
- Text analysis and classification
- Personalized ad recommendations
- Natural language processing

## Requirements

- Python 3.8+
- FastAPI
- PyTorch or TensorFlow (for advanced models)

## Setup

```bash
# Install dependencies
pip install -r requirements.txt

# Run the service
uvicorn main:app --reload --port 8001
```

## Endpoints

- `GET /` - Root endpoint
- `POST /analyze/image` - Analyze uploaded image
- `POST /analyze/text` - Analyze text content
- `POST /recommendations` - Get personalized recommendations
- `POST /nlp/process` - Process text with NLP
- `GET /health` - Health check

## Usage

### Image Analysis
Upload an image file to get object detection and quality analysis.

### Text Analysis
Submit text for classification, keyword extraction, and sentiment analysis.

### Recommendations
Get personalized ad recommendations based on user history and preferences.

## Architecture

The service follows a modular architecture with separate modules for:
- Image analysis
- Text analysis
- Recommendation engine
- NLP processing