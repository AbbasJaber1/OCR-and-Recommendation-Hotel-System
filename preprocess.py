import cv2
import numpy as np
import sys
import os

def preprocess_image(input_path, output_path):
    print(f"DEBUG: Input file path = {input_path}")

    if not os.path.exists(input_path):
        print(f"Error: Image file does not exist at {input_path}")
        return

    # Read the image in grayscale
    image = cv2.imread(input_path, cv2.IMREAD_GRAYSCALE)

    if image is None:
        print(f"Error: OpenCV cannot read the image at {input_path}. Check file format.")
        return

    # Apply Gaussian Blur to remove noise
    blurred = cv2.GaussianBlur(image, (3,3), 0)

    # Convert to Black & White using Adaptive Thresholding
    processed = cv2.adaptiveThreshold(image, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 15, 8)
    
    clahe = cv2.createCLAHE(clipLimit=3.0, tileGridSize=(8,8))  # NEW (Enhances text clarity)
    image = clahe.apply(image)

    # Save the processed image
    cv2.imwrite(output_path, processed)
    print(f"Processed image saved at {output_path}")

# Get file paths from PHP
if len(sys.argv) < 3:
    print("Error: Missing arguments.")
    sys.exit(1)

input_file = sys.argv[1]
output_file = sys.argv[2]

preprocess_image(input_file, output_file)
