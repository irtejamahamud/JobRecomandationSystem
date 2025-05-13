import sys
import json
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

def clean_text(text):
    """ Lowercase, remove punctuation, collapse whitespace. """
    return re.sub(r'\s+', ' ', re.sub(r'[^\w\s]', '', text.lower())).strip()

def main():
    try:
        input_json = sys.stdin.read()
        data = json.loads(input_json)
    except Exception as e:
        with open("debug_py_error.log", "w") as f:
            f.write(str(e))
        print(json.dumps({"error": f"Invalid Input: {str(e)}"}))
        sys.exit(1)

    seeker_profile = data.get('seeker_profile', '')
    jobs_data = data.get('jobs', [])

    if not seeker_profile or not jobs_data:
        print(json.dumps({"error": "Missing data"}))
        sys.exit(1)

    # Clean all texts
    seeker_cleaned = clean_text(seeker_profile)
    job_texts = [clean_text(job['text']) for job in jobs_data]

    corpus = [seeker_cleaned] + job_texts

    vectorizer = TfidfVectorizer(stop_words='english', ngram_range=(1, 2))
    tfidf_matrix = vectorizer.fit_transform(corpus)

    seeker_vector = tfidf_matrix[0]
    job_vectors = tfidf_matrix[1:]
    similarities = cosine_similarity(seeker_vector, job_vectors).flatten()

    top_n = min(10, len(similarities))
    top_indices = similarities.argsort()[-top_n:][::-1]

    recommended_jobs = []
    for idx in top_indices:
        recommended_jobs.append({
            'job_id': jobs_data[idx]['job_id'],
            'similarity': round(float(similarities[idx]), 4)
        })

    print(json.dumps({"recommended_jobs": recommended_jobs}))

if __name__ == "__main__":
    main()
