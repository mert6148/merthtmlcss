/* js-compiler.js
   In-browser JS editor + Babel compiler + sandboxed runner
   - Adds a modal editor opened by `.compiler-btn`
   - Uses Babel standalone to transpile ESNext -> ES5
   - Executes code inside a sandboxed iframe and captures console output
*/
(function () {
  'use strict';

  // Create modal HTML
  function createCompilerModal() {
    if (document.getElementById('js-compiler-modal')) return;

    const modal = document.createElement('div');
    modal.id = 'js-compiler-modal';
    modal.className = 'modal-overlay';
    modal.style.zIndex = 12000;

    modal.innerHTML = `
      <div class="modal-container" style="max-width:900px; width:95%;">
        <div class="modal-header">
          <h3>JS Compiler & Runner</h3>
          <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" style="display:flex; gap:1rem; flex-direction:column;">
          <div id="js-compiler-monaco" style="width:100%;height:360px;border:1px solid #ddd;border-radius:8px;overflow:hidden;background:#1e1e1e"></div>

          <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
            <button id="js-compiler-compile" class="btn btn-primary">Derle (Babel)</button>
            <button id="js-compiler-run" class="btn btn-success">Çalıştır</button>
            <button id="js-compiler-clear" class="btn btn-secondary">Çıktıyı Temizle</button>
            <button id="js-run-example" class="btn btn-primary">Örnek Çalıştır</button>
            <select id="js-snippet-select" style="min-width:180px;padding:.4rem;border-radius:6px;border:1px solid #ddd;background:#fff;color:#222">
              <option value="">-- Snippet Yükle --</option>
            </select>
            <button id="js-save-snippet" class="btn btn-secondary">Snippet Kaydet</button>
            <button id="js-delete-snippet" class="btn btn-secondary">Sil</button>
            <label style="margin-left:auto;font-size:0.9rem;color:#666;">Preset: <strong>env</strong></label>
          </div>

          <div style="border:1px solid #eee;padding:12px;border-radius:8px;background:#fafafa;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
              <strong>Çıktı (Console)</strong>
              <small id="js-compiler-status" style="color:#666">Hazır</small>
            </div>
            <pre id="js-compiler-output" style="white-space:pre-wrap;max-height:240px;overflow:auto;margin:0;font-family:monospace;font-size:13px"></pre>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(modal);

    // Initialize Monaco Editor (if loader is available)
    var monacoEditor = null;
    function initMonaco() {
      try {
        if (window.require && typeof window.require === 'function') {
          // configure loader base
          window.require.config({ paths: { vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.43.0/min/vs' } });
          window.require(['vs/editor/editor.main'], function () {
            monacoEditor = monaco.editor.create(document.getElementById('js-compiler-monaco'), {
              value: "// JS kodunuzu buraya yazın\nconsole.log('Merhaba from Monaco');",
              language: 'javascript',
              automaticLayout: true,
              theme: 'vs-dark',
              minimap: { enabled: false }
            });
            // Keybindings
            monacoEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.Enter, function () { runCode(); });
            monacoEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KEY_S, function () { compileCode(); });
            window.jsCompilerEditor = monacoEditor;
            // populate snippets once editor is ready
            ensureExampleSnippet();
            populateSnippets();
          });
        } else {
          appendStatus('Monaco loader not available in this environment');
          ensureExampleSnippet();
          populateSnippets();
        }
      } catch (err) {
        console.warn('Monaco init failed', err);
        ensureExampleSnippet();
        populateSnippets();
      }
    }
    initMonaco();

    // Event listeners
    modal.querySelector('.modal-close').addEventListener('click', () => {
      try { if (window.jsCompilerEditor && typeof window.jsCompilerEditor.dispose === 'function') window.jsCompilerEditor.dispose(); } catch (e) { }
      modal.remove();
    });
    document.getElementById('js-compiler-compile').addEventListener('click', compileCode);
    document.getElementById('js-compiler-run').addEventListener('click', runCode);
    document.getElementById('js-compiler-clear').addEventListener('click', () => {
      const out = document.getElementById('js-compiler-output');
      out.textContent = '';
    });

    // Message listener from iframe
    window.addEventListener('message', function (ev) {
      if (!ev.data || ev.data.source !== 'js-compiler-iframe') return;
      const out = document.getElementById('js-compiler-output');
      const { level, args } = ev.data;
      const text = args.map(a => (typeof a === 'object' ? JSON.stringify(a) : String(a))).join(' ');
      const time = new Date().toLocaleTimeString();
      out.textContent += `[${time}] ${level.toUpperCase()}: ${text}\n`;
      out.scrollTop = out.scrollHeight;
    });

    // Snippet control events
    document.getElementById('js-save-snippet').addEventListener('click', saveSnippet);
    document.getElementById('js-snippet-select').addEventListener('change', function (e) {
      const key = e.target.value;
      if (!key) return;
      loadSnippet(key);
    });
    document.getElementById('js-delete-snippet').addEventListener('click', function () {
      const sel = document.getElementById('js-snippet-select');
      const key = sel.value;
      if (!key) { appendStatus('Silinecek snippet seçin'); return; }
      deleteSnippet(key);
    });
    document.getElementById('js-run-example').addEventListener('click', runExample);
  }

  // Snippet storage helpers (localStorage)
  function getSnippets() {
    try { return JSON.parse(localStorage.getItem('js_compiler_snippets') || '{}'); } catch (e) { return {}; }
  }

  function saveSnippets(obj) {
    localStorage.setItem('js_compiler_snippets', JSON.stringify(obj));
  }

  function populateSnippets() {
    const sel = document.getElementById('js-snippet-select');
    if (!sel) return;
    // clear
    sel.innerHTML = '<option value="">-- Snippet Yükle --</option>';
    const list = getSnippets();
    Object.keys(list).forEach(k => {
      const opt = document.createElement('option'); opt.value = k; opt.textContent = k; sel.appendChild(opt);
    });
  }

  // Ensure there is at least one example snippet for first-time users
  function ensureExampleSnippet() {
    const snippets = getSnippets();
    if (Object.keys(snippets).length === 0) {
      const sample = `// Örnek: Basit veritabanı metriği simülasyonu\n(function(){\n  console.log('Örnek çalıştırıldı - veritabanı metriği:');\n  const metrics = { cpu: Math.floor(Math.random()*100), memory: Math.floor(Math.random()*100), disk: Math.floor(Math.random()*100) };\n  console.table(metrics);\n  console.log('Toplam tablolar:', 12);\n})();`;
      snippets['Örnek-Metri̇k'] = { code: sample, updated: Date.now() };
      saveSnippets(snippets);
    }
  }

  // Load example snippet and run it (compile + run)
  function runExample() {
    const snippets = getSnippets();
    const keys = Object.keys(snippets);
    if (!keys.length) { appendStatus('Örnek snippet bulunamadı'); return; }
    const key = keys[0];
    loadSnippet(key);
    // small delay to allow editor setValue to take effect
    setTimeout(() => {
      const compiled = compileCode();
      if (compiled) runCode();
    }, 120);
  }

  function saveSnippet() {
    const name = prompt('Snippet adı girin:');
    if (!name) { appendStatus('Snippet kaydı iptal edildi'); return; }
    const editor = window.jsCompilerEditor;
    if (!editor) { appendStatus('Editor hazır değil'); return; }
    const code = (typeof editor.getValue === 'function') ? editor.getValue() : '';
    const snippets = getSnippets();
    snippets[name] = { code: code, updated: Date.now() };
    saveSnippets(snippets);
    populateSnippets();
    appendStatus('Snippet kaydedildi: ' + name);
  }

  function loadSnippet(name) {
    const snippets = getSnippets();
    if (!snippets[name]) { appendStatus('Snippet bulunamadı: ' + name); return; }
    const editor = window.jsCompilerEditor;
    if (!editor) { appendStatus('Editor hazır değil'); return; }
    if (typeof editor.setValue === 'function') editor.setValue(snippets[name].code);
    appendStatus('Snippet yüklendi: ' + name);
  }

  function deleteSnippet(name) {
    const snippets = getSnippets();
    if (!snippets[name]) { appendStatus('Snippet bulunamadı: ' + name); return; }
    if (!confirm('"' + name + '" snippetini silmek istediğinize emin misiniz?')) return;
    delete snippets[name];
    saveSnippets(snippets);
    populateSnippets();
    appendStatus('Snippet silindi: ' + name);
  }

  // Compile code using Babel
  function compileCode() {
    const editor = window.jsCompilerEditor;
    const status = document.getElementById('js-compiler-status');
    const modal = document.getElementById('js-compiler-modal');
    if (!editor) { appendStatus('Editor hazır değil'); return null; }
    const code = (typeof editor.getValue === 'function') ? editor.getValue() : '';

    try {
      status.textContent = 'Derleniyor...';
      const result = Babel.transform(code, { presets: ['env'] });
      const compiled = result.code;
      // Store compiled on the modal dataset for run
      if (modal) modal.dataset.compiled = compiled;
      status.textContent = 'Derlendi (hazır çalıştırmaya)';
      appendStatus('Derleme başarılı');
      return compiled;
    } catch (err) {
      status.textContent = 'Derleme hatası';
      appendStatus('Derleme hatası: ' + err.message);
      return null;
    }
  }

  // Run compiled code in a sandboxed iframe and capture console
  function runCode() {
    const editor = window.jsCompilerEditor;
    const modal = document.getElementById('js-compiler-modal');
    const status = document.getElementById('js-compiler-status');
    if (!editor) { appendStatus('Editor hazır değil'); return; }

    // If not compiled, compile first
    let compiled = modal ? modal.dataset.compiled : null;
    if (!compiled) compiled = compileCode();
    if (!compiled) return;

    status.textContent = 'Çalıştırılıyor...';

    // Remove previous iframe if exists
    const old = document.getElementById('js-compiler-iframe');
    if (old) old.remove();

    const iframe = document.createElement('iframe');
    iframe.id = 'js-compiler-iframe';
    // sandbox allow-scripts is enough; disallow same-origin to prevent access
    iframe.setAttribute('sandbox', 'allow-scripts');
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    iframe.style.position = 'absolute';
    iframe.style.left = '-9999px';

    document.body.appendChild(iframe);

    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
    // Build the iframe HTML which forwards console messages to parent
    const wrappedScript = `
      (function(){
        function send(level, args){
          try{ parent.postMessage({ source: 'js-compiler-iframe', level: level, args: args }, '*'); }catch(e){}
        }
        // override console
        var __console = {};
        ['log','info','warn','error','debug'].forEach(function(l){
          __console[l] = console[l];
          console[l] = function(){ send(l, Array.prototype.slice.call(arguments)); };
        });
        // capture uncaught errors
        window.addEventListener('error', function(e){ send('error', [e.message + ' at ' + e.filename + ':' + e.lineno + ':' + e.colno]); });

        try {
          // user code start
          ${compiled}
          // user code end
        } catch (err) {
          send('error', [err.message]);
        }
      })();
    `;

    const html = `<!doctype html><html><head><meta charset="utf-8"></head><body><script>${wrappedScript}</script></body></html>`;
    iframeDoc.open();
    iframeDoc.write(html);
    iframeDoc.close();

    status.textContent = 'Çalıştırıldı';
    appendStatus('Çalıştırma başlatıldı');
  }

  function appendStatus(msg) {
    const out = document.getElementById('js-compiler-output');
    if (!out) return;
    const time = new Date().toLocaleTimeString();
    out.textContent += `[${time}] ${msg}\n`;
    out.scrollTop = out.scrollHeight;
  }

  // Hook button openers
  function initCompilerButton() {
    document.addEventListener('click', function (e) {
      if (e.target.closest('.compiler-btn')) {
        createCompilerModal();
      }
    });
  }

  // Auto initialize when DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCompilerButton);
  } else {
    initCompilerButton();
  }
})();
