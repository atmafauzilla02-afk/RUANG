// === Sidebar toggle for mobile ===
document.getElementById("toggleSidebar").addEventListener("click", () => {
  document.querySelector(".sidebar").classList.toggle("active");
});

// === Dummy data ===
let warga = [
  { nama: "Sri Rahayu", nik: "3210123456789012", alamat: "Jl. Mawar No. 12", telp: "08123456700" },
  { nama: "Budi Surecep", nik: "3210123456789013", alamat: "Jl. Kenanga No. 7", telp: "081234567891" },
  { nama: "Ayu Lestari", nik: "3210123456789014", alamat: "Jl. Melati No. 3", telp: "081234567892" },
  { nama: "Joko Hartono", nik: "3210123456789015", alamat: "Jl. Anggrek No. 8", telp: "081234567893" },
];

// === Render tabel ===
const wargaList = document.getElementById("wargaList");
function renderTable(data) {
  wargaList.innerHTML = data
    .map(
      (w, i) => `
      <tr>
        <td>${w.nama}</td>
        <td>${w.nik}</td>
        <td>${w.alamat}</td>
        <td>${w.telp}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm" onclick="editWarga(${i})"><i class="fa-solid fa-pen"></i></button>
          <button class="btn btn-danger btn-sm" onclick="hapusWarga(${i})"><i class="fa-solid fa-trash"></i></button>
        </td>
      </tr>`
    )
    .join("");
}
renderTable(warga);

// === Tambah/Edit warga ===
const form = document.getElementById("wargaForm");
const modalTitle = document.getElementById("modalTitle");
const modal = new bootstrap.Modal(document.getElementById("modalWarga"));

form.addEventListener("submit", (e) => {
  e.preventDefault();
  const newWarga = {
    nama: form.nama.value,
    nik: form.nik.value,
    alamat: form.alamat.value,
    telp: form.telp.value,
  };
  const index = form.wargaIndex.value;
  if (index) warga[index] = newWarga;
  else warga.push(newWarga);

  renderTable(warga);
  modal.hide();
  form.reset();
  form.wargaIndex.value = "";
});

// === Hapus ===
window.hapusWarga = (index) => {
  if (confirm("Yakin ingin menghapus data ini?")) {
    warga.splice(index, 1);
    renderTable(warga);
  }
};

// === Edit ===
window.editWarga = (index) => {
  const w = warga[index];
  form.nama.value = w.nama;
  form.nik.value = w.nik;
  form.alamat.value = w.alamat;
  form.telp.value = w.telp;
  form.wargaIndex.value = index;
  modalTitle.textContent = "Edit Warga";
  modal.show();
};

// === Search ===
document.getElementById("searchInput").addEventListener("input", (e) => {
  const keyword = e.target.value.toLowerCase();
  const filtered = warga.filter((w) => w.nama.toLowerCase().includes(keyword));
  renderTable(filtered);
});
