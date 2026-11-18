(function () {
  document.querySelectorAll('[data-password-field]').forEach(function (field) {
    var input = field.querySelector('[data-password-input]');
    var button = field.querySelector('[data-password-toggle]');
    if (!input || !button) {
      return;
    }

    var eye = button.querySelector('[data-eye]');
    var eyeOff = button.querySelector('[data-eye-off]');
    button.hidden = false;

    button.addEventListener('click', function () {
      var reveal = input.type === 'password';
      input.type = reveal ? 'text' : 'password';
      button.setAttribute('aria-label', reveal ? 'Sembunyikan password' : 'Tampilkan password');
      button.setAttribute('aria-pressed', reveal ? 'true' : 'false');
      button.title = reveal ? 'Sembunyikan password' : 'Tampilkan password';

      if (eye && eyeOff) {
        eye.hidden = reveal;
        eyeOff.hidden = !reveal;
      }
    });
  });
})();
