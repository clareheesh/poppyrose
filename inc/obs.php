<?php


/** Save repeating fields to post_meta after entry has been created or updated */
/** This function is heavily reliant on the form in Formidable Pro remaining the way it is e.g. none of the field ID's change */
add_filter('frm_new_post', 'save_repeating_fields_order', 10, 2);
function save_repeating_fields_order($post, $args)
{

	if ($args['form']->id == 2) { // The id of the order flowers form

		$result = array();
		$flowers = 0;

		$data = isset($_POST['item_meta'][64]) ? $_POST['item_meta'][64] : false;
		if (isset($data['form']))
			unset($data['form']);

		if ($data) {
			if (is_array($data) || is_object($data)) {

				foreach ($data as $i => $flowers) {
					if ($flowers && (is_array($flowers) || is_object($flowers))) {
						foreach ($flowers as $id => $value) {
							$key = get_key_by_id($id);

							if ($key != '')
								$post['post_custom'][$key . '_' . $i] = $value;
						}
					}
				}
			}

			$post['post_custom']['count_flowers'] = count($flowers);
		}

		// This is a new application, set the status to saved
		// $post['post_custom']['status'] = 'saved';
		// This is a new application, mark it as unread
		// $post['post_custom']['unread'] = true;
	}

	return $post;
}





/** Create a user if requested after form entry submission. Then update the order and assign to that newly created user */
add_action('frm_after_create_entry', 'create_new_poppy_user', 30, 2);
function create_new_poppy_user($entry_id, $form_id)
{

	if ($form_id == 2 && !is_user_logged_in()) {
		// This is the order flowers form - users who are already logged in cannot create accounts, so skip them

		$entry = FrmEntry::getOne($entry_id);
		$post_id = $entry->post_id;

		$create_account = isset($_POST['item_meta'][101]) ? $_POST['item_meta'][101] : false;

		if ($create_account) {

			// Validation checks ensure that the email isn't already assigned to an account
			// and that it isset
			$email = $_POST['item_meta'][87];
			$password = $_POST['item_meta'][102];
			$display_name = $_POST['item_meta'][85];

			// Create the user with their email as a username
			if ($user = wp_create_user($email, $password, $email)) {
				// User created successfully
				// Assign the post to the user
				wp_update_post(array(
					'ID' => 'post_id',
					'post_author' => $user
				));
			} else {
				// There has been a problem
			}
		}
	}
}


/** Ensure that a user attempting to create an account is not already registered */
add_filter('frm_validate_field_entry', 'ensure_unique_email', 10, 3);
function ensure_unique_email($errors, $field, $value)
{

	if ($field->id == 101 && $value) { // Field 101 is the "create account" checkbox

		$validate_email = $_POST['item_meta'][87]; // Field 87 is the email field
		$user = get_user_by('email', $validate_email);

		if ($user) {
			$errors['field87'] = 'Sorry, that email address is already taken.';
		}
	}

	return $errors;
}
