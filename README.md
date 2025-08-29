# NextWorkX

**NextWorkX** is an AI-powered job matching platform designed to connect job seekers and employers efficiently. Leveraging smart recommendations, skill-based matching, and a modern user experience, NextWorkX helps users find the right opportunities and companies discover top talent.

---

## 🚀 Features

- **AI-Powered Job Recommendations**  
  Get personalized job suggestions based on your skills, experience, and profile using advanced AI algorithms.

- **Skill-Based Matching**  
  Match with jobs that fit your expertise and interests for better career outcomes.

- **Step-by-Step Profile Builder**  
  Guided onboarding for job seekers:

  - Personal Info
  - Education
  - Experience & Projects
  - Skills & Languages
  - Resume Upload
  - Social Links

- **Dynamic Profile Progress Tracking**  
  Visual progress bar and completion tips to help users build a strong profile.

- **Resume Generator & Download**  
  Instantly generate a professional CV from your profile and download it as PDF.

- **Job Search with Advanced Filters**  
  Search jobs by title, company, location, category, salary, job type, experience level, and more.

- **Bookmark & Apply Jobs**  
  Save jobs for later and apply directly from the platform.

- **Employer Portal**  
  Employers can post jobs, view applicants, and manage company profiles.

- **Notifications & Social Integration**  
  Get notified about new matches and connect your social profiles.

- **Responsive & Modern UI**  
  Clean, mobile-friendly design using Poppins font and FontAwesome icons.

---

## 🛠️ Tech Stack

- **Backend:** PHP (PDO, MySQL)
- **Frontend:** HTML, CSS, JavaScript, jQuery
- **AI/Recommendation:** Custom PHP-based similarity engine (see `/ai/recommend_jobs.php`)
- **PDF Generation:** Print-ready CV layout (browser print/download)
- **Authentication:** Session-based login for job seekers and employers

---

## 📦 Installation

1. **Clone the repository:**
   ```
   git clone https://github.com/yourusername/nextworkx.git
   ```
2. **Setup your database:**

   - Import the provided SQL schema in `/database/nextworkx.sql` (if available).
   - Update `/includes/db.php` with your MySQL credentials.

3. **Configure uploads:**

   - Ensure `uploads/profile/` and `uploads/resume/` directories are writable.

4. **Run locally:**
   - Place the project in your web server root (e.g., `htdocs` for XAMPP).
   - Access via `http://localhost/nextworkx/`.

---

## 👤 User Guide

### For Job Seekers

- **Sign Up:** Create an account and start building your profile.
- **Profile Steps:** Complete each section for better job matches.
- **Search & Apply:** Use filters to find jobs, bookmark favorites, and apply.
- **Download CV:** Generate and download your resume anytime.

### For Employers

- **Register:** Create a company account.
- **Post Jobs:** Add new job listings with detailed requirements.
- **View Applicants:** See candidate profiles and download resumes.

---

## 📈 AI Recommendation Engine

NextWorkX uses a custom AI engine to analyze user profiles and job postings. It calculates similarity scores based on skills, experience, and preferences, surfacing the most relevant jobs for each user.

---

## 📄 License

This project is for educational and demonstration purposes. For commercial use, please contact the author.

---

## 🤝 Contributing

Pull requests and suggestions are welcome! For major changes, please open an issue first.

---

## 📬 Contact

For support or inquiries, email: `irtejamahamud9@gmail.com`

---

**NextWorkX – Empowering Careers with AI.**
