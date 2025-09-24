<?php
// user/ai_chat.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'job_seeker') {
    header('Location: ../login.php');
    exit;
}
include_once('../includes/header_jobseeker.php');
?>

  <style>
    :root { --bg:#f5f8fb; --panel:#ffffff; --muted:#6b7280; --text:#0b1a2b; --accent:#0066ff; --user:#e6f0ff; }
    body { background: var(--bg); color:var(--text); }
    .chat-wrapper { max-width: 1000px; margin: 30px auto; padding: 0 16px; }
    .chat-card { background: var(--panel); border-radius: 12px; box-shadow: 0 6px 20px rgba(16,24,40,.08); overflow: hidden; border:1px solid #eef2ff; }
    .chat-header { display:flex; align-items:center; gap:12px; padding:14px 18px; border-bottom:1px solid #f1f5f9; }
    .chat-header i { color: var(--accent); font-size: 20px; }
    .chat-header h2 { margin:0; color: var(--text); font-size: 18px; }
    .chat-body { height: 60vh; overflow-y: auto; padding: 18px; background: linear-gradient(180deg,#fbfdff,#f5f8fb); }
    .msg { display:flex; gap:12px; margin: 12px 0; align-items:flex-start; }
    .msg.user { flex-direction: row-reverse; }
    .bubble { max-width: 75%; padding: 12px 14px; border-radius: 12px; color: var(--text); line-height: 1.5; white-space: pre-wrap; box-shadow: 0 2px 6px rgba(15,23,42,.04); }
    .msg.user .bubble { background: var(--user); border:1px solid #d7e7ff; }
    .msg.assistant .bubble { background: #fff; border:1px solid #eef2ff; }
    .avatar { width: 38px; height:38px; border-radius: 50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#375aa7; flex: 0 0 38px; }
    .composer { display:flex; gap:10px; padding: 12px; border-top:1px solid #f1f5f9; background: transparent; }
    .composer textarea { flex:1; resize:none; height:56px; padding:12px; border-radius: 10px; border:1px solid #e6eefc; background:#fff; color:var(--text); outline:none; }
    .composer button { background: var(--accent); color:white; border:none; border-radius: 10px; padding: 0 18px; font-weight:600; cursor:pointer; }
    .composer button:disabled { opacity:.6; cursor:not-allowed; }
    .hint { color: var(--muted); font-size: 13px; padding: 10px 18px 0; }
    .spinner { width:18px; height:18px; border:2px solid rgba(0,0,0,.06); border-top-color:#fff; border-radius:50%; animation:spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
  .examples { display:flex; gap:10px; flex-wrap:wrap; margin-top: 8px; padding: 0 18px 14px; }
    .examples button { background:#f8fafc; color:#0b2a66; border:1px solid #e6eefc; padding:8px 12px; border-radius:8px; cursor:pointer; }
    .toolbar { display:flex; gap:10px; align-items:center; padding:10px 18px; }
    .toolbar label { font-size:13px; color:var(--muted); display:flex; gap:6px; align-items:center; }
    .raw-output { font-family: monospace; font-size:12px; color:#243b53; background:#f1f7ff; padding:8px; border-radius:8px; margin:8px 18px; display:none; white-space:pre-wrap; }
  /* Model picker styles */
  .model-picker { display:flex; flex-direction:column; gap:6px; min-width:220px; }
  .model-label { font-size:12px; color:var(--muted); }
  .model-select { position:relative; }
  .model-current { display:flex; align-items:center; gap:8px; width:100%; background:#fff; border:1px solid #e6eefc; padding:8px 10px; border-radius:8px; cursor:pointer; font-weight:600; color:var(--text); }
  .model-current .badge { background:#eef6ff; color:#0b2a66; padding:2px 6px; border-radius:999px; font-size:11px; margin-left:8px; font-weight:700; }
  .model-list { display:none; position:absolute; top:calc(100% + 8px); left:0; right:0; background:#fff; border:1px solid #e6eefc; border-radius:10px; box-shadow:0 8px 24px rgba(15,23,42,.06); list-style:none; margin:0; padding:8px; z-index:50; }
  .model-list li { padding:10px; border-radius:8px; cursor:pointer; }
  .model-list li:hover { background:#f8fafc; }
  .model-list li.selected { background:#eef6ff; }
  .model-list .meta { font-size:12px; color:var(--muted); margin-top:4px; }
  .model-info { font-size:12px; color:var(--muted); }
  </style>
  <script>
    const SYSTEM_PROMPT = `You are NextWorkX AI Job Assistant. Be concise, practical, and friendly.
You can help with:
- picking or improving skills and learning resources
- writing CV/resume bullets from experience
- interview prep questions and answers
- tailoring to a job description
- career paths and salary ranges
When asked for code or examples, provide short, clear snippets. If asked about this platform, it's called NextWorkX.`;
  </script>

  <main class="chat-wrapper">
    <div class="chat-card">
      <div class="chat-header">
        <i class="fa-solid fa-robot"></i>
        <h2>AI Job Assistant</h2>
      </div>
      <div class="hint">Ask about skills to learn, interview prep, resume bullets, or tailoring to a specific job.</div>
      <!-- <div class="toolbar">
        <label><input type="checkbox" id="debugToggle"> Enable debug (show raw response when assistant reply is empty)</label>
        <div class="model-picker" style="margin-left:12px;">
          <div class="model-label">Model</div>
          <div class="model-select" id="modelSelectEl">
            <button class="model-current" id="modelCurrent">Gemini 2.0 Flash <span class="badge">free</span> <i class="fa fa-caret-down"></i></button>
            <ul class="model-list" id="modelList" aria-hidden="true">
              <li data-value="google/gemini-2.0-flash-exp:free" class="selected"><strong>Gemini 2.0 Flash</strong><div class="meta">Fast experimental Gemini (vision & text)</div></li>
              <li data-value="deepseek/deepseek-r1:free"><strong>DeepSeek R1</strong><div class="meta">Lightweight recruiter-focused model</div></li>
            </ul>
          </div>
          <div class="model-info" id="modelInfo">Fast experimental Gemini (vision & text)</div>
        </div>
      </div> -->
      <div class="examples">
        <button data-eg="List 5 in-demand skills for a junior web developer and beginner resources.">Skills for junior web developer</button>
        <button data-eg="Mock interview: ask me 3 backend questions one by one and wait for my answer.">Mock interview</button>
        <button data-eg="Rewrite this resume bullet to be more impactful: Built an API in PHP.">Improve resume bullet</button>
        <button data-eg="How can I tailor my resume to this job description? (paste JD)">Tailor resume to JD</button>
      </div>
  <div id="chat" class="chat-body"></div>
  <div id="raw" class="raw-output" aria-hidden="true"></div>
      <div class="composer">
        <textarea id="input" placeholder="Type your message..."></textarea>
        <button id="sendBtn"><span class="label">Send</span></button>
      </div>
    </div>
  </main>

  <script>
  const chatEl = document.getElementById('chat');
  const inputEl = document.getElementById('input');
  const sendBtn = document.getElementById('sendBtn');
  const debugToggle = document.getElementById('debugToggle');
  const rawEl = document.getElementById('raw');
  // model picker elements
  // modelSelect will be queried at send time (hidden input created dynamically)
  const modelSelectEl = document.getElementById('modelSelectEl');
  const modelCurrent = document.getElementById('modelCurrent');
  const modelList = document.getElementById('modelList');
  const modelInfo = document.getElementById('modelInfo');

  // hidden input to maintain backward compatibility
  let hiddenModelInput = document.getElementById('modelSelect');
  if (!hiddenModelInput) {
    hiddenModelInput = document.createElement('input');
    hiddenModelInput.type = 'hidden';
    hiddenModelInput.id = 'modelSelect';
    hiddenModelInput.value = 'google/gemini-2.0-flash-exp:free';
    document.querySelector('.toolbar').appendChild(hiddenModelInput);
  }

  // Toggle list
  modelCurrent && modelCurrent.addEventListener('click', () => {
    const open = modelList.style.display === 'block';
    modelList.style.display = open ? 'none' : 'block';
    modelList.setAttribute('aria-hidden', open ? 'true' : 'false');
  });

  // Click outside to close
  document.addEventListener('click', (e) => {
    if (!modelSelectEl.contains(e.target)) {
      modelList.style.display = 'none';
      modelList.setAttribute('aria-hidden', 'true');
    }
  });

  // Select model
  modelList.querySelectorAll('li').forEach(li => {
    li.addEventListener('click', () => {
      modelList.querySelectorAll('li').forEach(x => x.classList.remove('selected'));
      li.classList.add('selected');
      const value = li.getAttribute('data-value');
      const label = li.querySelector('strong')?.innerText || value;
      const meta = li.querySelector('.meta')?.innerText || '';
      modelCurrent.innerHTML = label + ' <span class="badge">free</span> <i class="fa fa-caret-down"></i>';
      modelInfo.textContent = meta;
      hiddenModelInput.value = value;
      modelList.style.display = 'none';
    });
  });

    const history = [ { role: 'system', content: SYSTEM_PROMPT } ];

    function addMessage(role, content) {
      const wrapper = document.createElement('div');
      wrapper.className = 'msg ' + (role === 'user' ? 'user' : 'assistant');
      const avatar = document.createElement('div');
      avatar.className = 'avatar';
      avatar.innerHTML = role === 'user' ? '<i class="fa-regular fa-user"></i>' : '<i class="fa-solid fa-robot"></i>';
      const bubble = document.createElement('div');
      bubble.className = 'bubble';
      bubble.textContent = content;
      wrapper.appendChild(avatar);
      wrapper.appendChild(bubble);
      chatEl.appendChild(wrapper);
      chatEl.scrollTop = chatEl.scrollHeight;
    }

    function setLoading(loading) {
      if (loading) {
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<div class="spinner"></div>';
      } else {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<span class="label">Send</span>';
      }
    }

    async function sendMessage(text) {
      if (!text.trim()) return;
      addMessage('user', text);
      history.push({ role: 'user', content: text });
      inputEl.value = '';
      setLoading(true);
      try {
  const modelEl = document.getElementById('modelSelect');
  const model = (modelEl && modelEl.value) ? modelEl.value : 'google/gemini-2.0-flash-exp:free';
  const isGemini = /gemini/i.test(model);
  if (isGemini) {
    // Call native Gemini proxy for reliability; send only the latest message (plus system hint)
    const composed = `${SYSTEM_PROMPT}\n\nUser: ${text}`;
    const res = await fetch('../ajax/gemini_chat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'message=' + encodeURIComponent(composed)
    });
    if (!res.ok) {
      const txt = await res.text();
      throw new Error('Server returned ' + res.status + ': ' + txt);
    }
    const data = await res.json();
    const reply = data.reply || '';
    rawEl.style.display = 'none';
    addMessage('assistant', reply || 'No response.');
    history.push({ role: 'assistant', content: reply });
  } else {
    // Fallback to OpenRouter for non-Gemini models
    const payload = { model, messages: history };
    if (debugToggle && debugToggle.checked) payload.debug = true;
    const res = await fetch('../ajax/openrouter_chat.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    if (!res.ok) {
      const txt = await res.text();
      throw new Error('Server returned ' + res.status + ': ' + txt);
    }
    const data = await res.json();
    const reply = data.reply || '';
    if (!reply) {
      // Show raw JSON if available and debug enabled
      if (data.raw && payload.debug) {
        rawEl.style.display = 'block';
        rawEl.textContent = JSON.stringify(data.raw, null, 2);
      } else {
        rawEl.style.display = 'none';
      }
      addMessage('assistant', 'Sorry, I did not get a clear reply. Toggle debug to view raw response.');
      history.push({ role: 'assistant', content: '' });
    } else {
      rawEl.style.display = 'none';
      addMessage('assistant', reply);
      history.push({ role: 'assistant', content: reply });
    }
  }
      } catch (err) {
        console.error(err);
        addMessage('assistant', 'Error: ' + (err.message || 'Unknown error. Please try again.'));
      } finally {
        setLoading(false);
      }
    }

    sendBtn.addEventListener('click', () => sendMessage(inputEl.value));
    inputEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage(inputEl.value);
      }
    });

    document.querySelectorAll('.examples button').forEach(btn => {
      btn.addEventListener('click', () => {
        inputEl.value = btn.getAttribute('data-eg');
        inputEl.focus();
      });
    });

    // Initial greeting
    addMessage('assistant', 'Hi! I\'m your AI Job Assistant. Ask me about skills to learn, interview prep, or improving your resume.');
  </script>
<?php include_once('../includes/footer_jobseeker.php'); ?>
