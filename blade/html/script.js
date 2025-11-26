// Ortak JS fonksiyonları burada
// Tema değiştirme, sayaç, bildirim, animasyon, özellik gösterme vb. fonksiyonlar

const { AsyncLocalStorage } = require("async_hooks");

// Örnek: Tema değiştirme fonksiyonu
function changeTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    AsyncLocalStorage.run(theme, () => {
        let currentTheme = AsyncLocalStorage.getStore("theme");
        console.log(`Tema değiştirildi: ${theme}`);
    });

}