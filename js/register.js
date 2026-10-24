document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registration-form');
  const fullName = document.getElementById('fullName');
  const email = document.getElementById('email');
  const mobile = document.getElementById('mobile');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirmPassword');
  const course = document.getElementById('course');
  const year = document.getElementById('year');
  const terms = document.getElementById('terms');
  const successAlert = document.getElementById('success-alert');

  // Regex Patterns
  const nameRegex = /^[A-Za-z\s]{3,40}$/;
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  const mobileRegex = /^[6-9]\d{9}$/;
  const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/;

  function setValid(input, errorElement) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    errorElement.style.display = 'none';
  }

  function setInvalid(input, errorElement) {
    input.classList.remove('is-valid');
    input.classList.add('is-invalid');
    errorElement.style.display = 'block';
  }

  // Live Password Strength Indicator
  password.addEventListener('input', () => {
    const val = password.value;
    const bar = document.getElementById('password-strength-bar');
    const text = document.getElementById('password-strength-text');
    let strength = 0;

    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/\d/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    if (val.length === 0) {
      bar.style.width = '0%';
      text.textContent = '';
    } else if (strength <= 1) {
      bar.style.width = '25%';
      bar.style.backgroundColor = '#dc3545';
      text.textContent = 'Strength: Weak';
      text.style.color = '#dc3545';
    } else if (strength === 2 || strength === 3) {
      bar.style.width = '65%';
      bar.style.backgroundColor = '#ffc107';
      text.textContent = 'Strength: Moderate';
      text.style.color = '#d39e00';
    } else {
      bar.style.width = '100%';
      bar.style.backgroundColor = '#198754';
      text.textContent = 'Strength: Strong';
      text.style.color = '#198754';
    }
  });

  // Form Validation Handler
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    let isValid = true;

    // Validate Full Name
    if (!nameRegex.test(fullName.value.trim())) {
      setInvalid(fullName, document.getElementById('name-error'));
      isValid = false;
    } else {
      setValid(fullName, document.getElementById('name-error'));
    }

    // Validate Email
    if (!emailRegex.test(email.value.trim())) {
      setInvalid(email, document.getElementById('email-error'));
      isValid = false;
    } else {
      setValid(email, document.getElementById('email-error'));
    }

    // Validate Mobile
    if (!mobileRegex.test(mobile.value.trim())) {
      setInvalid(mobile, document.getElementById('mobile-error'));
      isValid = false;
    } else {
      setValid(mobile, document.getElementById('mobile-error'));
    }

    // Validate Password
    if (!passwordRegex.test(password.value)) {
      setInvalid(password, document.getElementById('password-error'));
      isValid = false;
    } else {
      setValid(password, document.getElementById('password-error'));
    }

    // Validate Confirm Password
    if (confirmPassword.value === '' || confirmPassword.value !== password.value) {
      setInvalid(confirmPassword, document.getElementById('confirm-password-error'));
      isValid = false;
    } else {
      setValid(confirmPassword, document.getElementById('confirm-password-error'));
    }

    // Validate Course
    if (course.value === '') {
      setInvalid(course, document.getElementById('course-error'));
      isValid = false;
    } else {
      setValid(course, document.getElementById('course-error'));
    }

    // Validate Year
    if (year.value === '') {
      setInvalid(year, document.getElementById('year-error'));
      isValid = false;
    } else {
      setValid(year, document.getElementById('year-error'));
    }

    // Validate Gender
    const genderChecked = document.querySelector('input[name="gender"]:checked');
    const genderError = document.getElementById('gender-error');
    if (!genderChecked) {
      genderError.style.display = 'block';
      isValid = false;
    } else {
      genderError.style.display = 'none';
    }

    // Validate Terms Checkbox
    const termsError = document.getElementById('terms-error');
    if (!terms.checked) {
      terms.classList.add('is-invalid');
      termsError.style.display = 'block';
      isValid = false;
    } else {
      terms.classList.remove('is-invalid');
      termsError.style.display = 'none';
    }

    if (isValid) {
      successAlert.classList.remove('d-none');
      successAlert.scrollIntoView({ behavior: 'smooth' });
    } else {
      successAlert.classList.add('d-none');
    }
  });
});