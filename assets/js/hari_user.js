document.addEventListener('DOMContentLoaded', function() {
const days = [
    { id: 'day-sen', name: 'Senin', dayAbbr: 'Sen' },
    { id: 'day-sel', name: 'Selasa', dayAbbr: 'Sel' },
    { id: 'day-rab', name: 'Rabu', dayAbbr: 'Rab' },
    { id: 'day-kam', name: 'Kamis', dayAbbr: 'Kam' },
    { id: 'day-jum', name: 'Jumat', dayAbbr: 'Jum' },
    { id: 'day-sab', name: 'Sabtu', dayAbbr: 'Sab' },
    { id: 'day-min', name: 'Minggu', dayAbbr: 'Min' }
];

const classInfoContainer = document.getElementById('info-kelas-container');

// Contoh data kelas (gantilah dengan data dari database Anda)
const sampleClasses = [
    {
        subject: 'Praktik Pemrograman Web',
        time: '08:00 - 10:00',
        room: 'Ruang 217',
        lecturer: 'Nama Dosen'
    },
    {
        subject: 'PBO',
        time: '10:00 - 12:00',
        room: 'Ruang 315',
        lecturer: 'Nama Dosen'
    },
    {
        subject: 'MMPPL',
        time: '13:00 - 15:00',
        room: 'Ruang 315',
        lecturer: 'Nama Dosen'
    },
    {
        subject: 'Praktik PBO',
        time: '09:00 - 11:00',
        room: 'Ruang 218',
        lecturer: 'Nama Dosen'
    },
    {
        subject: 'Struktur Data',
        time: '14:00 - 16:00',
        room: 'Ruang 217',
        lecturer: 'Nama Dosen'
    }
];

function displayClassInfo(dayAbbr) {
    classInfoContainer.innerHTML = ''; // Bersihkan konten sebelumnya

    if (dayAbbr === 'Sab' || dayAbbr === 'Min') {
        const noClassDiv = document.createElement('div');
        noClassDiv.classList.add('no-class-today');
        
        const img = document.createElement('img');
        img.src = '../assets/images/browser.png'; // Ganti dengan gambar yang sesuai
        img.alt = 'Tidak ada kelas';
        img.style.width = '100px';
        img.style.margin = '20px auto';
        img.style.display = 'block';

        const p = document.createElement('p');
        p.textContent = 'Tidak ada kelas hari ini.';
        p.style.textAlign = 'center';
        p.style.fontSize = '1.2em';
        p.style.marginTop = '10px';

        noClassDiv.appendChild(img);
        noClassDiv.appendChild(p);
        classInfoContainer.appendChild(noClassDiv);
    } else {
        const title = document.createElement('p');
        title.classList.add('info-title');
        title.textContent = 'Informasi Kelas Hari Ini :';
        classInfoContainer.appendChild(title);

        const numberOfClasses = Math.floor(Math.random() * 2) + 1; // 1 sampai 2 kelas (maksimal 3, jadi 1 atau 2)
        const displayedClasses = [];
        for (let i = 0; i < numberOfClasses; i++) {
            let randomClass;
            do {
                randomClass = sampleClasses[Math.floor(Math.random() * sampleClasses.length)];
            } while (displayedClasses.includes(randomClass) && displayedClasses.length < sampleClasses.length);
            displayedClasses.push(randomClass);
            
            const infoGrid = document.createElement('div');
            infoGrid.classList.add('info-grid');

            infoGrid.innerHTML = `
                <div class="info-item">
                    <img src="../assets/images/teachings.png" alt="icon" class="info-icon">
                    <span>${randomClass.subject}</span>
                </div>
                <div class="info-item">
                    <img src="../assets/images/clock.png" alt="icon" class="info-icon">
                    <span>${randomClass.time}</span>
                </div>
                <div class="info-item">
                    <img src="../assets/images/classroom.png" alt="icon" class="info-icon">
                    <span>${randomClass.room}</span>
                </div>
                <div class="info-item">
                    <img src="../assets/images/conference.png" alt="icon" class="info-icon">
                    <span>${randomClass.lecturer}</span>
                </div>
            `;
            infoGrid.style.cursor = 'pointer'; // Make it look clickable
            infoGrid.addEventListener('click', function() {
                updateAttendanceStatusForClass(randomClass.subject);
            });
            classInfoContainer.appendChild(infoGrid);
            if (i < numberOfClasses - 1) { // Add a divider if not the last class
                const hr = document.createElement('hr');
                hr.style.margin = '10px 0';
                classInfoContainer.appendChild(hr);
            }
        }
    }
    // Adjust height of jadwal-container
    const jadwalContainer = document.querySelector('.jadwal-container');
    if (jadwalContainer) {
        // Reset height to auto to allow content to define it, then set min-height
        jadwalContainer.style.height = 'auto'; 
        const newHeight = classInfoContainer.offsetHeight + document.querySelector('.hari-container').offsetHeight + document.querySelector('.jadwal-header').offsetHeight + 60; // 60 for padding/margins
        jadwalContainer.style.minHeight = newHeight + 'px';
    }
}

days.forEach(day => {
    const dayElement = document.getElementById(day.id);
    if (dayElement) {
        dayElement.addEventListener('click', function() {
            days.forEach(d => {
                const el = document.getElementById(d.id);
                if (el) el.classList.remove('active-day');
            });
            this.classList.add('active-day');
            displayClassInfo(day.dayAbbr);
        });
    }
});

// Tampilkan info untuk hari default (Senin)
const defaultDayElement = document.getElementById('day-sen');
if (defaultDayElement) {
        defaultDayElement.classList.add('active-day');
}
displayClassInfo('Sen');

function updateAttendanceStatusForClass(className) {
    const absenSubtitle = document.querySelector('.absen-subtitle');
    const statusText = document.getElementById('statusText');
    const beforeAttendanceDiv = document.getElementById('beforeAttendance');
    const afterAttendanceDiv = document.getElementById('afterAttendance');

    if (absenSubtitle) {
        absenSubtitle.textContent = className;
    }
    if (statusText) {
        statusText.textContent = 'Kamu Belum Absen'; // Reset status
    }
    // Reset to initial state (show attendance options, hide stats)
    if (beforeAttendanceDiv) {
        beforeAttendanceDiv.classList.remove('hidden');
    }
    if (afterAttendanceDiv) {
        afterAttendanceDiv.classList.add('hidden');
    }
    // Reset attendance counts if needed (assuming they are global or accessible)
    // For now, this example doesn't reset counts as they are handled by markAttendance
}
});