function loadProdiData() {
    console.log("PRODI_JS: Memulai loadProdiData().");
    const prodiTbody = document.querySelector('#prodi-crud .crud-table tbody');

    // LOG 1: Periksa apakah elemen tbody ditemukan
    if (!prodiTbody) {
        console.error("PRODI_JS_ERROR: Elemen 'tbody' untuk tabel prodi tidak ditemukan!");
        return;
    }
    console.log("PRODI_JS: Elemen tbody ditemukan:", prodiTbody);

    prodiTbody.innerHTML = '<tr><td colspan="3">Memuat data...</td></tr>'; // Tampilkan pesan loading

    fetch('../App/Api/prodi_api.php')
        .then(res => {
            if (!res.ok) {
                throw new Error(`Network response was not ok: ${res.statusText}`);
            }
            return res.json();
        })
        .then(res => {
            // LOG 2: Periksa data yang diterima dari API
            console.log("PRODI_JS: Data diterima dari API:", res);

            if (res.success && Array.isArray(res.data)) {
                prodiTbody.innerHTML = ''; // Kosongkan tabel sebelum diisi

                if (res.data.length === 0) {
                    prodiTbody.innerHTML = '<tr><td colspan="3">Tidak ada data program studi.</td></tr>';
                    return;
                }

                res.data.forEach(prodi => {
                    // LOG 3: Periksa setiap item prodi yang akan dirender
                    console.log("PRODI_JS: Merender prodi:", prodi);
                    prodiTbody.innerHTML += `
                        <tr>
                            <td>${prodi.id_prodi}</td>
                            <td>${prodi.nama_prodi}</td>
                            <td>
                                <button class="crud-button edit" data-id="${prodi.id_prodi}" data-nama="${prodi.nama_prodi}">Edit</button>
                                <button class="crud-button delete" data-id="${prodi.id_prodi}">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                console.error('PRODI_JS_ERROR: API tidak mengembalikan data yang sukses atau format data salah.', res.message);
                prodiTbody.innerHTML = `<tr><td colspan="3">Gagal memuat data: ${res.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('PRODI_JS_ERROR: Error saat fetch data prodi:', error);
            prodiTbody.innerHTML = `<tr><td colspan="3">Terjadi kesalahan: ${error.message}</td></tr>`;
        });
}

// Function to initialize event listeners for Prodi section
function initializeProdiEventListeners() {
    // Event listener for Add Prodi button
    const addProdiButton = document.getElementById('add-prodi');
    if (addProdiButton) {
        addProdiButton.addEventListener('click', function() {
            openProdiModal();
        });
    }

    // Event listener for Save Prodi button
    const saveProdiButton = document.getElementById('save-prodi');
    if (saveProdiButton) {
        saveProdiButton.addEventListener('click', function() {
            saveProdi();
        });
    }

    // Event listener for Cancel Prodi button
    const cancelProdiButton = document.getElementById('cancel-prodi');
    if (cancelProdiButton) {
        cancelProdiButton.addEventListener('click', function() {
            closeProdiModal();
        });
    }

    // Event listener for search bar
    const searchProdiBar = document.querySelector('#prodi-crud .search-bar');
    if (searchProdiBar) {
        searchProdiBar.addEventListener('keyup', function() {
            const query = this.value;
            filterProdiTable(query);
        });
    }

    // Event delegation for Edit and Delete buttons
    const prodiCrudSection = document.getElementById('prodi-crud');
    if (prodiCrudSection) {
        prodiCrudSection.addEventListener('click', function(event) {
            const target = event.target;
            if (target.classList.contains('crud-button')) {
                const prodiId = target.dataset.id;
                if (target.classList.contains('edit')) {
                    const prodiNama = target.dataset.nama;
                    openProdiModal({ id_prodi: prodiId, nama_prodi: prodiNama });
                } else if (target.classList.contains('delete')) {
                    deleteProdi(prodiId);
                }
            }
        });
    }
}

// Call loadProdiData and initializeProdiEventListeners when the Prodi tab is activated
document.addEventListener('DOMContentLoaded', function() {
    // Check if the prodi-crud section is active on initial load
    const prodiCrudSection = document.getElementById('prodi-crud');
    if (prodiCrudSection && prodiCrudSection.classList.contains('active')) {
        loadProdiData();
        initializeProdiEventListeners();
    }

    // Listen for custom event from crud_functions.js when a tab is activated
    window.addEventListener('hashchange', function() {
        if (window.location.hash === '#prodi-crud') {
            loadProdiData();
            initializeProdiEventListeners();
        }
    });
});

// Function to open Prodi modal
function openProdiModal(prodi = null) {
    const modalOverlay = document.getElementById('prodi-modal');
    const modalContent = modalOverlay.querySelector('.modal');
    const title = document.getElementById('prodi-modal-title');
    const prodiIdInput = document.getElementById('prodi-id');
    const namaProdiInput = document.getElementById('prodi-nama');
    const prodiForm = document.getElementById('form-prd');

    // Reset form before opening
    if (prodiForm) {
        prodiForm.reset();
    }

    // Pastikan semua elemen ditemukan sebelum melanjutkan
    if (!modalOverlay || !modalContent || !title || !prodiIdInput || !namaProdiInput) {
        console.error("Satu atau lebih elemen modal Prodi tidak ditemukan.");
        return;
    }

    if (prodi) {
        title.textContent = 'Edit Program Studi';
        prodiIdInput.value = prodi.id_prodi;
        namaProdiInput.value = prodi.nama_prodi;
    } else {
        title.textContent = 'Tambah Program Studi Baru';
        prodiIdInput.value = '';
        namaProdiInput.value = '';
    }
    
    // Tampilkan overlay
    modalOverlay.style.display = 'flex';
    
    // PERBAIKAN: Gunakan setTimeout untuk memungkinkan transisi CSS berjalan dengan benar
    setTimeout(() => {
        modalOverlay.classList.add('active');
        modalContent.classList.add('active');
    }, 10); // Penundaan kecil untuk memulai transisi
}

// Function to close Prodi modal
function closeProdiModal() {
    const modalOverlay = document.getElementById('prodi-modal');
    const modalContent = modalOverlay.querySelector('.modal');

    if (modalOverlay && modalContent) {
        modalOverlay.classList.remove('active');
        modalContent.classList.remove('active');
        
        // Sembunyikan elemen setelah transisi selesai
        setTimeout(() => {
            modalOverlay.style.display = 'none';
        }, 300); // Sesuaikan dengan durasi transisi di CSS Anda
    }
}

// Function to save Prodi (add or update)
function saveProdi() {
    const prodiId = document.getElementById('prodi-id').value; // Ambil nilai langsung
    const namaProdi = document.getElementById('prodi-nama').value.trim();

    if (!namaProdi) {
        alert('Nama Program Studi tidak boleh kosong.');
        return;
    }

    const action = prodiId ? 'update' : 'create';
    const url = '../App/Api/prodi_api.php';

    const payload = {
        action: action,
        id_prodi: prodiId,
        nama_prodi: namaProdi
    };

    // --- TAMBAHKAN LOGGING INI ---
    console.log("PRODI_JS: Payload yang akan dikirim:", payload);
    console.log("PRODI_JS: Payload dalam format JSON:", JSON.stringify(payload));
    // --- AKHIR LOGGING ---

    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            // Log the response status and text for better debugging
            console.error(`PRODI_JS_ERROR: HTTP error! status: ${response.status}, statusText: ${response.statusText}`);
            // Attempt to read response body for more details
            return response.text().then(text => { throw new Error(`HTTP error! status: ${response.status}, statusText: ${response.statusText}, Body: ${text}`); });
        }
        return response.json();
    })
    .then(data => {
        console.log("PRODI_JS: Response data dari API:", data);
        if (data.success) {
            alert(data.message);
            closeProdiModal();
            loadProdiData(); // Reload data after save
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('PRODI_JS_ERROR: Error saat fetch data prodi:', error);
        alert('Failed to save data. Please try again. Check console for details.');
    });
}

// Function to edit Prodi
function editProdi(id_prodi) {
    console.log("PRODI_JS: Memulai editProdi() untuk ID:", id_prodi);
    fetch(`../App/Api/prodi_api.php?action=read_single&id_prodi=${id_prodi}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("PRODI_JS: Data prodi untuk edit diterima:", data);
            if (data.success && data.data) {
                openProdiModal(data.data);
            } else {
                alert('Error: ' + (data.message || 'Prodi not found.'));
            }
        })
        .catch(error => {
            console.error('PRODI_JS_ERROR: Error fetching Prodi for edit:', error);
            alert('Failed to fetch Prodi data for editing.');
        });
}

// Function to delete Prodi
function deleteProdi(id_prodi) {
    console.log("PRODI_JS: Memulai deleteProdi() untuk ID:", id_prodi);
    if (confirm('Apakah Anda yakin ingin menghapus program studi ini?')) {
        const payload = {
            action: 'delete',
            id_prodi: id_prodi
        };

        fetch('../App/Api/prodi_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("PRODI_JS: Respon delete diterima:", data);
            if (data.success) {
                alert(data.message);
                loadProdiData(); // Reload data after deletion
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('PRODI_JS_ERROR: Error deleting Prodi:', error);
            alert('Failed to delete data. Please try again.');
        });
    }
}
// Function to filter Prodi table
function filterProdiTable(query) {
    const rows = document.querySelectorAll('#prodi-crud .crud-table tbody tr');
    const searchQuery = query.toLowerCase();

    rows.forEach(row => {
        const idProdi = row.cells[0].textContent.toLowerCase();
        const namaProdi = row.cells[1].textContent.toLowerCase();

        if (idProdi.includes(searchQuery) || namaProdi.includes(searchQuery)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
