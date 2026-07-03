// Consumir estadísticas y mostrarlas en el panel
document.addEventListener('DOMContentLoaded', async function () {
  const container = document.getElementById('stats');
  if (!container) return;

  try {
    const res = await fetch('/api/encuestas/estadisticas.php');
    const json = await res.json();
    if (json.total !== undefined) {
      container.innerHTML = `<p>Total encuestas: <strong>${json.total}</strong></p>`;
      // Mostrar últimas respuestas de ejemplo
      const list = document.createElement('ul');
      (json.latest || []).forEach(row => {
        const li = document.createElement('li');
        li.textContent = `#${row.id} - ${row.created_at} - ${JSON.stringify(row.data)}`;
        list.appendChild(li);
      });
      container.appendChild(list);
    } else {
      container.textContent = 'No hay estadísticas disponibles';
    }
  } catch (err) {
    container.textContent = 'Error al cargar estadísticas';
    console.error(err);
  }
});
