document.addEventListener('DOMContentLoaded', function() {
    const bodyElement = document.body;
    const NIM_MAHASISWA_LOGIN = bodyElement.dataset.nimMahasiswa;

    const hariItemsContainer = document.querySelector('.hari-container');
    const infoKelasContainer = document.getElementById('info-kelas-container');
    const absenSubtitle = document.querySelector('.absen-container .absen-subtitle');

    // LOGGING AWAL UNTUK VERIFIKASI
    console.log("JS LOG (User Initial): NIM_MAHASISWA_LOGIN dari DOM:", NIM_MAHASISWA_LOGIN);
    if (!NIM_MAHASISWA_LOGIN) {
        console.error('JS FATAL (User): NIM Mahasiswa TIDAK DITEMUKAN di atribut data DOM. Periksa pages/dashboard_user.php.');
    }

    if (!hariItemsContainer || !infoKelasContainer || !absenSubtitle) {
        console.error('JS ERROR (User): Elemen UI penting (hari-container, info-kelas-container, atau absen-subtitle) tidak ditemukan.');
        return;
    }

    function updateUserVisualAktifHari(clickedItemElement) {
        hariItemsContainer.querySelectorAll('.day-item').forEach(i => {
            i.classList.remove('active-day');
            const line = i.querySelector('.hari-text-line');
            if (line) line.style.display = 'none';
        });
        clickedItemElement.classList.add('active-day');
        let activeLine = clickedItemElement.querySelector('.hari-text-line');
        if (!activeLine) {
            activeLine = document.createElement('div');
            activeLine.className = 'hari-text-line';
            const tanggalSpan = clickedItemElement.querySelector('.tanggal');
            if (tanggalSpan) clickedItemElement.insertBefore(activeLine, tanggalSpan.nextSibling);
            else clickedItemElement.appendChild(activeLine);
        }
        activeLine.style.display = 'block';
    }

    async function fetchJadwalMahasiswaUntukHari(nim, namaHari) {
        if (!nim || !namaHari) {
            console.warn(`JS WARN (User): Panggilan fetchJadwal dibatalkan, NIM (${nim}) atau NamaHari (${namaHari}) tidak valid.`);
            infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px; color:orange;">Data tidak lengkap untuk memuat jadwal.</p>';
            absenSubtitle.textContent = 'Data tidak lengkap';
            return;
        }

        console.log(`JS LOG (User): Memulai fetch jadwal untuk NIM=${nim}, Hari=${namaHari}`);
        const apiUrl = `../App/Api/get_jadwal_mahasiswa_by_hari.php?nim=${encodeURIComponent(nim)}&nama_hari=${encodeURIComponent(namaHari)}`;

        infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px;">Memuat jadwal untuk hari ' + namaHari + '...</p>';
        absenSubtitle.textContent = 'Memuat...';

        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Gagal mengambil data. Status: ${response.status}. Pesan Server: ${errorText || response.statusText}`);
            }
            const result = await response.json();
            console.log('JS LOG (User): Respons API diterima:', result);

            infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p>';

            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach((jadwal, index) => {
                    const jamMulai = jadwal.jam_mulai ? jadwal.jam_mulai.substring(0, 5) : 'N/A';
                    const jamSelesai = jadwal.jam_selesai ? jadwal.jam_selesai.substring(0, 5) : 'N/A';

                    const jadwalWrapperDiv = document.createElement('div');
                    jadwalWrapperDiv.className = 'info-grid';
                    jadwalWrapperDiv.innerHTML = `
                        <div class="info-item">
                            <img src="../assets/images/teachings.png" alt="Mata Kuliah" class="info-icon">
                            <span>${jadwal.nama_matkul || 'N/A'}</span>
                        </div>
                        <div class="info-item">
                            <img src="../assets/images/clock.png" alt="Jam" class="info-icon">
                            <span>${jamMulai} - ${jamSelesai}</span>
                        </div>
                        <div class="info-item">
                            <img src="../assets/images/classroom.png" alt="Ruangan" class="info-icon">
                            <span>${jadwal.ruangan || 'N/A'}</span>
                        </div>
                        <div class="info-item">
                            <img src="../assets/images/conference.png" alt="Dosen" class="info-icon">
                            <span>${htmlspecialchars(jadwal.nama_dosen || 'N/A')}</span>
                        </div>
                    `;
                    infoKelasContainer.appendChild(jadwalWrapperDiv);
                    if (index < result.data.length - 1) {
                        const hr = document.createElement('hr');
                        infoKelasContainer.appendChild(hr);
                    }
                });
                absenSubtitle.textContent = result.data[0].nama_matkul || 'Pilih Mata Kuliah';
            } else {
                const message = 'Tidak ada jadwal kuliah ditemukan untuk hari ini.';
                infoKelasContainer.innerHTML = `
                    <img src="../assets/images/browser.png" alt="Tidak ada kelas" style="width:100px; margin:20px auto; display:block;">
                    <p style="text-align:center; font-size:1.2em; margin-top:10px;">${message}</p>
                `;
                absenSubtitle.textContent = 'Tidak ada jadwal';
            }
        } catch (error) {
            console.error('JS ERROR (User): Gagal fetch atau proses jadwal:', error);
            infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p>';
            infoKelasContainer.innerHTML += `<p style="text-align:center; margin-top:20px; color:red;">Gagal memuat jadwal. ${error.message}</p>`;
            absenSubtitle.textContent = 'Error';
        }
    }

    // Helper function for HTML escaping
    function htmlspecialchars(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/&/g, '&amp;')
                   .replace(/</g, '&lt;')
                   .replace(/>/g, '&gt;')
                   .replace(/"/g, '&quot;')
                   .replace(/'/g, '&#039;');
    }

    // --- Event Listener untuk Klik Hari ---
    hariItemsContainer.addEventListener('click', function(event) {
        const clickedItem = event.target.closest('.day-item');
        if (!clickedItem) return;

        updateUserVisualAktifHari(clickedItem);

        const namaHariDipilih = clickedItem.dataset.hari;
        const tanggalIsoDipilih = clickedItem.dataset.tanggalIso; // Not used in fetch, but good for logging

        console.log(`JS LOG (User Kalender): Hari diklik: ${namaHariDipilih} (ISO: ${tanggalIsoDipilih})`);
        console.log(`JS LOG (User Kalender): NIM Mahasiswa untuk fetch: ${NIM_MAHASISWA_LOGIN}`);

        fetchJadwalMahasiswaUntukHari(NIM_MAHASISWA_LOGIN, namaHariDipilih);
    });

    // --- Pemanggilan Awal untuk Hari Ini Saat Halaman Dimuat ---
    const initialActiveDay = hariItemsContainer.querySelector('.day-item.active-day');
    if (initialActiveDay) {
        const initialNamaHari = initialActiveDay.dataset.hari;
        // LOGGING SEBELUM FETCH AWAL
        console.log(`JS LOG (User Initial Load): Mau fetch untuk Hari='${initialNamaHari}', NIM='${NIM_MAHASISWA_LOGIN}'`);

        if (NIM_MAHASISWA_LOGIN && initialNamaHari) { // Periksa lagi di sini
            fetchJadwalMahasiswaUntukHari(NIM_MAHASISWA_LOGIN, initialNamaHari);
        } else {
            console.error("JS ERROR (User Initial Load): FETCH DIBATALKAN. NIM atau Hari awal tidak valid.", {nim: NIM_MAHASISWA_LOGIN, hari: initialNamaHari});
            if (infoKelasContainer) infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px; color:red;">Gagal memuat jadwal awal (NIM/Hari tidak terbaca dari DOM).</p>';
            if (absenSubtitle) absenSubtitle.textContent = 'Error Inisialisasi';
        }
    } else {
        console.warn('JS WARN (User Initial Load): Tidak ada .day-item.active-day ditemukan.');
        if (infoKelasContainer) infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px;">Tidak ada hari aktif awal yang ditemukan di kalender.</p>';
        if (absenSubtitle) absenSubtitle.textContent = 'Tidak ada jadwal';
    }
});