
$('document').ready(function () {
    /*validation */
    /*$('#btn-login').val("submit");
    $('#btn-login').on('click', function (e) {
        e.preventDefault();
        var username = $('#username').val();
        var password = $('#password').val();
        
        if (username !== '' && password !== '') {
            $('#empty_username').html("");
            $('#empty_password').html("");
            submitForm();

        } else {
            if (username === '') {
                $('#empty_username').html("Please enter your username");
                $('#username').on('keyup', function () {
                    var usernameInput = $('#username').val();
                    if (usernameInput.length > 0) {
                        $('#empty_username').html("");
                    }
                });
            }
            if (password === '') {
                $('#empty_password').html("Please enter your password");
                $('#password').on('keyup', function () {
                    var passwordInput = $('#password').val();
                    if (passwordInput.length > 0) {
                        $('#empty_password').html("");
                    }
                });
            }
        }
    });*/

    $('#login-form').validate({
        rules: {
            password: {required: true},
            username: {required: true}
        },
        messages: {
            password: {required: "Please enter your password"},
            username: {required: "Please enter your username"}
        },
        submitHandler: submitForm
    });




    /*login submit*/
    function submitForm() {
        var data = $('#login-form').serialize();
        console.log(data);
        $.ajax({
            type: 'POST',
            url: 'loginProcess.php',
            data: data,
            beforeSend: function () {
                $('#error').fadeOut();
                $('#btn-login').html('<i class="fa fa-exchange"></i> &nbsp; sending ...');
            },
            success: function (response) {
                if (response === "OK") {
                    $('#btn-login').html('<img src="../resources/loader.gif" / width="31" height="31"> &nbsp; Signing In ...');
                    setTimeout('window.location.href = "home.php";', 2000);
                } else {
                    $('#error').fadeIn(1000, function () {
                        $('#error').html('<div class="alert alert-danger alert-dismissible"> \n\
                        <button type="button" class="close" data-dismiss="alert">&times;</button> \n\
                        <i class="fa fa-info-circle"></i> &nbsp; ' + response + '! </div>');
                        $('#btn-login').html('<i class="fa fa-sign-in"></i> &nbsp; Sign In');
                    });
                }
            }
        });
        return false;
    }
});
