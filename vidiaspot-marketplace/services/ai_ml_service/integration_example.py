"""
AI/ML Integration Examples for VidiaSpot Marketplace

This file demonstrates how to integrate the AI/ML services with the main application
"""

import requests
import json
from typing import Dict, List, Any, Optional

class AIIntegrationClient:
    def __init__(self, ai_service_url: str = "http://localhost:8001"):
        self.ai_service_url = ai_service_url
        self.session = requests.Session()

    def analyze_image(self, image_file_path: str) -> Dict[Any, Any]:
        """
        Analyze an image for object detection and quality assessment
        
        Args:
            image_file_path: Path to the image file to analyze
            
        Returns:
            Dictionary with analysis results
        """
        with open(image_file_path, 'rb') as image_file:
            files = {'file': image_file}
            response = self.session.post(
                f"{self.ai_service_url}/analyze/image",
                files=files
            )
        
        if response.status_code == 200:
            return response.json()
        else:
            raise Exception(f"Image analysis failed: {response.text}")

    def analyze_ad_text(self, text: str) -> Dict[Any, Any]:
        """
        Analyze ad text for classification and keyword extraction
        
        Args:
            text: The text to analyze
            
        Returns:
            Dictionary with analysis results
        """
        payload = {
            "text": text,
            "task": "classification"
        }
        
        response = self.session.post(
            f"{self.ai_service_url}/analyze/text",
            json=payload
        )
        
        if response.status_code == 200:
            return response.json()
        else:
            raise Exception(f"Text analysis failed: {response.text}")

    def get_recommendations(self, user_id: int, item_ids: List[int] = [], category: Optional[str] = None) -> List[Dict[Any, Any]]:
        """
        Get personalized ad recommendations for a user
        
        Args:
            user_id: ID of the user
            item_ids: List of item IDs to base recommendations on
            category: Optional category to filter recommendations
            
        Returns:
            List of recommended ads
        """
        payload = {
            "user_id": user_id,
            "item_ids": item_ids,
            "category": category,
            "limit": 10
        }
        
        response = self.session.post(
            f"{self.ai_service_url}/recommendations",
            json=payload
        )
        
        if response.status_code == 200:
            return response.json()["recommendations"]
        else:
            raise Exception(f"Recommendations failed: {response.text}")

    def process_nlp(self, text: str) -> Dict[Any, Any]:
        """
        Process text with natural language processing
        
        Args:
            text: The text to process
            
        Returns:
            Dictionary with NLP analysis results
        """
        response = self.session.post(
            f"{self.ai_service_url}/nlp/process",
            params={"text": text}
        )
        
        if response.status_code == 200:
            return response.json()
        else:
            raise Exception(f"NLP processing failed: {response.text}")

# Example usage
def example_usage():
    client = AIIntegrationClient("http://localhost:8001")  # Adjust URL as needed
    
    # Example 1: Analyze an image
    try:
        # image_result = client.analyze_image("path/to/image.jpg")
        # print("Image Analysis:", image_result)
        pass
    except Exception as e:
        print(f"Image analysis error: {e}")
    
    # Example 2: Analyze ad text
    ad_text = "Beautiful used car for sale, excellent condition, very reliable."
    try:
        text_result = client.analyze_ad_text(ad_text)
        print("Text Analysis:", text_result)
    except Exception as e:
        print(f"Text analysis error: {e}")
    
    # Example 3: Get recommendations
    try:
        recs = client.get_recommendations(user_id=123, item_ids=[1, 2, 3])
        print("Recommendations:", recs)
    except Exception as e:
        print(f"Recommendations error: {e}")
    
    # Example 4: NLP processing
    try:
        nlp_result = client.process_nlp("This product is amazing, I love it!")
        print("NLP Processing:", nlp_result)
    except Exception as e:
        print(f"NLP processing error: {e}")

if __name__ == "__main__":
    example_usage()