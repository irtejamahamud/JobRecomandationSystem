import sys
import json
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

def main():
    # 1. Read input JSON from PHP
    try:
        input_json = sys.argv[1]
        data = json.loads(input_json)
    except Exception as e:
        print(json.dumps({"error": f"Invalid Input: {str(e)}"}))
        sys.exit(1)

    seeker_profile = data.get('seeker_profile', '')
    jobs_data = data.get('jobs', [])

    if not seeker_profile or not jobs_data:
        print(json.dumps({"error": "Missing data"}))
        sys.exit(1)

    # 2. Prepare corpus (seeker + jobs)
    corpus = [seeker_profile] + [job['text'] for job in jobs_data]

    # 3. TF-IDF Vectorization
    vectorizer = TfidfVectorizer(stop_words='english')
    tfidf_matrix = vectorizer.fit_transform(corpus)

    # 4. Cosine similarity
    seeker_vector = tfidf_matrix[0]
    job_vectors = tfidf_matrix[1:]

    similarities = cosine_similarity(seeker_vector, job_vectors).flatten()

    # 🟰 For testing: ignore threshold, always pick top N
    top_n = min(10, len(similarities))  # Don't exceed number of jobs
    top_indices = similarities.argsort()[-top_n:][::-1]  # Highest similarity first

    recommended_jobs = []
    for idx in top_indices:
        recommended_jobs.append({
            'job_id': jobs_data[idx]['job_id'],
            'similarity': round(float(similarities[idx]), 4)  # still showing similarity score
        })

    # 5. Output result
    print(json.dumps({"recommended_jobs": recommended_jobs}))

if __name__ == "__main__":
    main()
