
	jQuery(document).ready(function ($) {
		$('#register-btn').on('click', function () {
			// Use AJAX to submit the form
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: $('#custom-registration-form').serialize(),
				success: function (response) {
					if (response === 'success') {
						// Redirect to own page after successful registration
						window.location.href = '<?php echo home_url('#'); ?>';
					} else {
						// Display the error message in the div and show the failed-alert div
						$('.failed-alert-message').html(response);
						$('.registration-form.failed-alert').show();

						// Scroll to the top of the specific container
						$('.class #id').animate({ scrollTop: 0 }, 'fast');
					}
				}
			});
		});
		// Add event listener to hide failed-alert on input field click for registration form
		$('#custom-registration-form input').on('click', function () {
			$('.registration-form.failed-alert').hide();
			$('.failed-alert-message').html(''); // Clear the error message
		});
	});
