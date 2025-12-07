"""
NLP Processing Module for VidiaSpot AI/ML Service
Handles general natural language processing tasks
"""

import re
from typing import Dict, Any

def process_nlp_request(text: str) -> Dict[str, Any]:
    """
    Process a general NLP request and return relevant analysis.
    """
    # Perform multiple NLP tasks
    result = {
        "original_text": text,
        "word_count": len(text.split()),
        "char_count": len(text),
        "sentence_count": len(re.split(r'[.!?]+', text)) - 1,  # Subtract 1 because split creates an extra empty string
        "avg_word_length": calculate_avg_word_length(text),
        "readability_score": calculate_readability_score(text),
        "sentiment_estimate": estimate_sentiment(text),
        "entities": extract_entities(text),
    }
    
    return result

def calculate_avg_word_length(text: str) -> float:
    """
    Calculate average word length in the text.
    """
    words = re.findall(r'\w+', text)
    if not words:
        return 0.0
    
    total_length = sum(len(word) for word in words)
    return total_length / len(words)

def calculate_readability_score(text: str) -> float:
    """
    Calculate a simple readability score (placeholder implementation).
    """
    words = text.split()
    sentences = re.split(r'[.!?]+', text)
    
    # Remove empty strings from sentences list
    sentences = [s for s in sentences if s.strip()]
    
    if not words or len(sentences) == 0:
        return 0.0
    
    avg_words_per_sentence = len(words) / len(sentences)
    
    # Simple readability calculation
    readability = max(0, 10 - (avg_words_per_sentence / 10))
    return min(readability, 10.0)

def estimate_sentiment(text: str) -> str:
    """
    Estimate sentiment of the text (positive, neutral, negative) - simplified approach.
    """
    positive_words = {'good', 'great', 'excellent', 'amazing', 'wonderful', 'fantastic', 'perfect', 'love', 'like', 'beautiful', 'nice', 'awesome', 'brilliant', 'outstanding', 'superb'}
    negative_words = {'bad', 'terrible', 'awful', 'horrible', 'disgusting', 'hate', 'dislike', 'ugly', 'worst', 'disappointing', 'annoying', 'frustrating', 'problem', 'issue', 'damage', 'broken'}
    
    words = set(re.findall(r'\w+', text.lower()))
    
    pos_count = len(words.intersection(positive_words))
    neg_count = len(words.intersection(negative_words))
    
    if pos_count > neg_count:
        return "positive"
    elif neg_count > pos_count:
        return "negative"
    else:
        return "neutral"

def extract_entities(text: str) -> dict:
    """
    Extract named entities (simplified approach).
    """
    # For demonstration, extracting phone numbers and email addresses
    email_pattern = r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b'
    phone_pattern = r'(\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}'
    
    emails = re.findall(email_pattern, text)
    phones = re.findall(phone_pattern, text)
    
    return {
        "emails": emails,
        "phones": phones,
        "names": [],  # Placeholder - in real scenario, use NER
        "dates": [],  # Placeholder - in real scenario, use date extraction
        "locations": [],  # Placeholder - in real scenario, use location extraction
    }