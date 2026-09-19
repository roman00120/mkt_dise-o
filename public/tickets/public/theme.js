document.addEventListener("DOMContentLoaded", function () {
    // Aplicar tema guardado inmediatamente
    const currentTheme = localStorage.getItem("darkMode");
    if (currentTheme === "on") {
        document.body.classList.add("dark");
    }

    // Si no hay toggle en la p�gina, lo inyectamos (opcional, dependiendo de si queremos que aparezca en todas partes)
    injectToggle();

    setupToggle();
});

function injectToggle() {
    if (document.getElementById("darkToggle")) return;

    const navRight = document.querySelector('.nav-right');
    if (navRight) {
        const toggleDiv = document.createElement('div');
        toggleDiv.className = 'dark-mode-toggle nav-toggle';
        toggleDiv.innerHTML = `
            <span>Modo Oscuro</span>
            <label class="switch">
                <input type="checkbox" id="darkToggle">
                <span class="slider"></span>
            </label>
        `;
        navRight.insertBefore(toggleDiv, navRight.firstChild);
    }
}

function setupToggle() {
    const toggle = document.getElementById("darkToggle");
    if (!toggle) return;

    // Sincronizar estado del checkbox
    if (localStorage.getItem("darkMode") === "on") {
        toggle.checked = true;
    }

    toggle.addEventListener("change", function () {
        if (this.checked) {
            document.body.classList.add("dark");
            localStorage.setItem("darkMode", "on");
        } else {
            document.body.classList.remove("dark");
            localStorage.setItem("darkMode", "off");
        }
    });
}

