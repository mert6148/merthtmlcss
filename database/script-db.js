// Merthtmlcss Database Arayüzü JS - Gelişmiş
(function() {
  // Tema değiştirme
  function toggleTheme(force) {
    if(force === 'dark') document.body.classList.add('dark-theme');
    else if(force === 'light') document.body.classList.remove('dark-theme');
    else document.body.classList.toggle('dark-theme');
    localStorage.setItem('db-theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
  }
  // Ripple efekti
  function addRipple(e) {
    const btn = e.currentTarget;
    const circle = document.createElement('span');
    circle.className = 'ripple';
    const rect = btn.getBoundingClientRect();
    circle.style.left = (e.clientX - rect.left) + 'px';
    circle.style.top = (e.clientY - rect.top) + 'px';
    btn.appendChild(circle);
    setTimeout(() => circle.remove(), 500);
  }
  // Sayfa yüklendiğinde tema uygula
  document.addEventListener('DOMContentLoaded', function() {
    // Tema butonu ekle
    let btn = document.createElement('button');
    btn.className = 'theme-toggle';
    btn.title = 'Tema Değiştir';
    btn.innerHTML = '🌙';
    btn.onclick = function() {
      toggleTheme();
      btn.innerHTML = document.body.classList.contains('dark-theme') ? '☀️' : '🌙';
    };
    document.body.appendChild(btn);
    // Sistem temasını algıla
    if(!localStorage.getItem('db-theme')) {
      if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        toggleTheme('dark');
        btn.innerHTML = '☀️';
      }
    }
    // Kayıtlı tema uygula
    if(localStorage.getItem('db-theme') === 'dark') {
      document.body.classList.add('dark-theme');
      btn.innerHTML = '☀️';
    }
    // Bildirimleri kapama
    document.querySelectorAll('.success,.error,.info,.warning').forEach(function(el) {
      el.style.position = 'relative';
      let closeBtn = document.createElement('span');
      closeBtn.innerHTML = '&times;';
      closeBtn.style.cssText = 'position:absolute;top:8px;right:14px;cursor:pointer;font-size:1.2em;opacity:0.7;';
      closeBtn.onclick = function() {
        el.classList.add('close-anim');
        setTimeout(()=>{ el.style.display = 'none'; }, 500);
      };
      el.appendChild(closeBtn);
    });
    // Fade-in animasyonu
    document.querySelectorAll('.step,.test-result,.info,.success,.error,.warning,.download').forEach(function(el) {
      el.classList.add('fade-in');
    });
    // Ripple efekti tüm butonlara
    document.querySelectorAll('button, .btn').forEach(function(b) {
      b.addEventListener('click', function(e) {
        addRipple(e);
      });
    });
  });
})(); 