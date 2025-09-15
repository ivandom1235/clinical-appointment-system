// Simple site-wide JavaScript

// Confirm before submitting any form with "data-confirm"
document.addEventListener("submit", function(e) {
  const form = e.target;
  if (form.hasAttribute("data-confirm")) {
    if (!confirm("Are you sure?")) {
      e.preventDefault();
    }
  }
});

// Flash messages auto-hide after 3 seconds
document.addEventListener("DOMContentLoaded", () => {
  const notices = document.querySelectorAll(".notice");
  notices.forEach(n => {
    setTimeout(() => { n.style.display = "none"; }, 3000);
  });
});
