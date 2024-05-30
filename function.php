// Shortcode for user registration form
function custom_registration_form() {
	ob_start();
?>
<div id="custom-registration-form-container">
	<div class="registration-form failed-alert">
		<div class="failed-alert-content">
			<div class="failed-alert-icon icon-alert"><img src="#"></div>
			<div class="failed-alert-message"> </div>
		</div>
	</div>
	<form id="custom-registration-form" action="" method="post">
		<input type="hidden" name="action" value="custom_user_registration">
		<div class="name-email">
			<p>
				<label for="email" class="required-label">Email:</label>
				<input type="email" name="email" required>
			</p>
		</div>
		<div class="first-lastName">
			<p>
				<label for="first_name" class="required-label">First Name:</label>
				<input type="text" name="first_name" required>
			</p>
			<p>
				<label for="last_name" class="required-label">Last Name:</label>
				<input type="text" name="last_name" required>
			</p>
		</div>
		<div class="pass-conPass">
			<p>
				<label for="password" class="required-label">Password:</label>
				<input type="password" name="password" id="password" required>
			</p>
			<p>
				<label for="confirm_password" class="required-label">Confirm Password:</label>
				<input type="password" name="confirm_password" id="confirm_password" required>
			</p>
		</div>
		<div class="custom-dropdown-reg">
			<p>
				<label for="state" class="required-label">State:</label>
				<select name="state" id="state" required>
					<option value="Alabama" selected>Alabama</option>
					<option value="Alaska">Alaska</option>
					<option value="Arizona">Arizona</option>
					<option value="Arkansas">Arkansas</option>
				</select>
			</p>
		</div>
		<p class="ce-deadline-date-checkout" id="date_field">
			<label for="date_field" class="required-label">CE Deadline:</label>
			<input type="date" name="date_field" id="date_field" value="" required>
		</p>
		<button type="button" id="register-btn">Register</button>
	</form>
</div>
<?php

	return ob_get_clean();
}
add_shortcode('custom_registration_form', 'custom_registration_form');

// Handle form submission using AJAX
function handle_registration_form_ajax() {
	$result_message = ''; // Initialize the result message

	if (isset($_POST['action']) && $_POST['action'] === 'custom_user_registration') {
		$email = sanitize_email($_POST['email']);
		$first_name = sanitize_text_field($_POST['first_name']);
		$last_name = sanitize_text_field($_POST['last_name']);
		$password = $_POST['password'];
		$confirm_password = $_POST['confirm_password'];
		$discipline = sanitize_text_field($_POST['discipline']); 
		$state = sanitize_text_field($_POST['state']); 
		$date = sanitize_text_field($_POST['date_field']);

		// Validate inputs
		if (empty($email) && empty($first_name) && empty($last_name) && empty($password) && empty($confirm_password)) {
			$result_message = 'Registration require all fields.';
		} elseif (empty($first_name)) {
			$result_message = 'Registration requires a first name.';
		} elseif (empty($last_name)) {
			$result_message = 'Registration requires a last name.';
		} elseif (empty($email)) {
			$result_message = 'Registration requires a valid email.';
		} elseif (empty($password)) {
			$result_message = 'Registration requires a password.';
		} elseif (empty($confirm_password)) {
			$result_message = 'Registration requires a confirm password.';
		}elseif ($password !== $confirm_password) {
			$result_message = 'Passwords do not match. Please try again.';
		} elseif (empty($ce_date)) {
			$result_message = 'Registration requires Date.';
		} elseif (username_exists($username)) {
			$result_message = 'Registration username exists.';
		} elseif (email_exists($email)) {
			$result_message = 'Registration email address exists.';
		} else {
			// Extract username from email
			list($username) = explode('@', $email);

			// Create user data array
			$user_data = array(
				'user_login' => $username,
				'user_email' => $email,
				'user_pass'  => $password,
				'first_name' => $first_name,
				'last_name'  => $last_name,
			);

			// Perform user registration
			$user_id = wp_insert_user($user_data);

			if (!is_wp_error($user_id)) {

				// Add state to user meta using ACF
				update_field('state', $state, 'user_' . $user_id);
				update_user_meta( $user_id, 'date', sanitize_text_field( $_POST['date_field'] ) );

				// Log in the user
				$creds = array(
					'user_login'    => $username,
					'user_password' => $password,
					'remember'      => true,
				);
				$user_signon = wp_signon($creds, false);

				if (!is_wp_error($user_signon)) {
					// Registration successful
					$result_message = 'success';
				} else {
					// Login failed after registration
					$result_message = 'Login failed after registration. Please try logging in manually.';
				}
			} else {
				// Registration failed, handle the error
				$result_message = $user_id->get_error_message();
			}
		}
	}

	echo $result_message;
	die(); // This is important to end the AJAX request
}
add_action('wp_ajax_custom_user_registration', 'handle_registration_form_ajax');
add_action('wp_ajax_nopriv_custom_user_registration', 'handle_registration_form_ajax');
