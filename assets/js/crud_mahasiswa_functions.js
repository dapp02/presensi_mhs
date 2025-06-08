// Fungsi init dibuat global untuk dipanggil oleh crud_functions.js
window.initMahasiswa = function() {
    const mhsContent = document.getElementById('mahasiswa-crud');
    if (mhsContent && mhsContent.dataset.listenersAttached === 'true') {
        window.loadMahasiswaData();
        return;
    }
    console.log("Menginisialisasi modul Mahasiswa untuk pertama kali...");

    // Deklarasi Elemen DOM yang akan sering digunakan
    const mhsTbody = document.querySelector('#mahasiswa-crud .crud-table tbody');

    // Definisikan fungsi load
    window.loadMahasiswaData = function() {
        console.log("Memuat data mahasiswa...");
        if (!mhsTbody) return;
        mhsTbody.innerHTML = '<tr><td colspan="5">Memuat data...</td></tr>';

        fetch('../App/Api/crud_mahasiswa_api.php')
            .then(res => res.json())
            .then(res => {
                mhsTbody.innerHTML = '';
                if (res.success && res.data.length > 0) {
                    res.data.forEach(mhs => {
                        mhsTbody.innerHTML += `
                            <tr>
                                <td>${mhs.nim}</td>
                                <td>${mhs.nama_lengkap}</td>
                                <td>${mhs.nama_prodi || '-'}</td>
                                <td>${mhs.nama_kelas || '-'}</td>
                                <td>
                                    <button class="crud-button edit" data-nim="${mhs.nim}">Edit</button>
                                    <button class="crud-button delete" data-nim="${mhs.nim}" data-id_kelas="${mhs.id_kelas || ''}">Hapus</button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    mhsTbody.innerHTML = '<tr><td colspan="5">Tidak ada data mahasiswa.</td></tr>';
                }
            }).catch(error => console.error('Error loading Mahasiswa data:', error));
    };
    
    // --- Deklarasi Elemen DOM --- 
    const modal = document.getElementById('mahasiswa-modal');
    const modalTitle = document.getElementById('mahasiswa-modal-title');
    const addMahasiswaButton = document.getElementById('add-mahasiswa');
    const saveMahasiswaBtn = document.getElementById('save-mahasiswa');
    const cancelMahasiswaBtn = document.getElementById('cancel-mahasiswa');
    const closeModalButton = document.getElementById('close-mahasiswa-modal');

    const nimInput = document.getElementById('mhs-nim');
    const namaLengkapInput = document.getElementById('mhs-nama-lengkap');
    const passwordInput = document.getElementById('mhs-password');
    const prodiSelect = document.getElementById('mhs-prodi');
    const kelasSelect = document.getElementById('mhs-kelas');

    const API_URL = '../App/Api/crud_mahasiswa_api.php';

    // --- Definisi Fungsi --- 
    function showMahasiswaModal(title, nimToEdit = null) {
        const mahasiswaForm = document.getElementById('form-mhs');
        if (mahasiswaForm) {
            mahasiswaForm.reset();
        }
        // Reset form dan input
        if (mahasiswaForm) {
            mahasiswaForm.reset();
        }
        nimInput.value = '';
        namaLengkapInput.value = '';
        // namaLengkapInput.readOnly = true; // Removed to make it editable
        passwordInput.value = '';
        prodiSelect.value = '';
        kelasSelect.value = '';

        modalTitle.textContent = title;
        saveMahasiswaBtn.disabled = true;
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('active');
            modal.querySelector('.modal').classList.add('active');
        }, 10);

        // Fetch data untuk dropdown
        const prodiPromise = fetch('../App/Api/prodi_api.php').then(res => res.json());
        const kelasPromise = fetch('../App/Api/kelas_api.php').then(res => res.json());
        const mahasiswaPromise = fetch(`${API_URL}?action=get_all_mahasiswa_simple`).then(res => res.json());

        Promise.all([prodiPromise, kelasPromise, mahasiswaPromise])
            .then(([prodiRes, kelasRes, mahasiswaRes]) => {
                // Populate Prodi dropdown
                prodiSelect.innerHTML = '<option value="">Pilih Program Studi</option>';
                if (prodiRes.success) {
                    prodiRes.data.forEach(prodi => {
                        prodiSelect.innerHTML += `<option value="${prodi.id_prodi}">${prodi.nama_prodi}</option>`;
                    });
                }

                // Populate Kelas dropdown
                kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
                if (kelasRes.success) {
                    kelasRes.data.forEach(kelas => {
                        kelasSelect.innerHTML += `<option value="${kelas.id_kelas}">${kelas.nama_kelas}</option>`;
                    });
                }

                // Populate NIM dropdown
                if (mahasiswaRes.success) {
                    nimInput.innerHTML = '<option value="">Pilih NIM</option>'; // Clear and add default option
                    mahasiswaRes.data.forEach(mhs => {
                        nimInput.innerHTML += `<option value="${mhs.nim}">${mhs.nim} - ${mhs.nama_lengkap}</option>`;
                    });
                }

                if (nimToEdit) {
                    // Jika mode edit, pilih NIM yang sesuai dan isi data lainnya
                    fetch(`${API_URL}?action=get_by_id&id=${nimToEdit}`)
                        .then(res => res.json())
                        .then(res => {
                            if (res.success && res.data) {
                                const mhs = res.data;
                                nimInput.value = mhs.nim;
                    nimInput.disabled = true;
                                namaLengkapInput.value = mhs.nama_lengkap;
                                // namaLengkapInput.readOnly = true; // Removed to make it editable

                                prodiSelect.value = mhs.id_prodi || '';
                                prodiSelect.disabled = true; // Disable prodi selection on edit
                                kelasSelect.value = mhs.id_kelas || '';
                                passwordInput.placeholder = 'Biarkan kosong jika tidak ingin mengubah password';
                                saveMahasiswaBtn.disabled = false;
                            } else {
                                alert('Gagal memuat data mahasiswa untuk diedit.');
                                hideMahasiswaModal();
                            }
                        }).catch(error => {
                            console.error('Error fetching Mahasiswa for edit:', error);
                            alert('Gagal memuat data mahasiswa untuk diedit. Periksa konsol.');
                            hideMahasiswaModal();
                        });
                } else {
                    // Jika mode tambah, aktifkan NIM input dan tambahkan event listener
                    nimInput.disabled = false;
                    prodiSelect.disabled = false; // Enable prodi selection on add
                    kelasSelect.disabled = false; // Enable kelas selection on add
                    saveMahasiswaBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Gagal memuat data untuk modal:', error);
                alert('Gagal memuat data dropdown. Periksa konsol.');
                hideMahasiswaModal();
            });

        // Event listener untuk NIM dropdown
        nimInput.addEventListener('change', function() {
            const selectedNim = this.value;
            if (selectedNim) {
                const selectedOption = nimInput.options[nimInput.selectedIndex];
                const selectedName = selectedOption.textContent.split(' - ')[1];
                namaLengkapInput.value = selectedName;
            } else {
                namaLengkapInput.value = '';
            }
        });

        // Event listener untuk Kelas dropdown
        kelasSelect.addEventListener('change', function() {
            const selectedKelasId = this.value;
            if (selectedKelasId) {
                fetch(`../App/Api/kelas_api.php?action=get_by_id&id=${selectedKelasId}`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.success && res.data) {
                            prodiSelect.value = res.data.id_prodi || '';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching Kelas data:', error);
                    });
            } else {
                prodiSelect.value = '';
            }
        });
    }

    function hideMahasiswaModal() {
        const modalOverlay = document.getElementById('mahasiswa-modal');
        const modalContent = modalOverlay.querySelector('.modal');

        if (modalOverlay && modalContent) {
            modalOverlay.classList.remove('active');
            modalContent.classList.remove('active');
            setTimeout(() => {
                modalOverlay.style.display = 'none';
            }, 300);
        }
    }

    function saveMahasiswa() {
        const isEdit = modalTitle.textContent.includes('Edit'); // Determine if it's an edit operation
        const nim = isEdit ? nimInput.value : nimInput.value; // For edit, use nimInput.value; for add, use nimInput.value
        const namaLengkap = namaLengkapInput.value;

        const mahasiswaData = {
            nim: nim,
            nama_lengkap: namaLengkap,
            id_prodi: prodiSelect.value || null,
            id_kelas: kelasSelect.value || null,
        };

        // Only include password if it's a new entry or password field is not empty for edit
        if (!isEdit || passwordInput.value) {
            mahasiswaData.password = passwordInput.value;
        }



        let action;
        let payload;

        if (isEdit) {
            action = 'update';
            payload = {
                action: action,
                data: mahasiswaData
            };
        } else {
            // For 'Tambah Mahasiswa Baru' (create mode), we now assign an existing student to a class
            action = 'assignMahasiswaToKelas';
            payload = {
                action: action,
                data: {
                    nim: nimInput.value, // Use the selected NIM from the dropdown
                    id_kelas: kelasSelect.value || null // Use the selected class
                }
            };
        }

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
                    alert('Data mahasiswa berhasil disimpan!');
                    hideMahasiswaModal();
                    window.loadMahasiswaData();
                } else {
                    alert('Gagal menyimpan data mahasiswa: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error saving mahasiswa:', error);
                alert('Terjadi kesalahan saat menyimpan data mahasiswa. Detail: ' + error.message);
            });
    }

    function deleteMahasiswa(nim, id_kelas) {
        if (confirm('Apakah Anda yakin ingin mengeluarkan mahasiswa ini dari kelas ini?')) {
            const payload = {
                action: 'removeMahasiswaFromKelas',
                data: {
                    nim: nim,
                    id_kelas: id_kelas
                }
            };
            console.log('Sending delete payload:', payload);

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
                        alert('Mahasiswa berhasil dihapus!');
                        window.loadMahasiswaData();
                    } else {
                        alert('Gagal menghapus mahasiswa: ' + data.message);
                    }mhsContent.dataset.listenersAttached = 'true';
                })
                .catch(error => {
                    console.error('Error deleting mahasiswa:', error);
                    alert('Terjadi kesalahan saat menghapus mahasiswa.');
                });
        }
    }

    // --- Event Listeners --- 
    addMahasiswaButton.addEventListener('click', () => showMahasiswaModal('Tambah Mahasiswa Baru'));
    closeModalButton.addEventListener('click', hideMahasiswaModal);
    cancelMahasiswaBtn.addEventListener('click', hideMahasiswaModal);
    saveMahasiswaBtn.addEventListener('click', saveMahasiswa);

    mhsTbody.addEventListener('click', function(event) {
        const target = event.target;
        if (target.classList.contains('edit')) {
            const nim = target.dataset.nim;
            showMahasiswaModal('Edit Data Mahasiswa', nim);
        } else if (target.classList.contains('delete')) {
            const nim = target.dataset.nim;
            const id_kelas = target.dataset.id_kelas;
            deleteMahasiswa(nim, id_kelas);
        }
    });

    // Search functionality
    const searchInput = document.getElementById('mahasiswa-search-input');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = mhsTbody.querySelectorAll('tr');
        rows.forEach(row => {
            const nim = row.cells[0].textContent.toLowerCase();
            const nama = row.cells[1].textContent.toLowerCase();
            if (nim.includes(filter) || nama.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    console.log("Modul Mahasiswa diinisialisasi.");
    window.loadMahasiswaData(); // Muat data saat pertama kali diinisialisasi
};