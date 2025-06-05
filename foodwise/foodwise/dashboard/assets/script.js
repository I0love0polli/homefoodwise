document.addEventListener("DOMContentLoaded", () => {
  if (typeof lucide !== "undefined") {
    lucide.createIcons();
    console.log("Lucide caricato e inizializzato");
  } else {
    console.error("Lucide non è stato caricato");
  }

  const sidebar = document.querySelector(".sidebar");
  const content = document.querySelector(".content");
  const userProfile = document.querySelector(".user-profile");
  const sidebarHeader = document.querySelector(".sidebar-header");

  if (sidebar.classList.contains("collapsed")) {
    userProfile.classList.add("collapsed");
    sidebarHeader.classList.add("collapsed");
  }

  function toggleSidebar() {
    const isCollapsed = sidebar.classList.contains("collapsed");
    sidebar.classList.toggle("collapsed");
    content.classList.toggle("collapsed");
    userProfile.classList.toggle("collapsed");
    sidebarHeader.classList.toggle("collapsed");

    fetch("../dashboard/index.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "collapse_sidebar=" + !isCollapsed,
    })
      .then((response) => {
        console.log("Stato sidebar salvato:", response);
      })
      .catch((error) => {
        console.error("Errore nel salvataggio dello stato:", error);
      });

    lucide.createIcons();
  }

  // Rende la funzione disponibile globalmente
  window.toggleSidebar = toggleSidebar;

  sidebarHeader.addEventListener("click", toggleSidebar);

  const navLinks = document.querySelectorAll(".nav-link");
  navLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      if (sidebar.classList.contains("collapsed")) {
        e.stopPropagation();
      }
    });
  });
});
