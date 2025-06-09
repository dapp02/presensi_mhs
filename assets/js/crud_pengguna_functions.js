window.initPengguna = function() {
    const penggunaContent = document.getElementById('pengguna-crud');
    if (penggunaContent.dataset.listenersAttached === 'true') {
        return window.loadPenggunaData();
    }
    console.log("Menginisialisasi modul Manajemen Akun...");

    const penggunaTbody = penggunaContent.querySelector('.crud-table tbody');
    const modal = document.getElementById('pengguna-modal');
    const modalContent = modal.querySelector('.modal');
    const form = document.getElementById('pengguna-form');
    const API_URL = '../App/Api/crud_pengguna_api.php';

    window.loadPenggunaData = function() {
        fetch(API_URL).then(res => res.json()).then(res => {
            penggunaTbody.innerHTML = '';
            if (res.success && res.data) {
                res.data.forEach(user => {
                    const idNumber = user.nim || user.nidn || '<span style="color: #888;">Belum Diatur</span>';
                    penggunaTbody.innerHTML += `
                        <tr>
                            <td>${user.nama_lengkap}</td>
                            <td>${user.username}</td>
                            <td>${user.email}</td>
                            <td>${user.role}</td>
                            <td>${idNumber}</td>
                            <td>
                                ${user.role !== 'admin' ? `<button class="crud-button edit" data-id="${user.id_pengguna}">Lengkapi Data</button>` : ''}
                            </td>
                        </tr>`;
                });
            }
        });
    };

    function hidePenggunaModal() {
        modal.classList.remove('active');
        if(modalContent) modalContent.classList.remove('active');
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    async function showPenggunaModal(id) {
        form.reset();
        try {
            const response = await fetch(`${API_URL}?action=get_by_id&id=${id}`);
            const result = await response.json();
            if (result.success && result.data) {
                const user = result.data;
                document.getElementById('pengguna-id').value = user.id_pengguna;
                document.getElementById('pengguna-role').value = user.role;
                document.getElementById('pengguna-nama').value = user.nama_lengkap;
                
                const idLabel = document.getElementById('pengguna-id-label');
                const idInput = document.getElementById('pengguna-id-number');
                
                if (user.role === 'mahasiswa') {
                    idLabel.textContent = 'NIM';
                    idInput.value = user.nim || '';
                } else if (user.role === 'dosen') {
                    idLabel.textContent = 'NIDN';
                    idInput.value = user.nidn || '';
                }

                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.classList.add('active');
                    if(modalContent) modalContent.classList.add('active');
                }, 10);
            }
        } catch (error) { console.error('Error fetching user data:', error); }
    }

    function savePenggunaRole() {
        const payload = {
            action: 'assign_role',
            id_pengguna: document.getElementById('pengguna-id').value,
            role: document.getElementById('pengguna-role').value,
            id_number: document.getElementById('pengguna-id-number').value
        };

        if (!payload.id_number.trim()) {
            return alert('NIM / NIDN tidak boleh kosong.');
        }

        fetch(API_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        }).then(res => res.json()).then(res => {
            alert(res.message);
            if (res.success) {
                hidePenggunaModal();
                window.loadPenggunaData();
            }
        });
    }

    // --- EVENT LISTENERS ---
    penggunaTbody.addEventListener('click', e => {
        if (e.target.classList.contains('edit') && e.target.dataset.id) {
            showPenggunaModal(e.target.dataset.id);
        }
    });

    document.getElementById('save-pengguna').addEventListener('click', savePenggunaRole);
    document.getElementById('cancel-pengguna').addEventListener('click', hidePenggunaModal);
    document.getElementById('close-pengguna-modal').addEventListener('click', hidePenggunaModal);

    penggunaContent.dataset.listenersAttached = 'true';
    window.loadPenggunaData();
};