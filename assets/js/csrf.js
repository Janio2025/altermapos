function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) {
        return match[2];
    }
}

function setCsrfTokenInAllForms(csrfTokenName) {
    $('input[name="' + csrfTokenName + '"]').remove();
    var forms = document.querySelectorAll("form");
    forms.forEach(function (form) {
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = csrfTokenName;
        csrfInput.value = $('meta[name="csrf-token-name"]').attr('content');
        form.appendChild(csrfInput);
    });
}

$(document).ready(function () {
    // Add CSRF token input to each form and ajax requests
    var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content');

    setCsrfTokenInAllForms(csrfTokenName);

    $.ajaxSetup({
        beforeSend: function (jqXHR, settings) {
            if (settings.type === 'POST') {
                if (typeof settings.data === 'object') {
                    settings.data[csrfTokenName] = $('input[name="' + csrfTokenName + '"]').val();
                } else {
                    settings.data += '&' + $.param({
                        [csrfTokenName]: $('input[name="' + csrfTokenName + '"]').val()
                    });
                }
            }
            return true;
        },
        complete: function () {
            setCsrfTokenInAllForms(csrfTokenName);
        }
    });
});
