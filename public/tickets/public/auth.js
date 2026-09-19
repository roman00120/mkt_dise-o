document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        btn.classList.add('active');
        const tab = btn.dataset.tab + '-tab';
        document.getElementById(tab).classList.add('active');

        // Show/hide tutorial video based on active tab
        const tutorialVideo = document.getElementById('tutorial-video');
        if (tutorialVideo) {
            if (btn.dataset.tab === 'collaborator') {
                tutorialVideo.style.display = 'block';
            } else {
                tutorialVideo.style.display = 'none';
            }
        }

        const errorMsg = document.getElementById('error-message');
        if (errorMsg) {
            errorMsg.textContent = '';
        }
    });
});

document.getElementById('collaborator-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const department = document.getElementById('department').value;

    try {
        const response = await fetch('/tickets/api/auth.php?action=login-collaborator', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ department })
        });

        const data = await response.json();
        if (data.success) {
            window.location.href = '/tickets/index.php?page=dashboard';
        } else {
            showError(data.error || 'Error al iniciar sesión');
        }
    } catch (error) {
        showError('Error de conexión');
    }
});

document.getElementById('admin-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('/tickets/api/auth.php?action=login-admin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();
        if (data.success) {
            window.location.href = '/tickets/index.php?page=dashboard';
        } else {
            showError(data.error || 'Credenciales incorrectas');
        }
    } catch (error) {
        showError('Error de conexión');
    }
});

function showError(msg) {
    document.getElementById('error-message').textContent = msg;
}


