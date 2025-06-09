// Fungsi initKelas dibuat global untuk dipanggil oleh crud_functions.js
window.initKelas = function() {
    const kelasContent = document.getElementById('kelas-crud');
    // Jika listener sudah dipasang, cukup muat ulang data.
    if (kelasContent && kelasContent.dataset.listenersAttached === 'true') {
        window.loadKelasData();
        return;
    }
    console.log("Menginisialisasi modul Kelas untuk pertama kali...");

    // --- Deklarasi Elemen DOM ---
    const kelasTbody = kelasContent.querySelector('.crud-table tbody');
    const modal = document.getElementById('kelas-modal');
    const modalTitle = document.getElementById('kelas-modal-title');
    const addKelasButton = document.getElementById('add-kelas');
    const saveKelasBtn = document.getElementById('save-kelas');
    const cancelKelasBtn = document.getElementById('cancel-kelas');
    const closeModalButton = document.getElementById('close-modal-kelas');

    const kelasIdInput = document.getElementById('kelas-id');
    const namaKelasInput = document.getElementById('kls-nama'); // Changed from kelas-nama
    const prodiSelect = document.getElementById('prodi-kelas'); // Changed from kelas-prodi
    const dosenWaliSelect = document.getElementById('dosen-wali-kelas'); // Changed from kelas-dosen
    const tahunAjaranInput = document.getElementById('tahun-ajaran-kelas');

    const API_URL = '../App/Api/kelas_api.php';

    let allKelasData = []; // Variabel untuk menyimpan semua data kelas

    // --- Definisi Fungsi ---
    function showKelasModal(title, kelasIdToEdit = null) {
        const kelasForm = document.getElementById('form-kls'); // Get form element inside the function
        console.log(`EDIT_DEBUG: showKelasModal dipanggil. Mode: ${kelasIdToEdit ? 'edit' : 'add'}, ID: ${kelasIdToEdit}`);
        if (kelasForm) {
            kelasForm.reset();
        }
        kelasIdInput.value = '';
        modalTitle.textContent = title;

        // Disable save button initially to prevent premature saving in edit mode
        saveKelasBtn.disabled = true;

        // Tampilkan overlay
        modal.style.display = 'flex';

        // PERBAIKAN: Gunakan setTimeout untuk memungkinkan transisi CSS berjalan
        setTimeout(() => {
            modal.classList.add('active');
            modal.querySelector('.modal').classList.add('active');
        }, 10);

        // Ambil data untuk dropdown secara bersamaan
        const prodiPromise = fetch('../App/Api/prodi_api.php').then(res => res.json());
        const dosenPromise = fetch('../App/Api/dosen_api.php').then(res => res.json());

        Promise.all([prodiPromise, dosenPromise])
            .then(([prodiRes, dosenRes]) => {
                // Populate Prodi Dropdown
                prodiSelect.innerHTML = '<option value="">Pilih Program Studi</option>';
                if (prodiRes.success) {
                    prodiRes.data.forEach(prodi => {
                        prodiSelect.innerHTML += `<option value="${prodi.id_prodi}">${prodi.nama_prodi}</option>`;
                    });
                }

                // Populate Dosen Wali Dropdown
                dosenWaliSelect.innerHTML = '<option value="">Pilih Dosen Wali</option>';
                if (dosenRes.success) {
                    dosenRes.data.forEach(dosen => {
                        dosenWaliSelect.innerHTML += `<option value="${dosen.nidn}">${dosen.nama_lengkap}</option>`;
                    });
                }

                // Jika ini mode edit, ambil data kelas dan set value setelah dropdown terisi
                if (kelasIdToEdit) {
                    console.log(`EDIT_DEBUG: Memulai fetch data kelas untuk ID: ${kelasIdToEdit}`);
                    // Fetch data kelas by ID dan set nilai form
                    fetch(`${API_URL}?action=get_by_id&id=${kelasIdToEdit}`)
                        .then(res => res.json())
                        .then(res => {
                            console.log('EDIT_DEBUG: Respon API untuk data kelas:', res);
                            if (res.success && res.data) {
                                const kelas = res.data;
                                console.log('EDIT_DEBUG: Data kelas yang diterima:', kelas);
                                kelasIdInput.value = kelas.id_kelas;
                                console.log(`EDIT_DEBUG: Nilai dari input #kelas-id di-set menjadi: "${kelasIdInput.value}"`);
                                namaKelasInput.value = kelas.nama_kelas;
                                prodiSelect.value = kelas.id_prodi;
                                dosenWaliSelect.value = kelas.id_dosen_wali;
                                tahunAjaranInput.value = kelas.tahun_ajaran;
                                // Enable save button after all data is loaded
                                saveKelasBtn.disabled = false;
                            } else {
                                alert('Gagal memuat data kelas untuk diedit.');
                                hideKelasModal(); // Hide modal if data fetch fails
                            }
                        }).catch(error => {
                            console.error('Error fetching Kelas for edit:', error);
                            alert('Gagal memuat data kelas untuk diedit. Periksa konsol.');
                            hideKelasModal(); // Hide modal on error
                        });
                } else {
                    // If in add mode, enable save button immediately
                    saveKelasBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Gagal memuat data untuk modal:', error);
                alert('Gagal memuat data dropdown. Periksa konsol.');
                hideKelasModal(); // Hide modal on error
            });
    }

    function hideKelasModal() {
        const modalOverlay = document.getElementById('kelas-modal');
        const modalContent = modalOverlay.querySelector('.modal');

        if (modalOverlay && modalContent) {
            modalOverlay.classList.remove('active');
            modalContent.classList.remove('active');

            // Sembunyikan elemen setelah transisi selesai
            // Durasi harus cocok dengan durasi transisi di crud_admin.css
            setTimeout(() => {
                modalOverlay.style.display = 'none';
            }, 300);
        }
    }

    function saveKelas() {
        const id = document.getElementById('kelas-id').value;
        console.log(`EDIT_DEBUG: Tombol simpan diklik. ID yang terbaca dari form: "${id}"`);
        const kelasData = {
            id_kelas: id,
            nama_kelas: namaKelasInput.value,
            id_prodi: prodiSelect.value,
            id_dosen_wali: dosenWaliSelect.value,
            tahun_ajaran: tahunAjaranInput.value
        };

        const action = kelasData.id_kelas ? 'update' : 'create';
        const payload = {
            action: action,
            data: kelasData
        };

        console.log('EDIT_DEBUG: Payload yang akan dikirim ke API:', payload);

        fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data kelas berhasil disimpan!');
                    hideKelasModal();
                    window.loadKelasData(); // Muat ulang data tabel
                } else {
                    alert('Gagal menyimpan data kelas: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error saving kelas:', error);
                alert('Terjadi kesalahan saat menyimpan data kelas.');
            });
    }

    function deleteKelas(id) {
        if (confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
            const payload = {
                action: 'delete',
                data: {
                    id_kelas: id
                }
            };

            console.log('DELETE_DEBUG: Payload yang akan dikirim ke API:', payload);

            fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Kelas berhasil dihapus!');
                        window.loadKelasData(); // Muat ulang data tabel
                    } else {
                        alert('Gagal menghapus kelas: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deleting kelas:', error);
                    alert('Terjadi kesalahan saat menghapus kelas.');
                });
        }
    }

    window.loadKelasData = function() {
        fetch(API_URL)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    allKelasData = res.data; // Simpan data lengkap
                    filterKelasTable(); // Tampilkan semua data awalnya
                } else {
                    kelasTbody.innerHTML = '<tr><td colspan="6">Tidak ada data kelas.</td></tr>';
                }
            }).catch(error => console.error('Error loading Kelas data:', error));
    };

    function filterKelasTable() {
        const searchInput = document.getElementById('kelas-search-input');
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        kelasTbody.innerHTML = '';

        const filteredData = allKelasData.filter(kelas => {
            return (kelas.nama_kelas && kelas.nama_kelas.toLowerCase().includes(searchTerm)) ||
                   (kelas.nama_prodi && kelas.nama_prodi.toLowerCase().includes(searchTerm)) ||
                   (kelas.nama_dosen_wali && kelas.nama_dosen_wali.toLowerCase().includes(searchTerm)) ||
                   (kelas.tahun_ajaran && kelas.tahun_ajaran.toString().toLowerCase().includes(searchTerm));
        });

        if (filteredData.length > 0) {
            filteredData.forEach(kelas => {
                kelasTbody.innerHTML += `
                    <tr>
                        <td>${kelas.id_kelas}</td>
                        <td>${kelas.nama_kelas}</td>
                        <td>${kelas.nama_prodi}</td>
                        <td>${kelas.nama_dosen_wali}</td>
                        <td>${kelas.tahun_ajaran}</td>
                        <td>
                            <button class="crud-button edit" data-id="${kelas.id_kelas}">Edit</button>
                            <button class="crud-button delete" data-id="${kelas.id_kelas}">Hapus</button>
                        </td>
                    </tr>`;
            });
        } else {
            kelasTbody.innerHTML = '<tr><td colspan="6">Tidak ada data kelas yang cocok.</td></tr>';
        }
    }

    // --- Event Listeners ---
    addKelasButton.addEventListener('click', () => showKelasModal('Tambah Kelas'));
    cancelKelasBtn.addEventListener('click', hideKelasModal);
    closeModalButton.addEventListener('click', hideKelasModal);
    saveKelasBtn.addEventListener('click', saveKelas);

    // Event listener untuk search input
    const kelasSearchInput = document.getElementById('kelas-search-input');
    if (kelasSearchInput) {
        kelasSearchInput.addEventListener('keyup', filterKelasTable);
    }

    kelasTbody.addEventListener('click', (e) => {
        if (e.target.classList.contains('edit')) {
            const id = e.target.dataset.id;
            console.log(`EDIT_DEBUG: Tombol Edit diklik untuk ID: ${id}`);
            showKelasModal('Edit Kelas', id);
        }
        if (e.target.classList.contains('delete')) {
            const id = e.target.dataset.id;
            console.log(`DELETE_DEBUG: Tombol Hapus diklik untuk ID: ${id}`);
            deleteKelas(id);
        }
    });

    // Tandai bahwa listener sudah dipasang
    kelasContent.dataset.listenersAttached = 'true';

    // Muat data awal saat inisialisasi
    window.loadKelasData();
};

// Panggil initKelas saat DOMContentLoaded jika elemen kelas-crud ada
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('kelas-crud')) {
        window.initKelas();
    }
});