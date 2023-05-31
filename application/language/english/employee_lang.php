<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Employee_s Language File
 */

// Titles
$lang['employee title forgot']                   = "Forgot Password";
$lang['employee title login']                    = "Login";
$lang['employee title profile']                  = "Profile";
$lang['employee title register']                 = "Register";
$lang['employee title employee_add']                 = "Add Employee";
$lang['employee title employee_delete']              = "Confirm Delete Employee";
$lang['employee title employee_edit']                = "Edit Employee";
$lang['employee title employee_list']                = "Employee List";

// Buttons
$lang['employee button add_new_employee']            = "Add New Employee";
$lang['employee button register']                = "Create Account";
$lang['employee button reset_password']          = "Reset Password";
$lang['employee button login_try_again']         = "Try Again";

// Tooltips
$lang['employee tooltip add_new_employee']           = "Create a brand new restaurant.";

// Links
$lang['employee link forgot_password']           = "Forgot your password?";
$lang['employee link register_account']          = "Register for an account.";

// Table Columns
$lang['employee col first_name']                 = "First Name";
$lang['employee col is_admin']                   = "Admin";
$lang['employee col last_name']                  = "Last Name";
$lang['employee col employee_id']                    = "ID";
$lang['employee col name']                   = "Employee's Name";

// Form Inputs
$lang['employee input email']                    = "Email";
$lang['employee input address']               = "Address";
$lang['employee input phone']                 = "Phone Number";
$lang['employee input restaurant']                 = "Restaurant";
$lang['employee input close_hour']                = "Close Hour";
$lang['employee input password']                 = "Password";
$lang['employee input password_repeat']          = "Repeat Password";
$lang['employee input status']                   = "Status";
$lang['employee input name']                 = "Employee_name";
$lang['employee input name_email']           = "Employee_name or Email";

// Help
$lang['employee help passwords']                 = "Only enter passwords if you want to change it.";

// Messages
$lang['employee msg add_employee_success']           = "%s was successfully added!";
$lang['employee msg delete_confirm']             = "Are you sure you want to delete <strong>%s</strong>? This can not be undone.";
$lang['employee msg delete_employee']                = "You have succesfully deleted <strong>%s</strong>!";
$lang['employee msg edit_profile_success']       = "Your profile was successfully modified!";
$lang['employee msg edit_employee_success']          = "%s was successfully modified!";
$lang['employee msg register_success']           = "Thanks for registering, %s! Check your email for a confirmation message. Once
                                                 your account has been verified, you will be able to log in with the credentials
                                                 you provided.";
$lang['employee msg password_reset_success']     = "Your password has been reset, %s! Please check your email for your new temporary password.";
$lang['employee msg validate_success']           = "Your account has been verified. You may now log in to your account.";
$lang['employee msg email_new_account']          = "<p>Thank you for creating an account at %s. Click the link below to validate your
                                                 email address and activate your account.<br /><br /><a href=\"%s\">%s</a></p>";
$lang['employee msg email_new_account_title']    = "New Account for %s";
$lang['employee msg email_password_reset']       = "<p>Your password at %s has been reset. Click the link below to log in with your
                                                 new password:<br /><br /><strong>%s</strong><br /><br /><a href=\"%s\">%s</a>
                                                 Once logged in, be sure to change your password to something you can
                                                 remember.</p>";
$lang['employee msg email_password_reset_title'] = "Password Reset for %s";

// Errors
$lang['employee error add_employee_failed']          = "%s could not be added!";
$lang['employee error delete_employee']              = "<strong>%s</strong> could not be deleted!";
$lang['employee error edit_profile_failed']      = "Your profile could not be modified!";
$lang['employee error edit_employee_failed']         = "%s could not be modified!";
$lang['employee error email_exists']             = "The email <strong>%s</strong> already exists!";
$lang['employee error email_not_exists']         = "That email does not exists!";
$lang['employee error invalid_login']            = "Invalid name or password";
$lang['employee error password_reset_failed']    = "There was a problem resetting your password. Please try again.";
$lang['employee error register_failed']          = "Your account could not be created at this time. Please try again.";
$lang['employee error employee_id_required']         = "A numeric user ID is required!";
$lang['employee error employee_not_exist']           = "That user does not exist!";
$lang['employee error name_exists']          = "The name <strong>%s</strong> already exists!";
$lang['employee error validate_failed']          = "There was a problem validating your account. Please try again.";
$lang['employee error too_many_login_attempts']  = "You've made too many attempts to log in too quickly. Please wait %s seconds and try again.";
