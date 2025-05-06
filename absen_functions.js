function markAttendance(type) {
    // Update status text
    document.getElementById('statusText').textContent = 'Kamu sudah Absen';
    
    // Hide the before attendance section and show the after attendance section
    document.getElementById('beforeAttendance').classList.add('hidden');
    document.getElementById('afterAttendance').classList.remove('hidden');
    
    // Reset all counts
    document.getElementById('absenCount').textContent = '0';
    document.getElementById('izinCount').textContent = '0';
    document.getElementById('sakitCount').textContent = '0';
    document.getElementById('alphaCount').textContent = '0';
    
    // Update the count for the selected attendance type
    document.getElementById(type + 'Count').textContent = '1';
    
    // Optionally, update the status icon
    // document.getElementById('statusIcon').src = "new-icon-url.png";
}