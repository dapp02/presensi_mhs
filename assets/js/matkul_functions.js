// Fungsi initMatkul dibuat global untuk dipanggil oleh crud_functions.js
window.initMatkul = function() {
    const matkulContent = document.getElementById('matakuliah-crud');
    // Jika listener sudah dipasang, cukup muat ulang data.
    if (matkulContent && matkulContent.dataset.listenersAttached === 'true') {
        window.loadMatkulData();
        return;
    }
    console.log("Menginisialisasi modul Mata Kuliah untuk pertama kali...");

    // === DEKLARASI ELEMEN DOM ===
    const matkulTbody = matkulContent.querySelector('.crud-table tbody');
    const modal = document.getElementById('matakuliah-modal');
    const modalContent = modal.querySelector('.modal');
    const modalTitle = document.getElementById('matakuliah-modal-title');
    const matkulForm = document.getElementById('form-matkul');
    const matkulIdInput = document.getElementById('matkul-id'); // Asumsi ada input hidden ini
    const API_URL = '../App/Api/matakuliah_api.php';

    // === FUNGSI-FUNGSI ===

    window.loadMatkulData = function() {
        fetch(API_URL)
            .then(res => res.json())
            .then(res => {
                matkulTbody.innerHTML = '';
                if (res.success && res.data.length > 0) {
                    res.data.forEach(matkul => {
                        matkulTbody.innerHTML += `
                            <tr>
                                <td>${matkul.kode_matkul}</td>
                                <td>${matkul.nama_matkul}</td>
                                <td>${matkul.sks}</td>
                                <td>${matkul.nama_prodi}</td>
                                <td>
                                    <button class="crud-button edit" data-id="${matkul.id_matkul}">Edit</button>
                                    <button class="crud-button delete" data-id="${matkul.id_matkul}">Hapus</button>
                                </td>
                            </tr>`;
                    });
                } else {
                    matkulTbody.innerHTML = '<tr><td colspan="5">Tidak ada data mata kuliah.</td></tr>';
                }
            }).catch(error => console.error('Error loading Mata Kuliah data:', error));
    };

    function hideMatkulModal() {
        modal.classList.remove('active');
        if(modalContent) modalContent.classList.remove('active');
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    async function showMatkulModal(title, id = null) {
        matkulForm.reset();
        modalTitle.textContent = title;
        matkulIdInput.value = id;

        // Ambil data prodi untuk dropdown
        try {
            const response = await fetch('../App/Api/prodi_api.php');
            const prodiRes = await response.json();
            const prodiSelect = document.getElementById('matkul-prodi');

            prodiSelect.innerHTML = '<option value="">Pilih Program Studi</option>';
            if (prodiRes.success) {
                prodiRes.data.forEach(prodi => {
                    prodiSelect.innerHTML += `<option value="${prodi.id_prodi}">${prodi.nama_prodi}</option>`;
                });
            }

            // Jika mode edit, ambil data matkul dan isi form
            if (id) {
                const matkulResponse = await fetch(`${API_URL}?action=get_by_id&id=${id}`);
                const matkulResult = await matkulResponse.json();
                if (matkulResult.success && matkulResult.data) {
                    const matkulData = matkulResult.data;
                    document.getElementById('matkul-kode').value = matkulData.kode_matkul;
                    document.getElementById('matkul-nama').value = matkulData.nama_matkul;
                    document.getElementById('matkul-sks').value = matkulData.sks;
                    document.getElementById('matkul-prodi').value = matkulData.id_prodi;
                } else {
                    console.error('Error fetching single matkul data:', matkulResult.message);
                    alert('Gagal memuat data mata kuliah untuk diedit.');
                }
            }

            // Tampilkan modal
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('active');
                if(modalContent) modalContent.classList.add('active');
            }, 10);

        } catch (error) {
            console.error("Gagal memuat data untuk modal:", error);
            alert('Terjadi kesalahan saat memuat data form.');
        }
    }

    async function saveMatkul() {
        const id = document.getElementById('matkul-id').value;
        const action = id ? 'update' : 'create';

        const payload = {
            action: action,
            id_matkul: id,
            kode_matkul: document.getElementById('matkul-kode').value,
            nama_matkul: document.getElementById('matkul-nama').value,
            sks: document.getElementById('matkul-sks').value,
            id_prodi: document.getElementById('matkul-prodi').value
        };

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const result = await response.json();

            if (result.success) {
                alert(result.message);
                hideMatkulModal();
                window.loadMatkulData();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan mata kuliah: ' + error.message);
        }
    }

    async function deleteMatkul(id) {
        if (confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')) {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action: 'delete', id_matkul: id })
                });
                const result = await response.json();

                if (result.success) {
                    alert('Mata Kuliah berhasil dihapus!');
                    window.loadMatkulData();
                } else {
                    alert('Error deleting mata kuliah: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting mata kuliah.');
            }
        }
    }

    // === EVENT LISTENERS ===
    document.getElementById('add-matakuliah').addEventListener('click', () => showMatkulModal('Tambah Mata Kuliah Baru'));
    document.getElementById('save-matakuliah').addEventListener('click', saveMatkul);
    document.getElementById('cancel-matakuliah').addEventListener('click', hideMatkulModal);
    document.getElementById('close-matakuliah-modal').addEventListener('click', hideMatkulModal);

    // Gunakan Event Delegation untuk tombol Edit dan Hapus
    matkulTbody.addEventListener('click', function(e) {
        const target = e.target;
        const id = target.dataset.id;
        if (target.classList.contains('edit') && id) {
            showMatkulModal('Edit Mata Kuliah', id);
        }
        if (target.classList.contains('delete') && id) {
            deleteMatkul(id);
        }
    });

    // Tambahkan event listener untuk modal overlay
    const modalOverlay = document.getElementById('matakuliah-modal');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(event) {
            // Jika yang diklik adalah overlay itu sendiri (bukan modal-content di dalamnya)
            if (event.target === modalOverlay) {
                hideMatkulModal();
            }
        });
    }

    matkulContent.dataset.listenersAttached = 'true';
    window.loadMatkulData();
};