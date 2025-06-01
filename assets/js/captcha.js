// Variabel dan event listener lama yang terkait dengan elemen input captcha di HTML dihapus
// karena captcha sekarang ditangani melalui prompt window.

    // Fungsi untuk menghasilkan CAPTCHA dan menampilkannya di prompt
    function generateCaptchaForPrompt() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let captcha = '';
        for (let i = 0; i < 6; i++) {
            captcha += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        document.getElementById('captchaBox').textContent = captcha;
        return captcha;
    }

const captchaTextBox = document.querySelector(".captch_box input[name='captcha']");
const refreshButton = document.querySelector(".refresh_button");
const captchaInputBox = document.querySelector(".input_field.captch_box.input input[name='user_captcha']");
const message = document.querySelector(".message");
const submitButton = document.querySelector("button[type='submit']"); // Asumsi ini adalah tombol submit form registrasi

let captchaText = null;

const generateCaptcha = () => {
  const randomString = Math.random().toString(36).substring(2, 7);
  const randomStringArray = randomString.split("");
  const changeString = randomStringArray.map((char) => (Math.random() > 0.5 ? char.toUpperCase() : char));
  captchaText = changeString.join("   "); // Spasi untuk tampilan di box
  if(captchaTextBox) captchaTextBox.value = captchaText;
};

const refreshBtnClick = () => {
  generateCaptcha();
  if(captchaInputBox) captchaInputBox.value = "";
  if(message) {
    message.innerText = "Masukkan Captcha";
    message.style.color = "#000"; // Reset warna pesan
  }
};

const captchaKeyUpValidate = () => {
  if(submitButton && captchaInputBox) {
    submitButton.classList.toggle("disabled", !captchaInputBox.value);
  }
  if (!captchaInputBox.value && message) message.classList.remove("active");
};

// Fungsi submitBtnClick tidak diperlukan di sini karena validasi captcha akan dilakukan di handleRegister pada register.php

if(refreshButton) refreshButton.addEventListener("click", refreshBtnClick);
if(captchaInputBox) captchaInputBox.addEventListener("keyup", captchaKeyUpValidate);

generateCaptcha(); // Generate captcha saat script dimuat