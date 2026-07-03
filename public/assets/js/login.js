// Mostrar/ocultar contraseña y validación básica
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('toggle-password');
  const pass = document.getElementById('password');
  if (toggle && pass) {
    toggle.addEventListener('click', function () {
      pass.type = pass.type === 'password' ? 'text' : 'password';
      toggle.textContent = pass.type === 'password' ? 'Mostrar' : 'Ocultar';
    });
  }

  const form = document.getElementById('login-form');
  if (!form) return;
  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const fd = new FormData(form);
    const payload = { username: fd.get('username'), password: fd.get('password') };

    try {
      const res = await fetch('/api/auth/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.ok && json.token) {
        // Guardar token en sessionStorage para el panel
        sessionStorage.setItem('token', json.token);
        window.location.href = '/dashboard.html';
      } else {
        alert('Usuario o contraseña incorrectos');
      }
    } catch (err) {
      alert('Error en el login');
      console.error(err);
    }
  });
});
