document.addEventListener("DOMContentLoaded", () => {
  if (typeof lucide !== "undefined") {
      lucide.createIcons();
      console.log("Lucide caricato e inizializzato");
  } else {
      console.error("Lucide non è stato caricato");
  }
});