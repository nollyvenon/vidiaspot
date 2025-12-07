"""
Recommendation Engine Module for VidiaSpot AI/ML Service
Provides personalized ad recommendations
"""

import random
from typing import List, Dict, Any

def recommend_ads(user_id: int, item_ids: List[int] = [], category: str = None, limit: int = 10) -> list:
    """
    Generate personalized ad recommendations for a user.
    """
    # Placeholder implementation - in real scenario, use collaborative filtering, 
    # content-based filtering, or hybrid approaches
    
    # Simulate recommendations based on user's past interactions
    sample_ads = [
        {"id": 1, "title": "iPhone 13 Pro", "price": 450000, "category": "electronics", "location": "Lagos"},
        {"id": 2, "title": "Toyota Camry 2018", "price": 12000000, "category": "vehicles", "location": "Lagos"},
        {"id": 3, "title": "3-Bedroom House", "price": 80000000, "category": "property", "location": "Lagos"},
        {"id": 4, "title": "MacBook Pro", "price": 850000, "category": "electronics", "location": "Lagos"},
        {"id": 5, "title": "Leather Sofa Set", "price": 350000, "category": "furniture", "location": "Lagos"},
        {"id": 6, "title": "Women's Wedding Gown", "price": 80000, "category": "fashion", "location": "Abuja"},
        {"id": 7, "title": "Honda Motorcycle", "price": 650000, "category": "vehicles", "location": "Port Harcourt"},
        {"id": 8, "title": "Samsung Smart TV", "price": 500000, "category": "electronics", "location": "Lagos"},
    ]
    
    # If a category is specified, filter recommendations
    if category:
        filtered_ads = [ad for ad in sample_ads if ad['category'] == category]
    else:
        filtered_ads = sample_ads
    
    # If specific item IDs are provided, suggest similar items
    if item_ids:
        # For demo purposes, just pick random items from the same category as the viewed items
        recommended = []
        for ad in filtered_ads:
            if ad['id'] not in item_ids and len(recommended) < limit:
                recommended.append(ad)
        return recommended[:limit]
    else:
        # Shuffle and return random recommendations
        shuffled_ads = filtered_ads.copy()
        random.shuffle(shuffled_ads)
        return shuffled_ads[:limit]