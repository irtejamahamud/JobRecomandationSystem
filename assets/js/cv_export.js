// cv_export.js - centralized PDF export utility for CV
(function(){
  const CDNS = [
    'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js',
    'https://unpkg.com/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js'
  ];
  let state = { loading:false, tried:0, ready: false, queue: [] };

  function loadNext(){
    if (window.html2pdf){ state.ready = true; flush(); return; }
    if (state.tried >= CDNS.length){ flush(new Error('All CDNs failed')); return; }
    const url = CDNS[state.tried++];
    state.loading = true;
    const s = document.createElement('script');
    s.src = url; s.referrerPolicy = 'no-referrer';
    s.onload = ()=>{ state.loading=false; state.ready=true; flush(); };
    s.onerror = ()=>{ state.loading=false; setTimeout(loadNext, 120); };
    document.head.appendChild(s);
  }

  function flush(err){
    state.queue.splice(0).forEach(fn=>fn(err));
  }

  function ensure(cb){
    if (window.html2pdf){ return cb(); }
    state.queue.push(cb);
    if (!state.loading) loadNext();
  }

  function cleanClone(node){
    const clone = node.cloneNode(true);
    clone.querySelectorAll('.no-print').forEach(el=>el.remove());
    return clone;
  }

  function exportPDF(el, filename, options){
    ensure(err=>{
      if (err || !window.html2pdf){
        console.warn('html2pdf unavailable, falling back to print()', err);
        window.print();
        return;
      }
      try {
        const opt = Object.assign({
          margin:0.4,
          filename: filename || 'cv.pdf',
          image:{ type:'jpeg', quality:0.98 },
          html2canvas:{ scale:2, useCORS:true },
          jsPDF:{ unit:'in', format:'a4', orientation:'portrait' }
        }, options||{});
        window.html2pdf().from(cleanClone(el)).set(opt).save();
      } catch(e){
        console.error('Export failed; fallback to print()', e);
        window.print();
      }
    });
  }

  window.CVExport = { exportPDF };
})();