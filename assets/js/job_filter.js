document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const jobResults = document.querySelector("#job-list");
    const spinner = document.querySelector("#job-loading");
    const noResults = document.querySelector("#no-results");
  
    function loadJobs(page = 1) {
      const formData = new FormData(form);
      formData.append("page", page);
  
      const params = new URLSearchParams();
      formData.forEach((v, k) => params.append(k, v));
  
      // Show loading spinner
      spinner.style.display = "block";
      jobResults.innerHTML = "";
      noResults.style.display = "none";
  
      fetch("ajax/job_results.php?" + params.toString())
        .then(res => res.text())
        .then(html => {
          spinner.style.display = "none";
          jobResults.innerHTML = html;
  
          if (html.trim() === "") {
            noResults.style.display = "block";
          }
  
          attachPaginationEvents();
        })
        .catch(() => {
          spinner.style.display = "none";
          jobResults.innerHTML = "";
          noResults.style.display = "block";
        });
    }
  
    function attachPaginationEvents() {
      document.querySelectorAll(".pagination .page").forEach((btn) => {
        btn.onclick = (e) => {
          e.preventDefault();
          const page = btn.dataset.page;
          loadJobs(page);
        };
      });
    }
  
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      loadJobs(1);
    });
  
    loadJobs(); // initial load
  });
  