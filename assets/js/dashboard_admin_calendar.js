document.addEventListener('DOMContentLoaded', function() {
    const hariItems = document.querySelectorAll('.day-item');
    const infoKelasContainer = document.querySelector('.info-kelas');
    const absenSubtitle = document.querySelector('.absen-subtitle');
    const nidnDosen = document.body.dataset.nidnDosen;
    const tanggalHariIniDisplay = document.querySelector('.tanggal-hari');

    // Function to update the visual active day
    function updateVisualAktifHari(selectedDayElement) {
        const allHariItems = document.querySelectorAll('.hari-container .day-item');

        allHariItems.forEach(item => {
            item.classList.remove('active-day');
            const line = item.querySelector('.hari-text-line');
            if (line) {
                line.style.display = 'none'; // Sembunyikan semua garis
            }
        });

        selectedDayElement.classList.add('active-day');
        const activeLine = selectedDayElement.querySelector('.hari-text-line');
        if (activeLine) {
            activeLine.style.display = 'block'; // Tampilkan garis pada item yang aktif
        } else {
            console.warn('Peringatan: .hari-text-line tidak ditemukan untuk elemen hari aktif:', selectedDayElement);
        }
    }

    // Function to fetch and display schedule data
    async function fetchJadwalUntukHari(nidn, namaHari, tanggalIso) {
        console.log(`Fetching jadwal for NIDN: ${nidn}, Hari: ${namaHari}, Tanggal ISO: ${tanggalIso}`);

        // Update tanggal-hari display
        const dateObj = new Date(tanggalIso);
        const options = { day: '2-digit', month: 'long', year: 'numeric' };
        const formattedDate = dateObj.toLocaleDateString('id-ID', options);
        tanggalHariIniDisplay.textContent = formattedDate;

        infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p>Memuat jadwal...</p>';
        absenSubtitle.textContent = 'Memuat...';

        try {
            const response = await fetch(`../App/Api/get_jadwal_by_hari.php?nidn=${nidn}&nama_hari=${namaHari}`);
            const data = await response.json();

            console.log('API Response:', data);

            if (data.success === true) { // Periksa boolean result.success
                if (data.data && data.data.length > 0) {
                    let jadwalHtml = '<p class="info-title">Informasi Kelas Hari Ini :</p>';
                    data.data.forEach((jadwal, index) => { // Tambahkan index untuk <hr>
                        const jamMulai = jadwal.jam_mulai ? jadwal.jam_mulai.substring(0, 5) : 'N/A';
                        const jamSelesai = jadwal.jam_selesai ? jadwal.jam_selesai.substring(0, 5) : 'N/A';

                        // Menggunakan struktur info-grid per jadwal
                        jadwalHtml += `
                            <div class="info-grid" data-nama-matkul="${jadwal.nama_matkul || ''}" data-id-jadwal="${jadwal.id_jadwal || ''}">
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
                                    <img src="../assets/images/conference.png" alt="Kelas" class="info-icon">
                                    <span>${jadwal.nama_kelas || 'N/A'}</span>
                                </div>
                            </div>
                        `;
                        if (index < data.data.length - 1) { // Tambahkan <hr> jika bukan item terakhir
                            jadwalHtml += '<hr>';
                        }
                    });
                    infoKelasContainer.innerHTML = jadwalHtml;
                    absenSubtitle.textContent = data.data[0].nama_matkul || 'Pilih Mata Kuliah';
                } else {
                    // API sukses, tapi tidak ada data jadwal (result.data kosong)
                    infoKelasContainer.innerHTML = `
                        <img src="../assets/images/browser.png" alt="Tidak ada kelas" style="width:100px; margin:20px auto; display:block;">
                        <p style="text-align:center; font-size:1.2em; margin-top:10px;">${data.message || 'Tidak ada kelas hari ini.'}</p>
                    `;
                    absenSubtitle.textContent = 'Tidak ada jadwal';
                }
            } else {
                // API mengembalikan result.success === false
                console.error('API Error:', data.message);
                infoKelasContainer.innerHTML = `<p class="info-title">Informasi Kelas Hari Ini :</p><p style="text-align:center; margin-top:20px; color:red;">Error memuat jadwal: ${data.message || 'Terjadi kesalahan pada server.'}</p>`;
                absenSubtitle.textContent = 'Error';
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            infoKelasContainer.innerHTML = '<p class="info-title">Informasi Kelas Hari Ini :</p><p>Gagal memuat jadwal. Coba lagi nanti.</p>';
            absenSubtitle.textContent = 'Error Jaringan';
        }
    }

    // Add event listeners to each day item
    hariItems.forEach(item => {
        item.addEventListener('click', function() {
            const namaHari = this.dataset.hari;
            const tanggalIso = this.dataset.tanggalIso;
            updateVisualAktifHari(this);
            fetchJadwalUntukHari(nidnDosen, namaHari, tanggalIso);
        });
        });

        // Add event listener for .info-grid clicks using event delegation
        infoKelasContainer.addEventListener('click', function(event) {
            const clickedJadwalElement = event.target.closest('.info-grid');

            if (clickedJadwalElement && absenSubtitle) {
                const namaMatkulDipilih = clickedJadwalElement.dataset.namaMatkul;
                const idJadwalDipilih = clickedJadwalElement.dataset.idJadwal; // If you added this

                if (namaMatkulDipilih) {
                    absenSubtitle.textContent = namaMatkulDipilih;
                    console.log(`JS LOG: Jadwal diklik. Matkul untuk absen: ${namaMatkulDipilih}`);
                    // If storing idJadwal:
                    console.log(`JS LOG: ID Jadwal yang dipilih untuk absen: ${idJadwalDipilih}`);
                    // Save idJadwalDipilih to a global variable or data attribute on the absen button if needed
                }
            }
        });

        // Initial load: set active day based on current date
        const today = new Date();
        const todayIso = today.toISOString().split('T')[0];
        const activeDayInitially = document.querySelector(`.day-item[data-tanggal-iso="${todayIso}"]`);
        if (activeDayInitially) {
            activeDayInitially.click();
        } else {
            // Fallback to the first day if today's date is not found (e.g., weekend or no data)
            const firstDay = document.querySelector('.day-item');
            if (firstDay) {
                firstDay.click();
            }
        }
    });