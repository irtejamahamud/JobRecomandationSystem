(function(){
  const id = 'modern';
  const name = 'Modern';

  function esc(s){ if(s===null||s===undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }
  const U = window.CVUtils || { esc, nl2br:(s)=>esc(s).replace(/\n/g,'<br>'), fmtMonthYear:()=>'', fmtDate:(d)=>{ try { return new Date(d).toLocaleDateString(undefined,{day:'2-digit',month:'short',year:'numeric'});}catch(e){return d||'';} } };

  function preview(){
    return `<div style="font-size:10px;padding:6px;text-align:center;">`
      + `<div style=\"height:10px;background:#f3f6ff;border-radius:4px;margin:4px 14px;\"></div>`
      + `<div style=\"height:6px;background:#0b7d3e;border-radius:4px;margin:6px 26px;\"></div>`
      + `<div style=\"height:6px;background:#3843d0;border-radius:4px;margin:6px 34px;\"></div>`
      + `</div>`;
  }

  function section(title, inner){ if(!inner) return ''; return `<div class=\"cv-sec\"><h3>${esc(title)}</h3>${inner}</div>`; }

  function renderFull(d){
    const contacts = [];
    if(d.email) contacts.push(`<span><i class='fas fa-envelope'></i>${esc(d.email)}</span>`);
    if(d.mobile) contacts.push(`<span><i class='fas fa-phone'></i>${esc(d.mobile)}</span>`);
    if(d.website) contacts.push(`<span><i class='fas fa-globe'></i>${esc(d.website)}</span>`);
    if(d.address) contacts.push(`<span><i class='fas fa-map-marker-alt'></i>${esc(d.address)}</span>`);
    if(d.dob) contacts.push(`<span><i class='fas fa-birthday-cake'></i>${U.fmtDate(d.dob)}</span>`);
    if(d.gender) contacts.push(`<span><i class='fas fa-venus-mars'></i>${esc(d.gender)}</span>`);
    if(d.marital) contacts.push(`<span><i class='fas fa-heart'></i>${esc(d.marital)}</span>`);

    const exp = (d.experiences||[]).map(e=>{
      const company = esc(e.company_name||'');
      const title = esc(e.job_title||'');
      const start = U.fmtMonthYear(e.start_date)||'';
      const end = e.end_date?U.fmtMonthYear(e.end_date):'Present';
      const resp = e.responsibilities?`<div class=\"muted resp\">${U.nl2br(e.responsibilities)}</div>`:'';
      return `<div class=\"item\"><h4>${title}${company?` • ${company}`:''}</h4><div class=\"range\">${start} - ${end}</div>${resp}</div>`;
    }).join('');

    const edu = (d.educations||[]).map(e=>{
      return `<div class=\"item\"><h4>${esc(e.degree_title||'')}${e.level_name?` • ${esc(e.level_name)}`:''}</h4><div class=\"muted\">${esc(e.institution_name||'')}</div><div class=\"range\">${esc(e.start_year||'')} - ${esc(e.end_year||'')}</div></div>`;
    }).join('');

    const skills = (d.skills||[]).map(s=>`<span class=\"badge\">${esc(s.name)} — ${esc(s.proficiency)}</span>`).join('');
    const projects = (d.projects||[]).map(p=>{
      return `<div class=\"item\"><h4>${esc(p.title||'')}</h4>`
        + (p.project_link?`<div class=\"muted\" style=\"margin:4px 0;\"><a class=\"proj-link\" target=\"_blank\" href=\"${esc(p.project_link)}\"><i class=\"fas fa-link\"></i> ${esc(p.project_link)}</a></div>`:'')
        + (p.description?`<div class=\"muted\">${U.nl2br(p.description)}</div>`:'')
        + `</div>`; }).join('');

    const certs = (d.certifications||[]).map(c=>{
      const yr = c.issue_date?new Date(c.issue_date).getFullYear():'';
      return `<div class=\"item\"><h4>${esc(c.certification_name||'')}${c.issuing_organization?` • ${esc(c.issuing_organization)}`:''}</h4>${yr?`<div class=\"range\">${yr}</div>`:''}`
        + (c.certificate_url?`<div class=\"muted\" style=\"margin-top:4px;\"><a class=\"proj-link\" target=\"_blank\" href=\"${esc(c.certificate_url)}\"><i class=\"fas fa-external-link-alt\"></i> Certificate</a></div>`:'')
        + `</div>`; }).join('');

    const langs = (d.languages||[]).map(l=>`<span class=\"badge\">${esc(l.language_name)} — ${esc(l.proficiency)}</span>`).join('');

    const resume = d.latestResume?`<div class=\"cv-sec\"><a class=\"resume-link\" target=\"_blank\" href=\"${esc(d.latestResume)}\"><i class=\"fas fa-file-pdf\"></i> View Latest Uploaded Resume</a></div>`:'';

    return `
      <style>
        .cv-modern { max-width:900px;margin:24px auto;background:#fff;border-radius:14px;box-shadow:0 6px 22px rgba(31,40,105,0.08);overflow:hidden;font-family:'Poppins',Arial,sans-serif; }
        .cv-modern .header { display:grid;grid-template-columns:120px 1fr;gap:18px;padding:24px;background:#f3f6ff;border-bottom:1px solid #eef1ff; }
        .cv-modern .avatar { width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,.08); }
        .cv-modern h1 { margin:0;font-size:28px;color:#1f2869; }
        .cv-modern .subtitle { margin:6px 0 10px;color:#667;font-weight:500; }
        .cv-modern .contacts { display:flex;flex-wrap:wrap;gap:10px 18px;color:#444;font-size:13px; }
        .cv-modern .contacts span { display:inline-flex;align-items:center;gap:8px; }
        .cv-modern .contacts i { color:#3843d0; }
        .cv-modern .body { padding:20px 24px 28px; }
        .cv-modern .cv-sec { margin-top:18px; }
        .cv-modern .cv-sec h3 { margin:0 0 12px;font-size:15px;color:#3843d0;letter-spacing:.4px;text-transform:uppercase;position:relative;padding-bottom:6px; }
        .cv-modern .cv-sec h3:after { content:'';position:absolute;left:0;bottom:0;width:46px;height:3px;background:#0b7d3e;border-radius:2px; }
        .cv-modern .timeline { display:grid;gap:14px; }
        .cv-modern .item { padding:12px 14px;border:1px solid #eef1ff;border-radius:12px;background:#fff; }
        .cv-modern .item h4 { margin:0 0 6px;color:#1f2869;font-size:15px; }
        .cv-modern .item .muted { color:#666;font-size:13px; }
        .cv-modern .item .range { color:#0b7d3e;font-weight:600;font-size:12px; }
        .cv-modern .grid-2 { display:grid;gap:16px;grid-template-columns:1fr 1fr; }
        .cv-modern .badge { font-size:12px;padding:6px 10px;border-radius:999px;background:#f3f6ff;color:#1f2869;border:1px solid #e1e7ff; }
        .cv-modern .skill-badges,.cv-modern .lang-badges { display:flex;flex-wrap:wrap;gap:10px; }
        .cv-modern .proj-link { color:#3843d0;text-decoration:none; }
        .cv-modern .proj-link:hover { text-decoration:underline; }
        .cv-modern .resume-link { display:inline-flex;align-items:center;gap:8px;color:#0b7d3e;text-decoration:none;font-weight:600; }
        @media print { .cv-modern { box-shadow:none;border-radius:0; } }
      </style>
      <div class='cv-modern'>
        <div class='header'>
          <img class='avatar' src='${esc(d.profileImg)}' alt='Profile'>
          <div>
            <h1>${esc(d.fullName)}</h1>
            <div class='subtitle'>${esc(d.jobTitle||'Job Seeker')}</div>
            <div class='contacts'>${contacts.join('')}</div>
          </div>
        </div>
        <div class='body'>
          ${d.bio?section('Profile Summary', `<div class='cv-text'>${U.nl2br(d.bio)}</div>`):''}
          ${(d.experiences||[]).length?section('Experience', `<div class='timeline'>${exp}</div>`):''}
          <div class='cv-sec'>
            <div class='grid-2'>
              ${(d.educations||[]).length?section('Education', `<div class='timeline'>${edu}</div>`):''}
              ${(d.skills||[]).length?section('Skills', `<div class='skill-badges'>${skills}</div>`):''}
            </div>
          </div>
          ${(d.projects||[]).length?section('Projects', `<div class='timeline'>${projects}</div>`):''}
          ${(d.certifications||[]).length?section('Certifications', `<div class='timeline'>${certs}</div>`):''}
          ${(d.languages||[]).length?section('Languages', `<div class='lang-badges'>${langs}</div>`):''}
          ${resume}
        </div>
      </div>`;
  }

  window.TEMPLATES = window.TEMPLATES || [];
  window.TEMPLATES.push({ id, name, renderPreview:preview, renderFull, badge:'New' });
})();