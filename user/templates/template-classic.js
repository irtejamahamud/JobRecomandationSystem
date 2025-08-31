(function () {
  const id = "classic";
  const name = "Classic";

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

  function sectionIf(title, html, cond) {
    if (!cond) return "";
    return `
      <div class="cv-section">
        <h3>${esc(title)}</h3>
        ${html}
      </div>`;
  }

  function renderPreview() {
    return `<div style="font-size:11px; line-height:1.4; text-align:center; padding:10px;">
      <div style="width:60px;height:60px;border-radius:50%;background:#fff;margin:0 auto 8px;border:3px solid #e1e7ff;"></div>
      <div style="height:8px;background:#3843d0;margin:6px 24px;border-radius:4px;"></div>
      <div style="height:6px;background:#0b7d3e;margin:8px 36px;border-radius:4px;"></div>
      <div style="height:6px;background:#e1e7ff;margin:8px 36px;border-radius:4px;"></div>
    </div>`;
  }

  function renderFull(d) {
    const contacts = []
      .concat(
        d.email
          ? `<span><i class="fas fa-envelope"></i>${esc(d.email)}</span>`
          : ""
      )
      .concat(
        d.mobile
          ? `<span><i class="fas fa-phone"></i>${esc(d.mobile)}</span>`
          : ""
      )
      .concat(
        d.website
          ? `<span><i class="fas fa-globe"></i>${esc(d.website)}</span>`
          : ""
      )
      .concat(
        d.address
          ? `<span><i class="fas fa-map-marker-alt"></i>${esc(
              d.address
            )}</span>`
          : ""
      )
      .concat(
        d.dob
          ? `<span><i class="fas fa-birthday-cake"></i>${U.fmtDate(
              d.dob
            )}</span>`
          : ""
      )
      .concat(
        d.gender
          ? `<span><i class="fas fa-venus-mars"></i>${esc(d.gender)}</span>`
          : ""
      )
      .concat(
        d.marital
          ? `<span><i class="fas fa-heart"></i>${esc(d.marital)}</span>`
          : ""
      )
      .filter(Boolean)
      .join("");

    const expHtml = (d.experiences || [])
      .map((exp) => {
        const company = esc(exp.company_name || "");
        const title = esc(exp.job_title || "");
        const start = U.fmtMonthYear(exp.start_date) || "";
        const end = exp.end_date ? U.fmtMonthYear(exp.end_date) : "Present";
        const resp = exp.responsibilities
          ? `<div class="muted" style="margin-top:8px; white-space:pre-line;">${esc(
              exp.responsibilities
            )}</div>`
          : "";
        return `
        <div class="timeline-item">
          <h4>${title}${company ? " • " + company : ""}</h4>
          <div class="range">${start} - ${end}</div>
          ${resp}
        </div>`;
      })
      .join("");

    const eduHtml = (d.educations || [])
      .map((edu) => {
        const lvl = esc(edu.level_name || "");
        const deg = esc(edu.degree_title || "");
        const ins = esc(edu.institution_name || "");
        const sy = esc(edu.start_year || "");
        const ey = esc(edu.end_year || "");
        return `
        <div class="timeline-item">
          <h4>${deg}${lvl ? " • " + lvl : ""}</h4>
          <div class="muted">${ins}</div>
          <div class="range">${sy} - ${ey}</div>
        </div>`;
      })
      .join("");

    const skillHtml = (d.skills || [])
      .map(
        (sk) =>
          `<span class="badge">${esc(sk.name)} — ${esc(sk.proficiency)}</span>`
      )
      .join("");

    const projHtml = (d.projects || [])
      .map((p) => {
        const title = esc(p.title || "");
        const link = p.project_link
          ? `<div class="muted" style="margin:6px 0;">
        <a class="proj-link" href="${esc(
          p.project_link
        )}" target="_blank"><i class="fas fa-link"></i> ${esc(
              p.project_link
            )}</a>
      </div>`
          : "";
        const desc = p.description
          ? `<div class="muted" style="white-space:pre-line;">${esc(
              p.description
            )}</div>`
          : "";
        return `
        <div class="timeline-item">
          <h4>${title}</h4>
          ${link}
          ${desc}
        </div>`;
      })
      .join("");

    const certHtml = (d.certifications || [])
      .map((c) => {
        const cname = esc(c.certification_name || "");
        const org = esc(c.issuing_organization || "");
        const yr = c.issue_date ? new Date(c.issue_date).getFullYear() : "";
        const curl = esc(c.certificate_url || "");
        const link = curl
          ? `<div class="muted" style="margin-top:6px;"><a class="proj-link" href="${curl}" target="_blank"><i class="fas fa-external-link-alt"></i> Certificate</a></div>`
          : "";
        return `
        <div class="timeline-item">
          <h4>${cname}${org ? " • " + org : ""}</h4>
          ${yr ? `<div class="range">${yr}</div>` : ""}
          ${link}
        </div>`;
      })
      .join("");

    const langHtml = (d.languages || [])
      .map(
        (l) =>
          `<span class="badge">${esc(l.language_name)} — ${esc(
            l.proficiency
          )}</span>`
      )
      .join("");

    const resumeLink = d.latestResume
      ? `<div class="cv-section">
      <a class="resume-link" href="${esc(
        d.latestResume
      )}" target="_blank"><i class="fas fa-file-pdf"></i> View Latest Uploaded Resume</a>
    </div>`
      : "";

    // Return full HTML (scoped styles)
    return `
      <style>
        .cv-classic .cv-container {
          max-width: 900px; margin: 24px auto; background: #fff; border-radius: 14px;
          box-shadow: 0 6px 22px rgba(31,40,105,0.08); overflow: hidden;
        }
        .cv-classic .cv-header {
          display: grid; grid-template-columns: 120px 1fr; gap: 18px; padding: 24px; background: #f3f6ff;
          border-bottom: 1px solid #eef1ff;
        }
        .cv-classic .cv-avatar {
          width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }
        .cv-classic .cv-title { margin: 0; font-size: 28px; color: #1f2869; }
        .cv-classic .cv-subtitle { margin: 6px 0 10px; color: #667; font-weight: 500; }
        .cv-classic .cv-contacts { display: flex; flex-wrap: wrap; gap: 10px 18px; color: #444; font-size: 13px; }
        .cv-classic .cv-contacts span { display: inline-flex; align-items: center; gap: 8px; }
        .cv-classic .cv-contacts i { color: #3843d0; }
        .cv-classic .cv-body { padding: 20px 24px 28px; }
        .cv-classic .cv-section { margin-top: 18px; }
        .cv-classic .cv-section h3 {
          margin: 0 0 12px; font-size: 16px; color: #3843d0; letter-spacing: 0.4px; text-transform: uppercase;
          display: inline-block; position: relative; padding-bottom: 6px;
        }
        .cv-classic .cv-section h3:after {
          content: ""; position: absolute; left: 0; bottom: 0; width: 46px; height: 3px; background: #0b7d3e; border-radius: 2px;
        }
        .cv-classic .cv-text { color: #333; line-height: 1.7; }
        .cv-classic .timeline { display: grid; gap: 14px; }
        .cv-classic .timeline-item { padding: 12px 14px; border: 1px solid #eef1ff; border-radius: 12px; background: #fff; }
        .cv-classic .timeline-item h4 { margin: 0 0 6px; color: #1f2869; font-size: 16px; }
        .cv-classic .timeline-item .muted { color: #667; font-size: 13px; }
        .cv-classic .timeline-item .range { color: #0b7d3e; font-weight: 600; font-size: 13px; }
        .cv-classic .grid-2 { display: grid; gap: 16px; grid-template-columns: 1fr 1fr; }
        .cv-classic .skill-badges, .cv-classic .lang-badges { display: flex; flex-wrap: wrap; gap: 10px; }
        .cv-classic .badge { font-size: 12px; padding: 6px 10px; border-radius: 999px; background: #f3f6ff; color: #1f2869; border: 1px solid #e1e7ff; }
        .cv-classic .proj-link { color: #3843d0; text-decoration: none; }
        .cv-classic .proj-link:hover { text-decoration: underline; }
        .cv-classic .resume-link { display: inline-flex; align-items: center; gap: 8px; color: #0b7d3e; text-decoration: none; font-weight: 600; }
      </style>
      <div class="cv-classic">
        <div class="cv-container">
          <div class="cv-header">
            <img class="cv-avatar" src="${esc(d.profileImg)}" alt="Profile">
            <div>
              <h1 class="cv-title">${esc(d.fullName)}</h1>
              <div class="cv-subtitle">${esc(d.jobTitle || "Job Seeker")}</div>
              <div class="cv-contacts">${contacts}</div>
            </div>
          </div>

          <div class="cv-body">
            ${sectionIf(
              "Profile Summary",
              `<div class="cv-text">${U.nl2br(d.bio || "")}</div>`,
              !!d.bio
            )}
            ${sectionIf(
              "Experience",
              `<div class="timeline">${expHtml}</div>`,
              (d.experiences || []).length
            )}
            <div class="cv-section">
              <div class="grid-2">
                ${sectionIf(
                  "Education",
                  `<div class="timeline">${eduHtml}</div>`,
                  (d.educations || []).length
                )}
                ${sectionIf(
                  "Skills",
                  `<div class="skill-badges">${skillHtml}</div>`,
                  (d.skills || []).length
                )}
              </div>
            </div>
            ${sectionIf(
              "Projects",
              `<div class="timeline">${projHtml}</div>`,
              (d.projects || []).length
            )}
            ${sectionIf(
              "Certifications",
              `<div class="timeline">${certHtml}</div>`,
              (d.certifications || []).length
            )}
            ${sectionIf(
              "Languages",
              `<div class="lang-badges">${langHtml}</div>`,
              (d.languages || []).length
            )}
            ${resumeLink}
          </div>
        </div>
      </div>
    `;
  }

  window.TEMPLATES = window.TEMPLATES || [];
  window.TEMPLATES.push({
    id,
    name,
    renderPreview,
    renderFull,
    badge: "Default",
  });
})();
