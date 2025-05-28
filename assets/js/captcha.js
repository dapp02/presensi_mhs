// Variabel dan event listener lama yang terkait dengan elemen input captcha di HTML dihapus
// karena captcha sekarang ditangani melalui prompt window.

function generateCaptchaForPrompt() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let captcha = '';
    for (let i = 0; i < 6; i++) {
        captcha += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return captcha;
}

// Kode di bawah ini tidak lagi diperlukan karena elemennya sudah dihapus dari login.php
// dan logika validasi ada di login.php

// const captchaTextBox = document.querySelector(".captch_box input");
// const refreshButton = document.querySelector(".refresh_button");
// const captchaInputBox = document.querySelector(".captch_input input");
// const message = document.querySelector(".message");
// const submitButton = document.querySelector(".button"); // Asumsi ini adalah tombol login

// let captchaText = null;

// const generateCaptcha = () => {
//   const randomString = Math.random().toString(36).substring(2, 7);
//   const randomStringArray = randomString.split("");
//   const changeString = randomStringArray.map((char) => (Math.random() > 0.5 ? char.toUpperCase() : char));
//   captchaText = changeString.join("   "); // Spasi untuk tampilan di box
//   if(captchaTextBox) captchaTextBox.value = captchaText;
// };

// const refreshBtnClick = () => {
//   generateCaptcha();
//   if(captchaInputBox) captchaInputBox.value = "";
  // captchaKeyUpValidate(); // Fungsi ini mungkin perlu disesuaikan atau dihapus
// };

// const captchaKeyUpValidate = () => {
  // Validasi saat mengetik mungkin tidak relevan lagi untuk prompt
  // if(submitButton && captchaInputBox) {
  //   submitButton.classList.toggle("disabled", !captchaInputBox.value);
  // }
  // if (!captchaInputBox.value && message) message.classList.remove("active");
// };

// const submitBtnClick = () => { // Ini seharusnya ditangani oleh form submit di login.php
//   if (!captchaText || !captchaInputBox) return;
//   let cleanCaptchaText = captchaText.split("").filter((char) => char !== " ").join("");
//   if(message) message.classList.add("active");

//   if (captchaInputBox.value === cleanCaptchaText) {
//     if(message) {
//         message.innerText = "Captcha yang di masukkan benar";
//         message.style.color = "#826afb";
//     }
//     // Logika setelah captcha benar, misal enable tombol login atau submit form
//     // window.location.href = "dashboard_admin.php"; // Contoh, sebaiknya ditangani di login.php
//   } else {
//     if(message) {
//         message.innerText = "Captcha yang dimasukkan salah!";
//         message.style.color = "#FF2525";
//     }
//   }
// };

// if(refreshButton) refreshButton.addEventListener("click", refreshBtnClick);
// if(captchaInputBox) captchaInputBox.addEventListener("keyup", captchaKeyUpValidate);
// // if(submitButton) submitButton.addEventListener("click", submitBtnClick); // Tombol submit form login yang akan menangani ini

// generateCaptcha(); // Generate captcha saat script dimuat jika masih ada elemen yang membutuhkan