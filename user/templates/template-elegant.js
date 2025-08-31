(function () {
  const id = "elegant";
  const name = "Elegant";

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
    return `<div style="width:90%;height:80%;background:linear-gradient(180deg,#1f2869 0,#1f2869 38%,#fff 38%,#fff 100%);border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,.08);"></div>`;
  }

  function renderFull(d) {
    const skills = (d.skills || [])
      .map(
        (s) =>
          `<li>${esc(s.name)} <small style="color:#777;">(${esc(
            s.proficiency
          )})</small></li>`
      )
      .join("");
    const languages = (d.languages || [])
      .map(
        (l) =>
          `<li>${esc(l.language_name)} <small style="color:#777;">(${esc(
            l.proficiency
          )})</small></li>`
      )
      .join("");
    const contacts = `
      <ul class="elg-list">
        ${
          d.email
            ? `<li><i class="fas fa-envelope"></i>${esc(d.email)}</li>`
            : ""
        }
        ${
          d.mobile
            ? `<li><i class="fas fa-phone"></i>${esc(d.mobile)}</li>`
            : ""
        }
        ${
          d.website
            ? `<li><i class="fas fa-globe"></i>${esc(d.website)}</li>`
            : ""
        }
        ${
          d.address
            ? `<li><i class="fas fa-map-marker-alt"></i>${esc(d.address)}</li>`
            : ""
        }
      </ul>`;

    const exp = (d.experiences || [])
      .map((e) => {
        const start = U.fmtMonthYear(e.start_date) || "";
        const end = e.end_date ? U.fmtMonthYear(e.end_date) : "Present";
        const title = esc(e.job_title || "");
        const company = esc(e.company_name || "");
        const resp = e.responsibilities
          ? `<div class="elg-muted">${U.nl2br(e.responsibilities)}</div>`
          : "";
        return `<div class="elg-item">
        <div class="elg-hdr"><strong>${title}</strong>${
          company ? " • " + company : ""
        }</div>
        <div class="elg-range">${start} - ${end}</div>
        ${resp}
      </div>`;
      })
      .join("");

    const edu = (d.educations || [])
      .map((e) => {
        return `<div class="elg-item">
        <div class="elg-hdr"><strong>${esc(e.degree_title || "")}</strong>${
          e.level_name ? " • " + esc(e.level_name) : ""
        }</div>
        <div class="elg-muted">${esc(e.institution_name || "")}</div>
        <div class="elg-range">${esc(e.start_year || "")} - ${esc(
          e.end_year || ""
        )}</div>
      </div>`;
      })
      .join("");

    const projects = (d.projects || [])
      .map((p) => {
        return `<div class="elg-item">
        <div class="elg-hdr"><strong>${esc(p.title || "")}</strong></div>
        ${
          p.project_link
            ? `<div><a href="${esc(
                p.project_link
              )}" target="_blank" class="elg-link"><i class="fas fa-link"></i> ${esc(
                p.project_link
              )}</a></div>`
            : ""
        }
        ${
          p.description
            ? `<div class="elg-muted">${U.nl2br(p.description)}</div>`
            : ""
        }
      </div>`;
      })
      .join("");

    const certs = (d.certifications || [])
      .map((c) => {
        const yr = c.issue_date ? new Date(c.issue_date).getFullYear() : "";
        return `<div class="elg-item">
        <div class="elg-hdr"><strong>${esc(
          c.certification_name || ""
        )}</strong>${
          c.issuing_organization ? " • " + esc(c.issuing_organization) : ""
        }</div>
        ${yr ? `<div class="elg-range">${yr}</div>` : ""}
        ${
          c.certificate_url
            ? `<div><a class="elg-link" target="_blank" href="${esc(
                c.certificate_url
              )}"><i class="fas fa-external-link-alt"></i> Certificate</a></div>`
            : ""
        }
      </div>`;
      })
      .join("");

    return `
      <style>
        .cv-elegant { max-width: 1000px; margin: 16px auto; background:#fff; border-radius: 16px; overflow:hidden; box-shadow: 0 6px 22px rgba(31,40,105,0.08); }
        .cv-elegant .top {
          display:grid; grid-template-columns: 280px 1fr; min-height: 160px; background: #1f2869; color:#fff;
        }
        .cv-elegant .left {
          padding: 20px; background: #1f2869; color:#fff;
        }
        .cv-elegant .avatar { width: 120px; height:120px; border-radius: 12px; object-fit: cover; border: 4px solid rgba(255,255,255,0.4); box-shadow: 0 6px 16px rgba(0,0,0,0.2); }
        .cv-elegant .name { margin: 10px 0 0; font-size: 20px; font-weight: 700; }
        .cv-elegant .role { margin: 2px 0 8px; color:#d7dbff; font-weight: 500; }
        .cv-elegant .elg-list { list-style:none; margin:12px 0 0; padding:0; display:grid; gap:8px; font-size:13px; }
        .cv-elegant .elg-list i { width:16px; color:#9fb0ff; margin-right:8px; }
        .cv-elegant .right { padding: 20px; background: linear-gradient(180deg,#1f2869 40%, #fff 40%); }
        .cv-elegant .card { background:#fff; border-radius: 12px; padding: 16px; box-shadow: 0 8px 30px rgba(31,40,105,0.12); }
        .cv-elegant .body { padding: 16px; display: grid; grid-template-columns: 280px 1fr; gap: 16px; }
        .cv-elegant .sec h3 { margin:0 0 10px; font-size:14px; color:#3843d0; letter-spacing:.4px; text-transform:uppercase; }
        .cv-elegant .elg-item { padding: 10px 0; border-bottom: 1px dashed #e6e9ff; }
        .cv-elegant .elg-item:last-child { border-bottom: 0; }
        .cv-elegant .elg-hdr { color:#1f2869; }
        .cv-elegant .elg-muted { color:#666; font-size:13px; }
        .cv-elegant .elg-range { color:#0b7d3e; font-size:12px; font-weight:600; }
        .cv-elegant .elg-link { color:#3843d0; text-decoration:none; }
        .cv-elegant ul.plain { list-style: disc inside; padding-left: 0; color:#1f2869; }
        .cv-elegant .resume { padding: 12px 16px; }
        @media print {
          .cv-elegant { box-shadow:none; border-radius:0; }
        }
      </style>
      <div class="cv-elegant">
        <div class="top">
          <div class="left">
            <img class="avatar" src="${esc(d.profileImg)}" alt="Profile">
            <div class="name">${esc(d.fullName)}</div>
            <div class="role">${esc(d.jobTitle || "Job Seeker")}</div>
            ${contacts}
          </div>
          <div class="right">
            <div class="card">
              ${
                d.bio
                  ? `<div class="sec"><h3>Profile Summary</h3><div class="elg-muted">${U.nl2br(
                      d.bio
                    )}</div></div>`
                  : ""
              }
            </div>
          </div>
        </div>

        <div class="body">
          <div class="card">
            ${
              (d.skills || []).length
                ? `<div class="sec"><h3>Skills</h3><ul class="plain">${skills}</ul></div>`
                : ""
            }
            ${
              (d.languages || []).length
                ? `<div class="sec"><h3>Languages</h3><ul class="plain">${languages}</ul></div>`
                : ""
            }
            ${
              (d.certifications || []).length
                ? `<div class="sec"><h3>Certifications</h3>${certs}</div>`
                : ""
            }
            ${
              d.latestResume
                ? `<div class="resume"><a class="elg-link" target="_blank" href="${esc(
                    d.latestResume
                  )}"><i class="fas fa-file-pdf"></i> View Latest Uploaded Resume</a></div>`
                : ""
            }
          </div>
          <div class="card">
            ${
              (d.experiences || []).length
                ? `<div class="sec"><h3>Experience</h3>${exp}</div>`
                : ""
            }
            ${
              (d.educations || []).length
                ? `<div class="sec"><h3>Education</h3>${edu}</div>`
                : ""
            }
            ${
              (d.projects || []).length
                ? `<div class="sec"><h3>Projects</h3>${projects}</div>`
                : ""
            }
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
    badge: "2-Column",
  });
})();
