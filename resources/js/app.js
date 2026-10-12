
import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;

Alpine.start();

// Date picker: init semua input[data-datepicker] setelah DOM siap
// max="today" → tidak bisa pilih tanggal masa depan (mis. tanggal lahir)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[data-datepicker]').forEach(function (input) {
        flatpickr(input, {
            locale: Indonesian,
            dateFormat: 'Y-m-d',
            allowInput: true,
            maxDate: input.hasAttribute('data-max-today') ? 'today' : undefined,
        });
    });
});
