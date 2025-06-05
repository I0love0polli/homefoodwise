//Funzioni personale

function filterStaff(role) {
  const cards = document.querySelectorAll(".staff-card");
  const tabs = document.querySelectorAll(".filter-tab");

  if (tabs.length === 0) return;

  tabs.forEach((tab) => tab.classList.remove("active"));
  const activeTab = document.querySelector(
    `.filter-tab[onclick="filterStaff('${role}')"]`
  );
  if (activeTab) activeTab.classList.add("active");

  cards.forEach((card) => {
    card.style.display = "block";
    if (role === "all" || card.dataset.role === role) {
      card.dataset.visibleByFilter = "true";
    } else {
      card.dataset.visibleByFilter = "false";
      card.style.display = "none";
    }
  });

  searchStaff();
}

// Funzione per cercare lo staff
function searchStaff() {
  const input = document.getElementById("searchInput")?.value.toLowerCase();
  const cards = document.querySelectorAll(".staff-card");

  cards.forEach((card) => {
    const name = card.dataset.name?.toLowerCase();
    const role = card.dataset.role?.toLowerCase();
    const isVisibleByFilter = card.dataset.visibleByFilter === "true";

    if (isVisibleByFilter && (name?.includes(input) || role?.includes(input))) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
}

// Funzione per aprire il modale di modifica
function openEditStaffModal(no1, username, full_name, email, role, phone, hired) {
  const nameElement = document.getElementById("editStaffName");
  if (nameElement) nameElement.textContent = `${full_name}`;
  console.log(username);
  console.log(email);
  console.log(phone);
  console.log(hired);
  console.log(role);
  

  const usernameInput = document.getElementById("editStaffUsername");
  if (usernameInput) usernameInput.value = username;

  const emailInput = document.getElementById("editStaffEmail");
  if (emailInput) emailInput.value = email;

  const phoneInput = document.getElementById("editStaffPhone");
  if (phoneInput) phoneInput.value = phone;

  const hiredInput = document.getElementById("editStaffHired");
  if (hiredInput) hiredInput.value = hired;

  // Set the hidden input value
  const roleInput = document.getElementById("editStaffRole");
  if (roleInput) roleInput.value = role.toLowerCase();

  // Check the corresponding radio button
  const roleRadios = document.querySelectorAll(
    '.role-radio[name="editStaffRole"]'
  );
  roleRadios.forEach((radio) => {
    radio.checked = radio.value === role.toLowerCase();
  });

  const modalElement = document.getElementById("editStaffModal");
  if (modalElement) {
    const modalInstance = new bootstrap.Modal(modalElement);
    modalInstance.show();
  }
}

function selectRoleAdd(role) {
  console.log("Selected role for add:", role);
  document.getElementById("staffRole").value = role;
  console.log(
    "Updated staffRole value:",
    document.getElementById("staffRole").value
  );
}

// Funzione per selezionare il ruolo nel modale di aggiunta
function selectRole(role) {
  console.log("Selected role for edit:", role);
  document.getElementById("editStaffRole").value = role;
  console.log(
    "Updated editStaffRole value:",
    document.getElementById("editStaffRole").value
  );
}

// Funzione per aggiornare il ruolo
function updateStaffRole() {
  const id_username = document.getElementById("editStaffUsername").value;
  const newRole = document.getElementById("editStaffRole").value;

  if (!id_username || !newRole) {
    console.error("ID o ruolo non validi."); // Sostituisci alert con console.error
    return;
  }

  console.log("Sending ID:", id_username, "New Role:", newRole);

  fetch("../database/update_staff_role.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id_username=${encodeURIComponent(
      id_username
    )}&newRole=${encodeURIComponent(newRole)}`,
  })
    .then((response) => response.text())
    .then((text) => {
      console.log("Response text:", text);
      const data = JSON.parse(text);
      if (data.success) {
        location.reload(); // Ricarica la pagina in caso di successo
      } else {
        console.error("Errore durante l'aggiornamento:", data.message); // Logga l'errore
      }
    })
    .catch((error) => {
      console.error("Errore durante l'aggiornamento:", error); // Logga eventuali errori di fetch
    });
}

// Funzione per eliminare uno staff
function deleteStaff() {
  const username = document.getElementById("editStaffUsername")?.value;

  if (!username) {
    alert("Errore: username non valido.");
    return;
  }

  fetch("../database/delete_staff.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `staffUsername=${encodeURIComponent(username)}`,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(
          "Errore nella risposta del server: " + response.statusText
        );
      }
      return response.text(); // Leggi il testo per debug
    })
    .then((text) => {
      try {
        const data = JSON.parse(text);
        if (!data.success) {
          alert("Errore durante l'eliminazione: " + data.message);
          console.log("Dettagli errore:", data);
        } else {
          const modalElement = document.getElementById("editStaffModal");
          const modalInstance = bootstrap.Modal.getInstance(modalElement);
          modalInstance.hide();
          loadStaff(); // Ricarica la lista
        }
      } catch (e) {
        console.error("Errore di parsing JSON:", e);
        console.log("Testo ricevuto dal server:", text);
        alert(
          "Errore: La risposta dal server non è un JSON valido. Controlla la console per dettagli."
        );
      }
    })
    .catch((error) => {
      console.error("Errore nella comunicazione con il server:", error);
      alert("Errore nella comunicazione con il server: " + error.message);
    });
}

// Funzione per aggiungere un nuovo membro dello staff
function addStaff(event) {
  event.preventDefault();

  const form = document.getElementById("addStaffForm");
  if (!form) return;

  const formData = new FormData(form);
  formData.append("action", "add_staff");

  fetch("../database/add_staff.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((text) => JSON.parse(text))
    .then((data) => {
      if (!data.success) {
        alert("Errore: " + data.message);
      } else {
        form.reset();
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("addStaffModal")
        );
        modal.hide();
        location.reload();
      }
    })
    .catch(() => {
      alert("Errore nella comunicazione con il server.");
    });
}
// Assicura che i filtri siano applicati al caricamento della pagina
document.addEventListener("DOMContentLoaded", function () {
  filterStaff("all");
  lucide.createIcons();

  const addStaffForm = document.getElementById("addStaffForm");
  if (addStaffForm) addStaffForm.addEventListener("submit", addStaff);
});

// Funzione per caricare lo staff
function loadStaff() {
  fetch("../database/get_staff.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error(
          "Errore nella risposta del server: " + response.statusText
        );
      }
      return response.text();
    })
    .then((text) => {
      try {
        const data = JSON.parse(text);
        if (!data.success) {
          console.error("Errore nel caricamento dello staff:", data.message);
          alert("Errore nel caricamento dello staff: " + data.message);
          return;
        }

        const staffContainer = document.querySelector(".staff-container");
        if (!staffContainer) {
          console.error("Contenitore staff-container non trovato.");
          return;
        }

        staffContainer.innerHTML = "";

        // Mappatura dei ruoli alle icone
        const roleIcons = {
          'manager': 'shield',
          'cameriere': 'hand-platter',
          'chef' : 'cooking-pot',
          'barista' : 'martini',
          'cassiere' : 'hand-coins',
          // Puoi aggiungere altri ruoli qui, ad esempio:
          // 'chef': 'chef-hat',
          // 'cleaner': 'broom'
        };

        data.staff.forEach((member) => {
          const staffCard = document.createElement("div");
          staffCard.className = "col-md-6 col-lg-4 mb-4 staff-card";
          staffCard.setAttribute("data-role", member.ruolo.toLowerCase());
          staffCard.setAttribute("data-name", member.full_name);
          staffCard.setAttribute("data-hired", member.hired);

          // Sceglie l'icona dal mapping, con default 'user' se il ruolo non è mappato
          const roleIcon = roleIcons[member.ruolo.toLowerCase()] || 'user';

          staffCard.innerHTML = `
                  <div class="card staff-card-content" onclick="openEditStaffModal(
                      '${member.id_username}', '${member.username}', '${
            member.full_name
          }', 
                      '${member.email}', '${member.ruolo}', '${
            member.telefono
          }', '${member.hired}'
                  )">
                      <div class="card-body d-flex align-items-center">
                          <img src="../img/default-staff.jpg" alt="${
                            member.full_name
                          }" class="staff-image me-3">
                          <div>
                              <h5 class="card-title">${member.full_name}</h5>
                              <span class="staff-role staff-role-${member.ruolo.toLowerCase()}">
                                <i data-lucide="${roleIcon}" class="role-icon"></i>
                                ${member.ruolo}
                              </span>
                              <p class="card-text mb-1"><i data-lucide="mail" class="me-1"></i> ${
                                member.email
                              }</p>
                              <p class="card-text mb-1"><i data-lucide="phone" class="me-1"></i> ${
                                member.telefono
                              }</p>
                              <p class="card-text"><i data-lucide="calendar" class="me-1"></i> ${
                                member.hired
                              }</p>
                          </div>
                      </div>
                  </div>
              `;
          staffContainer.appendChild(staffCard);
        });

        lucide.createIcons();

        const activeFilter = document.querySelector(".filter-tab.active");
        if (activeFilter) {
          const role = activeFilter
            .getAttribute("onclick")
            .match(/'([^']+)'/)[1];
          filterStaff(role);
        }
      } catch (e) {
        console.error("Errore di parsing JSON:", e);
        console.log("Testo ricevuto dal server:", text);
        alert("Errore: La risposta dal server non è un JSON valido. Controlla la console per dettagli.");
      }
    })
    .catch((error) => {
      console.error("Errore nel caricamento dello staff:", error);
      alert("Errore nel caricamento dello staff: " + error.message);
    });
}

// Carica lo staff al caricamento della pagina
document.addEventListener("DOMContentLoaded", loadStaff);
