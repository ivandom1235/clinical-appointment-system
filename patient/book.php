<?php
$pageCss = '/assets/css/pages/patient_book.css';
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
require_role('patient');
require_once __DIR__ . '/../partials/header.php';
?>
<!-- Make PHP $base available to JS -->
<script>
  window.APP_BASE = "<?= $base ?>";
</script>

<section class="card">
  <h2>Book Appointment</h2>

  <form id="search-form" class="book-form" onsubmit="return false;">
    <label>Doctor:
      <select name="doctor_id" id="doctor" required>
        <option value="">Select doctor</option>
        <?php
          $q = $pdo->query("SELECT d.id, u.name, d.specialty
                            FROM doctors d JOIN users u ON u.id=d.user_id ORDER BY u.name");
          foreach ($q as $row) {
            $id = (int)$row['id'];
            $name = htmlspecialchars($row['name']);
            $spec = htmlspecialchars($row['specialty']);
            echo "<option value='{$id}'>Dr. {$name} ({$spec})</option>";
          }
        ?>
      </select>
    </label>

    <label>Date:
      <input type="date" id="date" name="date" required
             min="<?= date('Y-m-d') ?>">
    </label>

    <button type="button" id="load-slots">Find Slots</button>
  </form>

  <div id="slots" class="slot-grid" style="margin-top:20px;"></div>
</section>

<script>
  const $ = (id) => document.getElementById(id);

  document.addEventListener('DOMContentLoaded', () => {
    const btn = $('load-slots');
    const doctorSel = $('doctor');
    const dateInp = $('date');
    const slotsDiv = $('slots');

    btn.addEventListener('click', async () => {
      const doctor = doctorSel.value;
      const date = dateInp.value;

      if (!doctor || !date) {
        slotsDiv.innerHTML = "<p style='color:#d32;'>Select doctor and date.</p>";
        return;
      }
      slotsDiv.innerHTML = "<p>Loading slots…</p>";

      try {
        const url = `${window.APP_BASE}/backend/slots.php?doctor_id=${encodeURIComponent(doctor)}&date=${encodeURIComponent(date)}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const text = await res.text();
        let slots;
        try { slots = JSON.parse(text); }
        catch {
          slotsDiv.innerHTML = `<pre style="color:#d32;white-space:pre-wrap;">Server returned non-JSON:\n${text}</pre>`;
          return;
        }

        if (!Array.isArray(slots) || slots.length === 0) {
          slotsDiv.innerHTML = "<p><em>No free slots that day.</em></p>";
          return;
        }

        // render slot buttons (POST to book_handler.php)
        slotsDiv.innerHTML = slots.map(t => `
          <form method="POST" action="${window.APP_BASE}/backend/book_handler.php" style="display:inline-block;margin:5px;">
            <input type="hidden" name="doctor_id" value="${doctor}">
            <input type="hidden" name="date" value="${date}">
            <input type="hidden" name="time" value="${t}:00">
            <button type="submit">${t}</button>
          </form>
        `).join('');
      } catch (err) {
        slotsDiv.innerHTML = `<p style="color:#d32;">Request failed: ${err}</p>`;
      }
    });
  });
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
