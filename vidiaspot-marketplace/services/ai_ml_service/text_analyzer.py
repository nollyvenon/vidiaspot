"""
Text Analysis Module for VidiaSpot AI/ML Service
Handles text processing, keyword extraction, and classification
"""

import re
from transformers import pipeline, AutoTokenizer, AutoModelForSequenceClassification

def extract_keywords(text: str) -> list:
    """
    Extract relevant keywords from text.
    """
    # Simple keyword extraction using regex and common English words
    # In real scenario, use NLP techniques like TF-IDF, RAKE, etc.
    words = re.findall(r'\w+', text.lower())
    
    # Remove common stop words
    stop_words = {'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'about', 'as', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'up', 'down', 'out', 'off', 'over', 'under', 'again', 'further', 'then', 'once'}
    
    keywords = [word for word in words if len(word) > 2 and word not in stop_words]
    
    # Get unique keywords and limit to top 20
    unique_keywords = list(set(keywords))
    return unique_keywords[:20]

def classify_ad_text(text: str) -> str:
    """
    Classify ad text into categories.
    """
    # Simple classification based on keywords
    text_lower = text.lower()
    
    category_keywords = {
        'electronics': ['phone', 'laptop', 'computer', 'tablet', 'camera', 'tv', 'audio'],
        'vehicles': ['car', 'bike', 'truck', 'motorcycle', 'vehicle', 'engine'],
        'property': ['house', 'apartment', 'land', 'building', 'rent', 'sale', 'estate'],
        'furniture': ['chair', 'table', 'bed', 'sofa', 'cabinet', 'furniture'],
        'fashion': ['clothes', 'shoes', 'bag', 'dress', 'shirt'],
        'books': ['book', 'novel', 'textbook'],
        'jobs': ['job', 'work', 'employment', 'opportunity', 'position'],
        'services': ['service', 'repair', 'installation', 'maintenance']
    }
    
    scores = {}
    for category, keywords in category_keywords.items():
        score = sum(1 for keyword in keywords if keyword in text_lower)
        scores[category] = score
    
    # Return category with highest score, or 'general' if no matches
    if scores and max(scores.values()) > 0:
        return max(scores, key=scores.get)
    else:
        return 'general'