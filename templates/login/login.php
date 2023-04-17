<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */
set_language(substr(str_replace("-", "_", $_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 5));
//set_language('en_US');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        Fidelity Login
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
</head>

<body class="">
<main class="main-content  mt-0">
    <section>
        <div class="page-header min-vh-75">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
                        <div class="card card-plain mt-8">
                            <div class="card-header pb-0 text-left bg-transparent">
                                <h3 class="font-weight-bolder text-info text-gradient"><?php echo __("Welcome back"); ?></h3>
                                <p class="mb-0"><?php echo __("Enter your email and password to sign in"); ?></p>
                            </div>
                            <div class="card-body">
                                <form role="form">
                                    <label>Email</label>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" placeholder="Email" aria-label="Email" id="email" aria-describedby="email-addon">
                                    </div>
                                    <label>Password</label>
                                    <div class="mb-3">
                                        <input type="password" class="form-control" placeholder="Password" aria-label="Password" id="password" aria-describedby="password-addon">
                                    </div>
                                    <div class="text-center">
                                        <button onclick="do_login()" type="button" class="btn bg-gradient-info w-100 mt-4 mb-0">Sign in</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                            <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('../assets/img/curved-images/curved6.jpg')"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="footer py-5">
    <div class="container">
        <div class="row">
            <div class="col-8 mx-auto text-center mt-1">
                <p class="mb-0 text-secondary">
                    Copyright © <script>
                        document.write(new Date().getFullYear())
                    </script> Fidelity System.
                </p>
            </div>
        </div>
    </div>
</footer>

<!--   Core JS Files   -->
<script src="../assets/js/core/jquery.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
<script>
   function do_login(){
       let email = $("#email").val();
       let pwd   = $("#password").val()

       if( email.length === 0 || pwd.length === 0){
           alert("Missing Parameter, check the login");
           return;
       }


       $.post('/admin_login', {email: email, password: pwd}, function(resp){
           if(resp.status === 'error'){
               alert("Sorry, invalid credentials")
               return;
           }

           window.location = '/pages/home'
       });
   }
</script>
<!-- GitHub buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
<script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.7"></script>
</body>

</html>