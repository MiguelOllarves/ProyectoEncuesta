// Lógica para enviar la encuesta via fetch
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('encuesta-form');
  if (!form) return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    const payload = {};
    formData.forEach((v, k) => payload[k] = v);

    try {
      const res = await fetch('/api/encuestas/guardar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const json = await res.json();
      if (json.ok) {
        alert('Encuesta registrada. Gracias.');
        form.reset();
      } else {
        alert('Error: ' + (json.error || 'Respuesta no válida'));
      }
    } catch (err) {
      alert('Error al enviar la encuesta');
      console.error(err);
    }
  });
});
