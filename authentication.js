$('document').ready(function () {
    $('#login').prop('checked', true);
    $('#submit').prop('value', 'Sign in');
});

$('input[type=radio][name=action]').change(function () {
    if (this.value === 'login') {
        $('#submit').prop('value', 'Sign in');
        $('#repeatPasswordDiv').html('');
        $('#password').prop('title', 'User password');
    } else {
        $('#submit').prop('value', 'Register');
        $('#password').prop('title', "Password must contain a lowercase character and either an uppercase character or a number");
        $('#repeatPasswordDiv').html("<label for=\"repeatPassword\" class=\"repeatPassword\">Repeat Password: </label>\n"
          + "<input id=\"repeatPassword\" type=\"password\" title=\"Passwords must match\" " +
          "name=\"repeatPassword\" class=\"repeatPassword\" placeholder=\"********\" required><br/><br/>");
    }
});

function validate() {
    if ($('input[name=action]:checked').val() === 'register') {
        const email = $('#email').val();
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (!re.test(String(email).toLowerCase())) {
            alert("Please, insert a valid email!");
            return false;
        }
        const pw = $('#password').val();
        if (!(pw.match(/[a-z]/) && (pw.match(/[A-Z]/) || pw.match(/\d/)))) {
            alert("Password must contain a lowercase character and either an uppercase character or a number!");
            return false;
        }
        if ($('#password').val() !== $('#repeatPassword').val()) {
            alert("Passwords must match!");
            return false;
        }
    }
    return true;
}

function homepage() {
    $(location).attr('href', 'index.php');
}