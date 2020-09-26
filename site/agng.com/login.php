<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: welcome.php");
  exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, email, password FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;                            
                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if email doesn't exist
                    $email_err = "No account found with that email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>AGNG - Login</title>

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
<link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
        <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.css">
        <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="fonts/Linearicons/css/linearicons.css">
    <!-- Font special for pages-->

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/main.css" rel="stylesheet" media="all">
<!--
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
-->
    
    
    <script src="jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#state').on('change',function(){
                var stateID = $(this).val();
                if(stateID){
                    $.ajax({
                        type:'POST',
                        url:'ajaxData.php',
                        data:'state_id='+stateID,
                        success:function(html){
                            $('#district').html(html).show();
                            $('#section').html('<option value="">Select district first</option>');
                            $('#church').html('<option value="">Select section first</option>');
                        }
                    }); 
                }else{
                    $('#district').html('<option value="">Select state first</option>');
                    $('#section').html('<option value="">Select district first</option>'); 
                }
            });
            
            $('#district').on('change',function(){
                var districtID = $(this).val();
                if(districtID){
                    $.ajax({
                        type:'POST',
                        url:'ajaxData.php',
                        data:'district_id='+districtID,
                        success:function(html){
                            $('#section').html(html).show();
                            $('#church').html('<option value="">Select section first</option>');
                        }
                    }); 
                }else{
                    $('#section').html('<option value="">Select district first</option>');
                    $('#church').html('<option value="">Select section first</option>');
                }
            });
            
            $('#section').on('change',function(){
                var sectionID = $(this).val();
                if(sectionID){
                    $.ajax({
                        type:'POST',
                        url:'ajaxData.php',
                        data:'section_id='+sectionID,
                        success:function(html){
                            $('#church').html(html).show();
                        }
                    }); 
                }else{
                    $('#church').html('<option value="">Select section first</option>'); 
                }
            });
        });
    </script>
</head>

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">
    
    <div id="overlayer"></div>
        <div class="loader">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    
        <div class="page-wrapper bg-red login-top login-bottom font-robo">
        <div class="wrapper wrapper--w960">
            <div class="card card-2">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="title">MEMBER LOGIN</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        
                            
                                <div class="input-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                                    <input type="email" placeholder="Email" name="email" class="input--style-2" value="<?php echo $email; ?>" autocomplete="off" onfocus="this.placeholder=''" onblur="this.placeholder='Email'">
                                </div>
                                <span class="help-block"><?php echo $email_err; ?></span>
                            
                            
                                <div class="input-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                    <input id="password-visible" type="password" placeholder="Password" name="password" class="input--style-2" value="<?php echo $password; ?>" autocomplete="off" onfocus="this.placeholder=''" onblur="this.placeholder='Password'">
                                    <span class="focus-input100"></span>
                                    <span toggle="#password" class="fa fa-eye-slash field-icon toggle-password input-icon" onclick="password()" style="font-size: 14px; margin-top: 17.5px;"></span>
                                </div>
                                <span class="help-block"><?php echo $password_err; ?></span>
                            
                        
                        
<!--
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group <?php echo (!empty($gender_err)) ? 'has-error' : ''; ?>">
                                    <div class="rs-select2 js-select-simple select--no-search">
                                        <select class="input--style-2" name="gender" required>
                                            <option disabled="disabled" selected="selected" value="">Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <div class="select-dropdown"></div>
                                        <span class="help-block"><?php echo $gender_err; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-2 js-datepicker" type="text" placeholder="Birthdate" name="birthday">
                                    <i class="zmdi zmdi-calendar-note input-icon js-btn-calendar"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                                    <input type="email" placeholder="Email" name="email" class="input--style-2" value="<?php echo $email; ?>" autocomplete="off" onfocus="this.placeholder=''" onblur="this.placeholder='Email'">
                                </div>
                                <span class="help-block"><?php echo $email_err; ?></span>
                            </div>
                            <div class="col-2">
                                <div class="input-group <?php echo (!empty($mobile_err)) ? 'has-error' : ''; ?>">
                                    <input type="tel" placeholder="Mobile" name="mobile" class="input--style-2"   value="<?php echo $mobile; ?>" autocomplete="off" onfocus="this.placeholder=''" onblur="this.placeholder='Mobile'"> 
                                </div>
                                <span class="help-block"><?php echo $mobile_err; ?></span>
                            </div>
                        </div>
                        
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                    <input id="password-visible" type="password" placeholder="Password" name="password" class="input--style-2" value="<?php echo $password; ?>" autocomplete="off" onfocus="this.placeholder=''" onblur="this.placeholder='Password'">
                                    <span class="focus-input100"></span>
                                    <span toggle="#password" class="fa fa-eye-slash field-icon toggle-password input-icon" onclick="password()" style="font-size: 14px; margin-top: 17.5px;"></span>
                                </div>
                                <span class="help-block"><?php echo $password_err; ?></span>
                            </div>
                            <div class="col-2">
                                <div class="input-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                                    <input id="confirm-password-visible" type="password" placeholder="Confirm Password" name="confirm_password" class="input--style-2" value="<?php echo $confirm_password; ?>" autocomplete="off" onfocus="this.placeholder=''" onblur="this.placeholder='Confirm Password'">
                                    <span class="focus-input100"></span>
                                    <span toggle="#password" class="fa fa-eye-slash field-icon toggle-password input-icon" onclick="confirmPassword()" style="font-size: 14px;     margin-top: 17.5px;"></span>
                                </div>
                                <span class="help-block"><?php echo $confirm_password_err; ?></span>
                            </div>
                        </div>

                        

                        <div class="input-group">
                            <input class="input--style-2" type="text" placeholder="Name" name="name">
                        </div>
                        
                        <div class="input-group">
                            <div class="rs-select2 js-select-simple select--no-search">
                                <select name="class">
                                    <option disabled="disabled" selected="selected">Class</option>
                                    <option>Class 1</option>
                                    <option>Class 2</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                    <option>Class 3</option>
                                </select>
                                <div class="select-dropdown"></div>
                            </div>
                        </div>
                        
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-2" type="text" placeholder="Registration Code" name="res_code">
                                </div>
                            </div>
                        </div>
-->
<!--
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="p-t-30">
                                <p class="btn btn--radius btn--green"><a href="register.php">Register</a></p>
                                </div>
                            </div>
                            <div class="col-2">
                        <div class="p-t-30">
                           <button class="btn btn--radius btn--green" type="submit" value="submit">Submit</button>
                        </div>
                            </div>
                        </div>
-->
                        
                        <div class="p-t-30">
                            
                            <button class="btn btn--radius btn--green" type="submit" value="submit">Submit</button>
                        </div>
                    </form>
                    <p style="margin-top: 50px; text-align: center;"><a style=" text-decoration: none; color: #666666;" href="register.php">Not a Member? Register</a></p>
                </div>
            </div>
        </div>
    </div>
                
    

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="js/global.js"></script>
    
    
    
    <!-- START LOGIN IMAGE -->
        <!--===============================================================================================-->
        <script src="vendor/tilt/tilt.jquery.min.js"></script>
        <script >
            $('.js-tilt').tilt({
                scale: 1.1
            })
        </script>
        <!--===============================================================================================-->
        <!-- END LOGIN IMAGE -->
    
    
    
        <!-- START TAB -->
        <!--===============================================================================================-->
        <script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the crurrent tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "<span class='lnr lnr-arrow-right'></span>";
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}
</script>
        <!--===============================================================================================-->
        <!-- END TAB -->
    
        <!-- START SHOW PASSWORD - PASSWORD -->
        <!--===============================================================================================-->
        <script>
            function password() {
                var x = document.getElementById("password-visible");
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }
        </script>
        <!--===============================================================================================-->
        <!-- END SHOW PASSWORD -->
        
        
        
        <!-- START SHOW PASSWORD - CONFIRM PASSWORD -->
        <!--===============================================================================================-->
        <script>
            function confirmPassword() {
                var x = document.getElementById("confirm-password-visible");
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }
        </script>
        <!--===============================================================================================-->
        <!-- END SHOW PASSWORD -->
        
        
        
        <!-- START TOGGLE EYE -->
        <!--===============================================================================================-->
        <script>
            (function($) {
                $(".toggle-password").click(function() {
                    $(this).toggleClass("fa fa-eye fa fa-eye-slash");
                    var input = $($(this).attr("toggle"));
                    if (input.attr("type") == "password") {
                        input.attr("type", "text");
                    } else {
                        input.attr("type", "password");
                    }
                });
            })(jQuery);
        </script>
        <!--===============================================================================================-->
        <!-- END TOGGLE EYE -->

        

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->