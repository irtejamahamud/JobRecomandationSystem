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
      <div style="width:88%;height:84%;background:#fff;border-radius:10px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 4px 10px rgba(0,0,0,0.05);overflow:hidden;display:flex;">
        <div style="width:35%;background:#1f2937;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;padding:12px;">
          <div style="width:66px;height:66px;border-radius:50%;background:rgba(255,255,255,0.12);margin-bottom:10px;"></div>
          <div style="font-weight:700;font-size:13px;line-height:1;">Full Name</div>
          <div style="font-size:11px;opacity:0.85;margin-top:6px;">Job Title</div>
        </div>
        <div style="flex:1;padding:14px;">
          <div style="width:60%;height:12px;background:rgba(0,0,0,0.05);border-radius:3px;margin-bottom:6px;"></div>
          <div style="width:80%;height:8px;background:rgba(0,0,0,0.03);border-radius:3px;"></div>
        </div>
      </div>
    `;
  }

  function listToHtml(arr, mapper) {
    return (arr || []).map(mapper).join('');
  }

  function safeUrl(u){
    try{
      if(!u) return '';
      var s = String(u).trim();
      if(/^https?:\/\//i.test(s)) return s;
      // allow protocol relative
      if(/^\/\//.test(s)) return s;
      return '';
    }catch(e){return '';}
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
          <div class="elg-row-left">
            <div class="elg-role">${esc(e.job_title||'')}</div>
            <div class="elg-company">${esc(e.company_name||'')}</div>
          </div>
          <div class="elg-row-right">${start} — ${end}</div>
          ${e.responsibilities?`<div class="elg-desc">${U.nl2br(e.responsibilities)}</div>`:''}
        </div>`;
    });

    const edu = listToHtml(d.educations, e => `
      <div class="elg-row">
        <div class="elg-row-left">
          <div class="elg-role">${esc(e.degree_title||'')}</div>
          <div class="elg-company">${esc(e.institution_name||'')}</div>
        </div>
        <div class="elg-row-right">${esc(e.start_year||'')} — ${esc(e.end_year||'')}</div>
      </div>`);

    const projects = listToHtml(d.projects, p => {
      const url = safeUrl(p.project_link);
      const title = esc(p.title||'');
      const titleHtml = url ? `<a class="elg-project-link" href="${url}" target="_blank" rel="noopener">${title}</a>` : `<div class="elg-role">${title}</div>`;
      return `
      <div class="elg-project">
        <div class="elg-project-title">${titleHtml}</div>
        ${p.description?`<div class="elg-desc">${U.nl2br(p.description)}</div>`:''}
      </div>`;
    });

    const certs = listToHtml(d.certifications, c => `
      <div class="elg-row">
        <div class="elg-row-left">
          <div class="elg-role">${esc(c.certification_name||'')}</div>
          <div class="elg-company">${esc(c.issuing_organization||'')}</div>
        </div>
        <div class="elg-row-right">${c.issue_date?new Date(c.issue_date).getFullYear():''}</div>
      </div>`);

    return `
      <style>
        .cv-elegant {
          max-width: 920px;
          margin: 24px auto;
          background: #ffffff;
          border-radius: 12px;
          overflow: hidden;
          font-family: 'Inter', 'Segoe UI', Roboto, Arial, sans-serif;
          color: #1f2937;
          box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }
        .cv-elegant .wrap {
          display: grid;
          grid-template-columns: 260px 1fr;
          gap: 18px;
        }
        .cv-elegant .side {
          background: #1f2937;
          color: #f9fafb;
          padding: 26px 18px;
          display: flex;
          flex-direction: column;
          gap: 12px;
          min-height: 100%;
          align-items: center;
        }
        .cv-elegant .side .avatar {
          width: 110px;
          height: 110px;
          border-radius: 50%;
          object-fit: cover;
          margin: 0 auto;
          border: 3px solid rgba(255, 255, 255, 0.12);
          box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        }
        .cv-elegant .name {
          text-align: center;
          font-size: 20px;
          font-weight: 700;
          margin-top: 8px;
          color: #ffffff;
        }
        .cv-elegant .title {
          text-align: center;
          font-size: 13px;
          font-weight: 500;
          color: #d1d5db;
          margin-bottom: 10px;
        }
        .cv-elegant .divider {
          height: 1px;
          background: rgba(255,255,255,0.12);
          margin: 10px 0 14px;
        }
        .elg-contacts {
          list-style: none;
          padding: 0;
          margin: 0;
          display: flex;
          flex-direction: column;
          gap: 8px;
          font-size: 13px;
          color: rgba(255,255,255,0.9);
        }
        .elg-contacts i {
          width: 16px;
          margin-right: 8px;
          color: rgba(255,255,255,0.6);
        }
        .side .section-title {
          font-size: 12px;
          text-transform: uppercase;
          letter-spacing: 1px;
          color: rgba(255,255,255,0.6);
          margin-top: 12px;
        }

        .cv-elegant .main {
          padding: 34px 30px 28px;
          background: #fff;
        }

        .cv-elegant h3 {
          margin: 0 0 12px;
          font-size: 14px;
          font-weight: 700;
          color: #1f2937;
          border-left: 3px solid #2563eb;
          padding-left: 8px;
        }

        .elg-card {
          background: #f9fafb;
          padding: 16px 18px;
          border-radius: 10px;
          margin-bottom: 18px;
          box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }

        .elg-row {
          display:flex;
          justify-content:space-between;
          align-items:flex-start;
          gap:12px;
          padding: 8px 0;
          border-bottom: 1px dashed rgba(0,0,0,0.05);
        }
        .elg-row:last-child { border-bottom: none; }

        .elg-row-left { flex:1 1 auto; min-width:0; }
        .elg-row-right { flex:0 0 120px; text-align:right; color: #6b7280; font-size: 12px; }
        .elg-role { font-weight: 600; color: #111827; font-size: 14px; }
        .elg-company { color: #4b5563; font-size: 13px; margin-top: 3px; }
        .elg-desc { clear: both; margin-top: 8px; color: #374151; font-size: 13px; line-height: 1.5; }

        .elg-skill, .elg-lang {
          display: flex;
          justify-content: space-between;
          background: rgba(255,255,255,0.04);
          padding: 8px 10px;
          border-radius: 8px;
          margin-bottom: 8px;
          font-size: 13px;
        }

        /* Small row for skills + languages */
        .elg-small-row { display:flex; gap:12px; margin-top:12px; }
        .elg-card.elg-small { flex:1 1 0; padding:12px; }
        .elg-list .elg-skill, .elg-list .elg-lang { padding:6px 8px; font-size:13px; }

        /* Projects */
        .elg-project { padding:10px 0; border-bottom:1px dashed rgba(0,0,0,0.05); }
        .elg-project:last-child { border-bottom:none; }
        .elg-project-title { font-weight:600; color:#111827; font-size:14px; margin-bottom:6px; }
        .elg-project-link { color:#2563eb; text-decoration:none; }
        .elg-project-link:hover { text-decoration:underline; }
        .elg-skill span, .elg-lang span {
          color: #6b7280;
          font-size: 12px;
        }

        @media (max-width:900px) {
          .cv-elegant .wrap { grid-template-columns: 1fr; }
          .cv-elegant .side { flex-direction:row; align-items:center; gap:12px; padding:14px; }
          .cv-elegant .side .avatar { width:72px;height:72px; }
          .cv-elegant .name { text-align:left; }
          .cv-elegant .title { text-align:left; }
        }
        @media print {
          .cv-elegant { box-shadow: none; border-radius: 0; }
          .cv-elegant .side { background: #fff; color: #000; }
        }
      </style>

      <div class="cv-elegant">
        <div class="wrap">
          <aside class="side">
            <img class="avatar" src="${esc(d.profileImg)}" alt="Avatar">
            <div class="name">${esc(d.fullName)}</div>
            <div class="title">${esc(d.jobTitle || 'Professional')}</div>
            <div class="divider"></div>
            ${contacts}
            ${d.bio ? `<div class="section-title">About</div><div class="profile elg-desc">${U.nl2br(d.bio)}</div>` : ''}
          </aside>
          <main class="main">
            ${ (d.experiences||[]).length ? `<div class="elg-card"><h3>Experience</h3>${exp}</div>` : '' }

            <!-- Education and Projects full width -->
            ${ (d.educations||[]).length ? `<div class="elg-card"><h3>Education</h3>${edu}</div>` : '' }
            ${ (d.projects||[]).length ? `<div class="elg-card"><h3>Projects</h3>${projects}</div>` : '' }

            <!-- Compact row: Skills and Languages side-by-side -->
            <div class="elg-small-row">
              ${ (d.skills||[]).length ? `<div class="elg-card elg-small"><h3>Skills</h3><ul class="elg-list" style="list-style:none;padding:0;margin:0">${skills}</ul></div>` : '' }
              ${ (d.languages||[]).length ? `<div class="elg-card elg-small"><h3>Languages</h3><ul class="elg-list" style="list-style:none;padding:0;margin:0">${languages}</ul></div>` : '' }
            </div>

            ${ (d.certifications||[]).length ? `<div class="elg-card"><h3>Certifications</h3>${certs}</div>` : '' }
          </main>
        </div>
      </div>
    `;
  }

  window.TEMPLATES = window.TEMPLATES || [];
  window.TEMPLATES.push({ id, name, renderPreview, renderFull, badge: 'Elegant' });
})();
