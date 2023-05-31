<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Employee_s Language File
 */

// Titles
$lang['scheduling title forgot']                   = "Forgot Password";
$lang['scheduling title login']                    = "Login";
$lang['scheduling title profile']                  = "Profile";
$lang['scheduling title register']                 = "Register";
$lang['scheduling title restaurant_add']                 = "Add Employee";
$lang['scheduling title restaurant_delete']              = "Confirm Delete Employee";
$lang['scheduling title restaurant_edit']                = "Edit Employee";
$lang['scheduling title restaurant_list']                = "Employee List";
$lang['scheduling title period_list']                = "List of Working Period";
$lang['scheduling title dayoff_list']                = "Setup Schedule";


// Buttons
$lang['scheduling button add_new_scheduling']            = "Add New Employee";
$lang['scheduling button register']                = "Create Account";
$lang['scheduling button reset_password']          = "Reset Password";
$lang['scheduling button login_try_again']         = "Try Again";

// Tooltips
$lang['scheduling tooltip add_new_scheduling']           = "Create a brand new restaurant.";

// Links
$lang['scheduling link forgot_password']           = "Forgot your password?";
$lang['scheduling link register_account']          = "Register for an account.";

// Table Columns
$lang['scheduling col first_name']                 = "First Name";
$lang['scheduling col is_admin']                   = "Admin";
$lang['scheduling col last_name']                  = "Last Name";
$lang['scheduling col restaurant_id']                    = "ID";
$lang['scheduling col name']                   = "Employee's Name";

// Form Inputs
$lang['scheduling input email']                    = "Email";
$lang['scheduling input address']               = "Address";
$lang['scheduling input total_hours']               = "Total Hours";
$lang['scheduling input phone']                 = "Phone Number";
$lang['scheduling input open_hour']                 = "Open Hour";
$lang['scheduling input close_hour']                = "Close Hour";
$lang['scheduling input password']                 = "Password";
$lang['scheduling input password_repeat']          = "Repeat Password";
$lang['scheduling input status']                   = "Status";
$lang['scheduling input name']                 = "Employee_name";
$lang['scheduling input name_email']           = "Employee_name or Email";
$lang['scheduling input start_period']           = "Start Period";
$lang['scheduling input end_period']           = "End Period";

// Help
$lang['scheduling help passwords']                 = "Only enter passwords if you want to change it.";

// Messages
$lang['scheduling msg add_scheduling_success']           = "New period was successfully added!";
$lang['scheduling msg delete_confirm']             = "Are you sure you want to delete <strong>%s</strong>? This can not be undone.";
$lang['scheduling msg delete_scheduling']                = "Working period was succesfully deleted";
$lang['scheduling msg edit_profile_success']       = "Your profile was successfully modified!";
$lang['scheduling msg edit_scheduling_success']          = "%s was successfully modified!";
$lang['scheduling msg register_success']           = "Thanks for registering, %s! Check your email for a confirmation message. Once
                                                 your account has been verified, you will be able to log in with the credentials
                                                 you provided.";
$lang['scheduling msg password_reset_success']     = "Your password has been reset, %s! Please check your email for your new temporary password.";
$lang['scheduling msg validate_success']           = "Your account has been verified. You may now log in to your account.";
$lang['scheduling msg email_new_account']          = "<p>Thank you for creating an account at %s. Click the link below to validate your
                                                 email address and activate your account.<br /><br /><a href=\"%s\">%s</a></p>";
$lang['scheduling msg email_new_account_title']    = "New Account for %s";
$lang['scheduling msg email_password_reset']       = "<p>Your password at %s has been reset. Click the link below to log in with your
                                                 new password:<br /><br /><strong>%s</strong><br /><br /><a href=\"%s\">%s</a>
                                                 Once logged in, be sure to change your password to something you can
                                                 remember.</p>";
$lang['scheduling msg email_password_reset_title'] = "Password Reset for %s";

// Errors
$lang['scheduling error add_scheduling_failed']          = "%s could not be added!";
$lang['scheduling error delete_scheduling']              = "<strong>%s</strong> could not be deleted!";
$lang['scheduling error edit_profile_failed']      = "Your profile could not be modified!";
$lang['scheduling error edit_scheduling_failed']         = "%s could not be modified!";
$lang['scheduling error email_exists']             = "The email <strong>%s</strong> already exists!";
$lang['scheduling error email_not_exists']         = "That email does not exists!";
$lang['scheduling error invalid_login']            = "Invalid name or password";
$lang['scheduling error password_reset_failed']    = "There was a problem resetting your password. Please try again.";
$lang['scheduling error register_failed']          = "Your account could not be created at this time. Please try again.";
$lang['scheduling error restaurant_id_required']         = "A numeric user ID is required!";
$lang['scheduling error restaurant_not_exist']           = "That user does not exist!";
$lang['scheduling error name_exists']          = "The name <strong>%s</strong> already exists!";
$lang['scheduling error validate_failed']          = "There was a problem validating your account. Please try again.";
$lang['scheduling error too_many_login_attempts']  = "You've made too many attempts to log in too quickly. Please wait %s seconds and try again.";
