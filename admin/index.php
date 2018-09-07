<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <?php include("../include/head.php"); ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
        <script type="text/javascript" src="js/loginScript.js"></script>
        <title>Login</title>
        <style>
            body{
                background:#f1f9f9;
            }
            .form-signin{
                max-width: 500px;
                padding: 19px 29px 29px;
                /*margin-top:90px;*/
                background-color: #fff;
                border: 1px solid #e5e5e5;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
            }
            .form-signin .form-signin-heading{
                color:#00A2D1;
            }
            .form-signin input[type="text"],
            .form-signin input[type="password"]{
                font-size: 16px;
                padding: 7px 9px;
            }
            .signin-form, .body-container
            {
                margin-top:110px;
            }
            #check-e{
                color: red;
            }
            .error{
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="signin-form">
            <div class="container h-100">
                <div class="row h-100 justify-content-center align-items-center">
                    <form class="col-12 form-signin" method="post" id="login-form">
                        <h2 class="form-signin-heading">Administrator Log In</h2><hr />
                        <div id="error">
                            <!-- error will be shown here ! -->
                        </div>
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" placeholder="Please insert username" name="username" id="username"  />
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" placeholder="Please insert password" name="password" id="password"  />
                        </div>
                        <hr />
                        <div class="form-group">
                            <button type="submit" class="btn btn-default" name="btn-login" id="btn-login" >
                                <i class="fa fa-sign-in"></i> &nbsp; Sign In 
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
