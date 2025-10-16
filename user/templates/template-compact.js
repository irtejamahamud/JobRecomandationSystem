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
    return `
      <div style="width:88%;height:78%;border-radius:8px;overflow:hidden;display:flex;box-shadow:0 6px 18px rgba(31,40,105,0.06);">
        <div style="width:36%;background:linear-gradient(180deg,#3843d0,#2563eb);padding:8px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;">
          <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.12);margin-bottom:8px"></div>
          <div style="font-weight:700;font-size:12px">Full Name</div>
          <div style="font-size:11px;opacity:0.9;margin-top:6px">Job Title</div>
        </div>
        <div style="flex:1;padding:10px;background:#fff;display:flex;flex-direction:column;justify-content:center;">
          <div style="height:8px;background:#eef4ff;border-radius:4px;width:70%;margin-bottom:8px"></div>
          <div style="height:6px;background:#f6f8ff;border-radius:4px;width:90%"></div>
        </div>
      </div>`;
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
        .cv-compact { max-width: 880px; margin: 18px auto; background: linear-gradient(180deg,#ffffff,#fbfdff); border-radius:12px; box-shadow:0 8px 28px rgba(31,40,105,0.08); overflow:hidden; }
        .cv-compact .inner { display:grid; grid-template-columns: 260px 1fr; gap:20px; padding:18px; }
        .cv-compact .sidebar { background:linear-gradient(180deg,#3843d0,#2563eb); color:#fff; padding:18px; border-radius:8px; }
        .cv-compact .avatar { width:88px;height:88px;border-radius:12px;object-fit:cover;margin:0 auto 10px;box-shadow:0 6px 18px rgba(0,0,0,0.25); }
        .cv-compact .name{ text-align:center;font-size:20px;margin:6px 0 2px;font-weight:700;color:#fff }
        .cv-compact .role{ text-align:center;color:rgba(255,255,255,0.9);font-weight:600 }
        .cv-compact .contact{ margin-top:12px;font-size:13px; }
        .cv-compact .contact div{ display:flex;gap:8px;align-items:center;padding:6px 0;color:rgba(255,255,255,0.95) }
        .cv-compact .skills{ margin-top:12px; display:flex; flex-wrap:wrap; gap:8px }
        .cv-compact .chip{ background: rgba(255,255,255,0.12); padding:6px 8px;border-radius:999px;font-size:13px }

        .cv-compact .content{ padding:6px 2px; }
        .cv-compact h3{ margin:0 0 8px;color:#1f2869;font-size:14px }
        .cv-compact .section{ background:#fff;padding:12px;border-radius:8px;margin-bottom:12px; box-shadow:0 2px 10px rgba(0,0,0,0.03) }
        .cv-compact ul{ margin:0;padding-left:16px }
        .cv-compact li{ margin-bottom:8px }
        .cv-compact .muted{ color:#666;font-size:13px }

        .proj-link{ color:#2563eb;text-decoration:none }
        .proj-link:hover{ text-decoration:underline }

        @media (max-width:800px){ .cv-compact .inner{ grid-template-columns:1fr; } .cv-compact .sidebar{ order:2 } }
      </style>

      <div class="cv-compact">
        <div class="inner">
          <div class="sidebar">
            <img class="avatar" src="${esc(d.profileImg)}" alt="Profile">
            <div class="name">${esc(d.fullName)}</div>
            <div class="role">${esc(d.jobTitle||'Job Seeker')}</div>
            <div class="contact">
              ${d.email?`<div><i class="fas fa-envelope"></i><span>${esc(d.email)}</span></div>`:''}
              ${d.mobile?`<div><i class="fas fa-phone"></i><span>${esc(d.mobile)}</span></div>`:''}
              ${d.website?`<div><i class="fas fa-globe"></i><span>${esc(d.website)}</span></div>`:''}
            </div>
            ${ skills ? `<div class="skills">${(d.skills||[]).map(s=>`<span class="chip">${esc(s.name)}</span>`).join('')}</div>` : '' }
          </div>
          <div class="content">
            ${ d.bio?`<div class="section"><h3>Summary</h3><div class="muted">${U.nl2br(d.bio)}</div></div>`:'' }
            ${ (d.experiences||[]).length?`<div class="section"><h3>Experience</h3><ul>${exp}</ul></div>`:'' }
            ${ (d.educations||[]).length?`<div class="section"><h3>Education</h3><ul>${edu}</ul></div>`:'' }
            ${ (d.projects||[]).length?`<div class="section"><h3>Projects</h3><ul>${projs.replace(/href="/g,'class="proj-link" href="')}</ul></div>`:'' }
            ${ (d.certifications||[]).length?`<div class="section"><h3>Certifications</h3><ul>${certs}</ul></div>`:'' }
            ${ d.latestResume?`<div class="section"><a href="${esc(d.latestResume)}" target="_blank"><i class="fas fa-file-pdf"></i> View Latest Uploaded Resume</a></div>`:'' }
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
    badge: "Minimal",
  });
})();
