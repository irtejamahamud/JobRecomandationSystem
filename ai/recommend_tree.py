import sys
import json
import re
from sklearn.tree import DecisionTreeClassifier
import numpy as np

def clean_text(text):
    return re.sub(r'\s+', ' ', re.sub(r'[^\w\s]', '', text.lower())).strip()

# Define a basic skill set for vectorization
MASTER_SKILLS = ['python', 'java', 'sql', 'machine learning', 'data analysis', 'html', 'css', 'javascript', 'react', 'django']

def vectorize_text(skills_string):
    skills = clean_text(skills_string).split(',')
    skills = [s.strip() for s in skills]
    return [1 if master_skill in skills else 0 for master_skill in MASTER_SKILLS]

def main():
    try:
        input_json = sys.stdin.read()
        data = json.loads(input_json)
    except Exception as e:
        print(json.dumps({"error": f"Invalid input: {str(e)}"}))
        sys.exit(1)

    seeker_profile = data.get('seeker_profile', '')
    jobs_data = data.get('jobs', [])

    if not seeker_profile or not jobs_data:
        print(json.dumps({"error": "Missing seeker or job data"}))
        sys.exit(1)

    # Sample training data (for demo only — in production use DB or logs)
    training_X = [
        [1,0,1,1,0,0,0,0,0,0], 
        [0,1,1,0,0,0,0,0,0,0],  
        [1,1,0,0,0,0,0,1,0,0],  
        [0,0,1,0,0,0,0,0,0,0],  
        [0,0,0,1,1,0,0,0,0,0],  
    ]
    training_y = ['Data Scientist', 'Backend Developer', 'Full Stack Developer', 'Database Admin', 'ML Engineer']

    # Train model
    clf = DecisionTreeClassifier()
    clf.fit(training_X, training_y)

    # Vectorize seeker
    seeker_vec = vectorize_text(seeker_profile)

    # Predict category
    predicted_category = clf.predict([seeker_vec])[0]

    # Vectorize and score jobs
    recommended_jobs = []
    for job in jobs_data:
        job_vec = vectorize_text(job['text'])
        job_prediction = clf.predict([job_vec])[0]
        if job_prediction == predicted_category:
            recommended_jobs.append({
                'job_id': job['job_id'],
                'predicted_category': job_prediction
            })

    print(json.dumps({
        "recommended_jobs": recommended_jobs,
        "predicted_for_user": predicted_category
    }))

if __name__ == "__main__":
    main()
