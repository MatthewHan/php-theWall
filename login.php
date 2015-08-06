<?php
session_start();
require("connection.php");

if(isset($_SESSION['logged_user']))
{
  Header('Location: index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Login/Sign Up</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <link href="signin.css" rel="stylesheet">
    <script type = "text/javascript">
    $(document).ready(function(){
      $(function() {
          $('#login-form-link').click(function(e) {
          $("#login-form").delay(100).fadeIn(100);
          $("#register-form").fadeOut(100);
          $('#register-form-link').removeClass('active');
          $(this).addClass('active');
          e.preventDefault();
        });
        $('#register-form-link').click(function(e) {
          $("#register-form").delay(100).fadeIn(100);
          $("#login-form").fadeOut(100);
          $('#login-form-link').removeClass('active');
          $(this).addClass('active');
          e.preventDefault();
        });

      });
    })

    </script>

  </head>
  <body>
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-login">
          <div class="panel-heading">
            <div class="row">
              <div class="col-xs-6">
                <a href="#" class="active" id="login-form-link">Login</a>
              </div>
              <div class="col-xs-6">
                <a href="#" id="register-form-link">Register</a>
              </div>
            </div>
            <hr>
          </div>
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-12">
                <form id="login-form" action="process.php" method="post" role="form" style="display: <?php if(isset($_SESSION['register_errors'])){echo 'none';} else {echo 'block';} ?>;">
                  <?php if(isset($_SESSION['login_errors']['email'])){?> 
                  <span class="help-block alert alert-danger"><?= $_SESSION['login_errors']['email'] ?></span> 
                  <?php
                  } ?>
                  <?php if(isset($_SESSION['success']['register'])){?> 
                  <span class="help-block alert alert-success"><?= $_SESSION['success']['register'] ?></span> 
                  <?php
                  unset($_SESSION['success']);
                  } ?>
                  <div class="form-group">
                    <input type="text" name="email" id="email" tabindex="1" class="form-control" placeholder="Email Address" value="">
                  </div>
                  <div class="form-group">
                    <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-sm-6 col-sm-offset-3">
                        <button name="login_submit" id="login_submit" tabindex="4" class="form-control btn btn-login" value="login">Log In</button>
                      </div>
                    </div>
                  </div>
                </form>
                <form id="register-form" action="process.php" method="post" role="form" style="display: <?php if(isset($_SESSION['register_errors'])){echo 'block';} else {echo 'none';} ?>;">
                  <div class="form-group">
                    <input type="text" name="first_name" id="first_name" tabindex="1" class="form-control" placeholder="First Name" value="<?php if(isset($_SESSION['register']['first_name'])){echo $_SESSION['register']['first_name'];} ?>" >
                    <?php if(isset($_SESSION['register_errors']['first_name'])){?> 
                    <span class="help-block alert alert-danger"><?= $_SESSION['register_errors']['first_name'] ?></span> 
                    <?php
                    } ?>
                  </div>
                  <div class="form-group">
                    <input type="text" name="last_name" id="last_name" tabindex="1" class="form-control" placeholder="Last Name" value="<?php if(isset($_SESSION['register']['last_name'])){echo $_SESSION['register']['last_name'];} ?>" >
                    <?php if(isset($_SESSION['register_errors']['last_name'])){?> 
                    <span class="help-block alert alert-danger"><?= $_SESSION['register_errors']['last_name'] ?></span> 
                    <?php
                    } ?>
                  </div>
                  <div class="form-group">
                    <input type="email" name="email" id="email" tabindex="1" class="form-control" placeholder="Email Address" value="<?php if(isset($_SESSION['register']['email'])){echo $_SESSION['register']['email'];} ?>" >
                    <?php if(isset($_SESSION['register_errors']['email'])){?> 
                    <span class="help-block alert alert-danger"><?= $_SESSION['register_errors']['email'] ?></span> 
                    <?php
                    } ?>
                  </div>
                  <div class="form-group">
                    <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
                    <?php if(isset($_SESSION['register_errors']['password'])){?> 
                    <span class="help-block alert alert-danger"><?= $_SESSION['register_errors']['password'] ?></span> 
                    <?php
                    } ?>
                  </div>
                  <div class="form-group">
                    <input type="password" name="confirm_password" id="confirm_password" tabindex="2" class="form-control" placeholder="Confirm Password">
                    <?php if(isset($_SESSION['register_errors']['confirm_password'])){?> 
                    <span class="help-block alert alert-danger"><?= $_SESSION['register_errors']['confirm_password'] ?></span> 
                    <?php
                    } ?>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-sm-6 col-sm-offset-3">
                        <button name="register_submit" id="register_submit" tabindex="4" class="form-control btn btn-register" value="register">Register Now</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </body>
</html>

<?php
if(isset($_SESSION['register_errors']))
{
  unset($_SESSION['register_errors']);
}
if(isset($_SESSION['login_errors']))
{
  unset($_SESSION['login_errors']);
}
?>
