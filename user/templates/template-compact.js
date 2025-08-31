(function () {
  const id = "compact";
  const name = "Compact";

  function esc(s) {
    if (s === null || s === undefined) return "";
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
  const U = window.CVUtils || {
    esc,
    nl2br: (s) => esc(s).replace(/\n/g, "<br>"),
    fmtMonthYear: () => "",
    fmtDate: () => "",
  };

  function renderPreview() {
    return `<div style="width:88%;height:78%;border:2px dashed #cbd3ff;border-radius:6px;"></div>`;
  }

  function item(label, value) {
    if (!value) return "";
    return `<div class="row"><span>${label}</span><strong>${esc(
      value
    )}</strong></div>`;
  }

  function renderFull(d) {
    const exp = (d.experiences || [])
      .map((e) => {
        const start = U.fmtMonthYear(e.start_date) || "";
        const end = e.end_date ? U.fmtMonthYear(e.end_date) : "Present";
        return `<li><strong>${esc(e.job_title || "")}</strong>${
          e.company_name ? " • " + esc(e.company_name) : ""
        } <em>(${start} - ${end})</em></li>`;
      })
      .join("");

    const edu = (d.educations || [])
      .map((e) => {
        return `<li><strong>${esc(e.degree_title || "")}</strong>${
          e.level_name ? " • " + esc(e.level_name) : ""
        } — ${esc(e.institution_name || "")} <em>(${esc(
          e.start_year || ""
        )} - ${esc(e.end_year || "")})</em></li>`;
      })
      .join("");

    const skills = (d.skills || []).map((s) => esc(s.name)).join(", ");
    const langs = (d.languages || [])
      .map((l) => `${esc(l.language_name)} (${esc(l.proficiency)})`)
      .join(", ");
    const projs = (d.projects || [])
      .map((p) => {
        return `<li><strong>${esc(p.title || "")}</strong>${
          p.project_link
            ? ` — <a href="${esc(p.project_link)}" target="_blank">${esc(
                p.project_link
              )}</a>`
            : ""
        }${
          p.description
            ? `<div class="muted">${U.nl2br(p.description)}</div>`
            : ""
        }</li>`;
      })
      .join("");

    const certs = (d.certifications || [])
      .map((c) => {
        const yr = c.issue_date ? new Date(c.issue_date).getFullYear() : "";
        return `<li><strong>${esc(c.certification_name || "")}</strong>${
          c.issuing_organization ? " • " + esc(c.issuing_organization) : ""
        }${yr ? ` — ${yr}` : ""}</li>`;
      })
      .join("");

    return `
      <style>
        .cv-compact { max-width: 820px; margin: 16px auto; background:#fff; border-radius: 12px; padding: 18px; box-shadow: 0 6px 22px rgba(31,40,105,0.08); }
        .cv-compact .head { display: grid; grid-template-columns: 80px 1fr; gap: 14px; border-bottom: 2px solid #f0f2ff; padding-bottom: 12px; }
        .cv-compact img { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; }
        .cv-compact h1 { margin: 0; font-size: 24px; color:#1f2869; }
        .cv-compact .role { color:#3843d0; font-weight:600; margin-top:2px; }
        .cv-compact .rows { display:grid; grid-template-columns: 1fr 1fr; gap: 8px 12px; margin-top: 8px; }
        .cv-compact .row { font-size: 13px; color:#333; display:flex; gap: 8px; }
        .cv-compact .row span { color:#667; min-width: 70px; }
        .cv-compact section { margin-top: 14px; }
        .cv-compact h3 { margin: 0 0 8px; font-size: 14px; color:#0b7d3e; text-transform: uppercase; letter-spacing: .4px; }
        .cv-compact ul { margin: 0; padding-left: 16px; }
        .cv-compact .muted { color:#666; font-size: 12px; margin-top: 2px; }
        @media print { .cv-compact { box-shadow:none; border-radius:0; } }
      </style>
      <div class="cv-compact">
        <div class="head">
          <img src="${esc(d.profileImg)}" alt="Profile">
          <div>
            <h1>${esc(d.fullName)}</h1>
            <div class="role">${esc(d.jobTitle || "Job Seeker")}</div>
            <div class="rows">
              ${item("Email", d.email)}${item("Phone", d.mobile)}${item(
      "Website",
      d.website
    )}
              ${item("Address", d.address)}${item(
      "DOB",
      d.dob ? U.fmtDate(d.dob) : ""
    )}${item("Gender", d.gender)}
            </div>
          </div>
        </div>

        ${
          d.bio
            ? `<section><h3>Summary</h3><div>${U.nl2br(d.bio)}</div></section>`
            : ""
        }

        ${
          (d.experiences || []).length
            ? `<section><h3>Experience</h3><ul>${exp}</ul></section>`
            : ""
        }
        ${
          (d.educations || []).length
            ? `<section><h3>Education</h3><ul>${edu}</ul></section>`
            : ""
        }
        ${
          skills
            ? `<section><h3>Skills</h3><div>${esc(skills)}</div></section>`
            : ""
        }
        ${
          langs
            ? `<section><h3>Languages</h3><div>${esc(langs)}</div></section>`
            : ""
        }
        ${
          (d.projects || []).length
            ? `<section><h3>Projects</h3><ul>${projs}</ul></section>`
            : ""
        }
        ${
          (d.certifications || []).length
            ? `<section><h3>Certifications</h3><ul>${certs}</ul></section>`
            : ""
        }
        ${
          d.latestResume
            ? `<section><a href="${esc(
                d.latestResume
              )}" target="_blank"><i class="fas fa-file-pdf"></i> View Latest Uploaded Resume</a></section>`
            : ""
        }
      </div>
    `;
  }

  window.TEMPLATES = window.TEMPLATES || [];
  window.TEMPLATES.push({
    id,
    name,
    renderPreview,
    renderFull,
    badge: "Minimal",
  });
})();
