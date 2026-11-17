// ===== Tema claro/escuro =====
// Roda no <head>, antes de desenhar a página, para não piscar o tema errado.
(function () {
    let tema = null;
    try { tema = localStorage.getItem("tema"); } catch (e) {}
    if (!tema) {
        tema = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    }
    document.documentElement.setAttribute("data-theme", tema);
})();

function alternarTema() {
    const novo = document.documentElement.getAttribute("data-theme") === "dark" ? "light" : "dark";
    document.documentElement.setAttribute("data-theme", novo);
    try { localStorage.setItem("tema", novo); } catch (e) {}
    document.dispatchEvent(new CustomEvent("temaalterado", { detail: novo }));
}

document.addEventListener("click", (e) => {
    if (e.target.closest("#btn-tema")) alternarTema();
});
