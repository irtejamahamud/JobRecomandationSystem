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
    return `
      <div style="width:88%;height:84%;background:linear-gradient(180deg,#ffffff 0,#ffffff 55%,#f8f7f5 55%);border-radius:8px;border:1px solid rgba(20,20,20,0.04);box-shadow:0 6px 18px rgba(20,20,20,0.06);display:flex;align-items:stretch;overflow:hidden;">
        <div style="width:220px;background:linear-gradient(180deg,#1b1b1b 0,#2e2b2b 100%);padding:18px;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;">
          <div style="width:84px;height:84px;border-radius:12px;background:#fff;opacity:0.12;margin-bottom:12px;"></div>
          <div style="font-weight:700;font-size:14px;color:#fff;">Full Name</div>
          <div style="font-size:11px;color:rgba(255,255,255,0.75)">Job Title</div>
        </div>
        <div style="flex:1;padding:14px 18px;">
          <div style="height:18px;background:linear-gradient(90deg,#d4c6a8,#fff);width:60%;border-radius:3px;margin-bottom:8px;"></div>
          <div style="height:10px;background:rgba(10,10,10,0.03);width:80%;border-radius:3px;"></div>
        </div>
      </div>
    `;
  }

  function listToHtml(arr, mapper) {
    return (arr || []).map(mapper).join('');
  }

  function renderFull(d) {
    const skills = listToHtml(d.skills, s => `<li class="elg-skill">${esc(s.name)}<span>${esc(s.proficiency||'')}</span></li>`);
    const languages = listToHtml(d.languages, l => `<li class="elg-lang">${esc(l.language_name)}<span>${esc(l.proficiency||'')}</span></li>`);

    const contacts = `
      <ul class="elg-contacts">
        ${d.email?`<li><i class="fas fa-envelope"></i> ${esc(d.email)}</li>`:''}
        ${d.mobile?`<li><i class="fas fa-phone"></i> ${esc(d.mobile)}</li>`:''}
        ${d.website?`<li><i class="fas fa-globe"></i> ${esc(d.website)}</li>`:''}
        ${d.address?`<li><i class="fas fa-map-marker-alt"></i> ${esc(d.address)}</li>`:''}
      </ul>`;

    const exp = listToHtml(d.experiences, e => {
      const start = U.fmtMonthYear(e.start_date) || '';
      const end = e.end_date ? U.fmtMonthYear(e.end_date) : 'Present';
      return `
        <div class="elg-row">
          <div class="elg-row-left"><div class="elg-role">${esc(e.job_title||'')}</div><div class="elg-company">${esc(e.company_name||'')}</div></div>
          <div class="elg-row-right">${start} — ${end}</div>
          ${e.responsibilities?`<div class="elg-desc">${U.nl2br(e.responsibilities)}</div>`:''}
        </div>
      `;
    });

    const edu = listToHtml(d.educations, e => `
      <div class="elg-row">
        <div class="elg-row-left"><div class="elg-role">${esc(e.degree_title||'')}</div><div class="elg-company">${esc(e.institution_name||'')}</div></div>
        <div class="elg-row-right">${esc(e.start_year||'')} — ${esc(e.end_year||'')}</div>
      </div>
    `);

    const projects = listToHtml(d.projects, p => `
      <div class="elg-row">
        <div class="elg-row-left"><div class="elg-role">${esc(p.title||'')}</div></div>
        ${p.description?`<div class="elg-desc">${U.nl2br(p.description)}</div>`:''}
      </div>
    `);

    const certs = listToHtml(d.certifications, c => `
      <div class="elg-row"><div class="elg-row-left"><div class="elg-role">${esc(c.certification_name||'')}</div><div class="elg-company">${esc(c.issuing_organization||'')}</div></div><div class="elg-row-right">${c.issue_date?new Date(c.issue_date).getFullYear():''}</div></div>
    `);

    return `
      <style>
        .cv-elegant { max-width:900px;margin:18px auto;background:#fff;border-radius:12px;overflow:hidden;font-family: Inter, 'Segoe UI', Roboto, system-ui, -apple-system, 'Helvetica Neue', Arial; color:#222; box-shadow: 0 10px 30px rgba(22,22,22,0.06); }
        .cv-elegant .wrap { display:grid; grid-template-columns: 260px 1fr; }
        .cv-elegant .side { background:linear-gradient(180deg,#111 0,#2b2b2b 100%); color:#fff; padding:26px 20px; display:flex; flex-direction:column; gap:12px; }
        .cv-elegant .side .avatar { width:110px;height:110px;border-radius:10px;object-fit:cover;border:3px solid rgba(255,255,255,0.08); box-shadow:0 8px 20px rgba(0,0,0,0.3); }
        .cv-elegant .name { font-family: Georgia, 'Times New Roman', serif; font-size:20px; font-weight:700; color:#fff;margin-top:6px; }
        .cv-elegant .title { color:#e6d8a8; font-weight:600; margin-bottom:8px; }
        .cv-elegant .divider { height:1px;background:rgba(255,255,255,0.06); margin:8px 0 12px; }
        .cv-elegant .elg-contacts { list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px;font-size:13px;color:rgba(255,255,255,0.9); }
        .cv-elegant .elg-contacts i { width:16px;margin-right:8px;color:rgba(255,255,255,0.7); }
        .cv-elegant .side .section-title { font-size:12px; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.6); margin-top:8px; }

  .cv-elegant .main { padding:34px 28px 28px; background:#fff; position:relative; }
  .cv-elegant .profile { margin-bottom:12px; color:#444; }
  /* floating summary that sits visually between sidebar and main content */
  .cv-elegant .summary-floating { position:relative; max-width:760px; margin: -46px auto 18px; background:#fff; border-radius:12px; padding:22px 28px; box-shadow:0 18px 40px rgba(20,20,40,0.12); text-align:center; }
  .cv-elegant .summary-title { font-size:13px; font-weight:800; color:#2942d6; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px; }
  .cv-elegant .summary-text { color:#6b7280; line-height:1.6; font-size:14px; margin:0; }
        .cv-elegant h3 { margin:0 0 10px;font-size:13px;color:#1f2937;letter-spacing:0.6px;text-transform:uppercase; }
        .elg-card { background:linear-gradient(180deg,#fff,#fbfbfa); padding:14px;border-radius:10px; box-shadow: 0 6px 20px rgba(15,15,15,0.03); margin-bottom:14px; }
        .elg-row { padding:8px 0;border-bottom:1px dashed rgba(30,30,30,0.04); }
        .elg-row:last-child { border-bottom:0; }
        .elg-row-left { float:left; max-width:72%; }
        .elg-row-right { float:right; color:#6b7280;font-size:12px; }
        .elg-role { font-weight:700;color:#111; }
        .elg-company { color:#6b7280;font-size:13px;margin-top:3px; }
        .elg-desc { clear:both;margin-top:8px;color:#4b5563;font-size:13px; }

        .elg-skill, .elg-lang { display:flex; justify-content:space-between; padding:6px 8px; border-radius:6px; background:linear-gradient(90deg,rgba(15,15,15,0.02),rgba(15,15,15,0.00)); margin-bottom:8px; }
        .elg-skill span, .elg-lang span { color:#6b7280;font-size:12px; }

        @media print { .cv-elegant { box-shadow:none;border-radius:0; } .cv-elegant .side { background:#fff;color:#000 } }
      </style>

      <div class="cv-elegant">
        <div class="wrap">
          <aside class="side">
            <img class="avatar" src="${esc(d.profileImg)}" alt="Avatar">
            <div class="name">${esc(d.fullName)}</div>
            <div class="title">${esc(d.jobTitle || 'Job Seeker')}</div>
            <div class="divider"></div>
            ${contacts}
            ${ (d.bio) ? `<div class="section-title">About</div><div class="profile elg-desc">${U.nl2br(d.bio)}</div>` : '' }
            <div style="flex:1"></div>
            ${ d.latestResume ? `<a class="elg-link" href="${esc(d.latestResume)}" target="_blank" style="color:#e6d8a8;text-decoration:none;font-weight:600">Download Resume</a>` : '' }
          </aside>
          <main class="main">
            ${ d.bio ? `<div class="summary-floating"><div class="summary-title">Profile Summary</div><p class="summary-text">${U.nl2br(d.bio)}</p></div>` : '' }

            ${ (d.experiences||[]).length ? `<div class="elg-card"><h3>Experience</h3>${exp}</div>` : '' }

            <div style="display:grid;grid-template-columns:1fr 320px;gap:16px;">
              <div>
                ${ (d.educations||[]).length ? `<div class="elg-card"><h3>Education</h3>${edu}</div>` : '' }
                ${ (d.projects||[]).length ? `<div class="elg-card"><h3>Projects</h3>${projects}</div>` : '' }
              </div>
              <aside>
                ${ (d.skills||[]).length ? `<div class="elg-card"><h3>Skills</h3><ul style="list-style:none;padding:0;margin:0">${skills}</ul></div>` : '' }
                ${ (d.languages||[]).length ? `<div class="elg-card"><h3>Languages</h3><ul style="list-style:none;padding:0;margin:0">${languages}</ul></div>` : '' }
                ${ (d.certifications||[]).length ? `<div class="elg-card"><h3>Certifications</h3>${certs}</div>` : '' }
              </aside>
            </div>
          </main>
        </div>
      </div>
    `;
  }

  window.TEMPLATES = window.TEMPLATES || [];
  window.TEMPLATES.push({ id, name, renderPreview, renderFull, badge: 'Elegant' });
})();
