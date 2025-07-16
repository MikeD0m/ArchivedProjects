<?php
/*
Plugin Name: Michael's New Form Plugin
Description: Plugin to handle project form submissions and store data in MySQL database using direct connection.
Version: 3.0
Author: Michael Dominguez
*/

// Function to display login form
function custom_login_form_shortcode() {
    ob_start();
    ?>
    <style>
        #custom-login-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        #custom-login-form label {
            display: block;
            margin-bottom: 10px;
        }
        #custom-login-form input[type="text"],
        #custom-login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #custom-login-form input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #custom-login-form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        #custom-login-form p {
            margin-top: 10px;
            text-align: center;
        }
        #custom-login-form p a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        #custom-login-form p a:hover {
            text-decoration: underline;
        }
    </style>

    <form id="custom-login-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="custom_login">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Login">
    </form>

    <p>Don't have an account? <a href="<?php echo esc_url(home_url('/create-account/')); ?>">Create Account</a></p>

    <?php
    return ob_get_clean();
}
add_shortcode('custom_login_form', 'custom_login_form_shortcode');

add_action('admin_post_custom_login', 'custom_login_handler');
add_action('admin_post_nopriv_custom_login', 'custom_login_handler');

//function to handle the login form submission
function custom_login_handler() {
    global $wpdb;

    //Get the email and password received from the user's input
    $username = sanitize_text_field($_POST['email']);
    $password = $_POST['password'];
    // Validate username (assuming it can be either email or username)
    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}Portal_Users WHERE PU_Email = %s",
        $username
    ));

    if (!$user) {
        // User not found
        wp_redirect(add_query_arg('loginEmail', 'failed', home_url('/login-page/')));
        exit;
    }

    // Validate password
    if ($password !== $user->PU_Password) { // Fixed password validation comparison
        // Password incorrect
        wp_redirect(add_query_arg('loginPassword', 'failed', home_url('/login-page/')));
        exit;
    }

    // Login successful, set user session
    $user_id = $user->PU_id;
    $_SESSION['PU_ID'] = $user_id;
    // Redirect user after successful login
    wp_redirect(home_url()); // Replace with your desired redirect URL
    exit;
}

//Function that logs out the user
function custom_logout_shortcode() {
    ob_start();
    ?>
    <form id="custom-logout-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="custom_logout">
        <input type="submit" value="Logout">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_logout_form', 'custom_logout_shortcode');

// Logout handler
add_action('admin_post_custom_logout', 'custom_logout_handler');
add_action('admin_post_nopriv_custom_logout', 'custom_logout_handler');

//Handles log out by resetting the session PU_id
function custom_logout_handler() {
    unset($_SESSION['PU_ID']); // Clear session variable
    wp_safe_redirect(home_url('/login-page/')); // Redirect to login page after logout
    exit;
}
add_action('admin_post_custom_logout', 'custom_logout_handler');
add_action('admin_post_nopriv_custom_logout', 'custom_logout_handler');

//Function that displays the create account form page
function create_account_page() {
    ob_start();
    ?>
    <style>
        .create-account-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .create-account-form label {
            display: block;
            margin-bottom: 8px;
        }
        .create-account-form input[type="email"],
        .create-account-form input[type="password"],
        .create-account-form input[type="text"],
        .create-account-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .create-account-form input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .create-account-form input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>

    <div class="create-account-form">
        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="profile_picture">Profile Picture URL:</label>
            <input type="text" id="profile_picture" name="profile_picture">

            <label for="user_type">User Type:</label>
            <select id="user_type" name="user_type" required>
                <option value="provider">Provider</option>
                <option value="seeker">Seeker</option>
            </select>

            <input type="hidden" name="action" value="handle_create_account">
            <?php wp_nonce_field( 'handle_create_account_nonce', 'handle_create_account_nonce' ); ?>
            <input type="submit" value="Create Account">
        </form>
    </div>
    <?php
    return ob_get_clean();
}
// Register shortcode [create_account]
add_shortcode( 'create_account', 'create_account_page' );

//This function handles the responses from the create account form page and updates the database with this new information after validating if the user doesn't already exist
function handle_create_account() {
    if ( isset( $_POST['handle_create_account_nonce'] ) && wp_verify_nonce( $_POST['handle_create_account_nonce'], 'handle_create_account_nonce' ) ) {
        global $wpdb;

        // Sanitize and validate input
        $email = sanitize_email( $_POST['email'] );
        $password = sanitize_text_field( $_POST['password'] );
        $profile_picture = esc_url_raw( $_POST['profile_picture'] );
        $user_type = sanitize_text_field( $_POST['user_type'] );

        // Check if user already exists
        $existing_user = $wpdb->get_row( $wpdb->prepare( "SELECT PU_ID FROM {$wpdb->prefix}Portal_Users WHERE PU_Email = %s", $email ) );

        if ( $existing_user ) {
            // User already exists, redirect or handle error
            wp_redirect( home_url() ); // Example redirect
            exit;
        }

        // Insert new user into Portal_Users table
        $wpdb->insert(
            $wpdb->prefix .'Portal_Users',
            array(
                'PU_Email' => $email,
                'PU_Password' => $password,
                'PU_profile_pic' => $profile_picture,
                'PU_credits' => 0, // Initialize credits to 0
            ),
            array( '%s', '%s', '%s', '%d' )
        );

        // Get the newly inserted PU_ID
        $pu_id = $wpdb->insert_id;

        // Set PU_ID in session for future use (e.g., provider form)
        $_SESSION['PU_ID'] = $pu_id;

        // Redirect to appropriate form based on user type
        if ( $user_type === 'provider' ) {
            wp_redirect( home_url( '/create-provider/' ) ); // Redirect to create-provider page
        } elseif ( $user_type === 'seeker' ) {
            wp_redirect( home_url( '/create-seeker/' ) ); // Redirect to create-seeker page
        } else {
            wp_redirect( home_url() ); // Handle other cases as needed
        }
        exit;
    } else {
        // Nonce verification failed; handle the error or redirect as needed
        wp_die( 'Nonce verification failed' );
    }
}

// Hook into admin_post action hook for handle_create_account
add_action( 'admin_post_handle_create_account', 'handle_create_account' );
add_action( 'admin_post_nopriv_handle_create_account', 'handle_create_account' ); // For non-logged in users

//Function to display the provider account creation form which opens after the user selected to be a provider in the form before
function provider_form() {
    ob_start(); // Start output buffering to capture HTML
    ?>
    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" pattern="[0-9]+" title="Please enter numbers only" required><br><br>

        <label for="phone_extension">Phone Extension:</label>
        <input type="text" id="phone_extension" name="phone_extension" pattern="[0-9]+" title="Please enter numbers only"><br><br>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="company">Company Name:</label>
        <input type="text" id="company" name="company" required><br><br>

        <input type="hidden" name="action" value="handle_provider_form">
        <?php wp_nonce_field( 'handle_provider_form_nonce', 'handle_provider_form_nonce' ); ?>
        <input type="submit" value="Submit">
    </form>
    <?php
    return ob_get_clean(); // Return the captured HTML content
}

add_shortcode( 'provider_form', 'provider_form' );

//This function handles the responses of the provider creation form by updating the solution_provider table with this new information.
function handle_provider_form() {
    if ( isset( $_POST['handle_provider_form_nonce'] ) && wp_verify_nonce( $_POST['handle_provider_form_nonce'], 'handle_provider_form_nonce' ) ) {
        global $wpdb;

        // Ensure PU_ID exists in session
        if ( ! isset( $_SESSION['PU_ID'] ) || empty( $_SESSION['PU_ID'] ) ) {
            wp_die( 'Session error: PU_ID not found.' );
        }

        // Sanitize and validate input
        $pu_id = $_SESSION['PU_ID'];
        $first_name = sanitize_text_field( $_POST['first_name'] );
        $last_name = sanitize_text_field( $_POST['last_name'] );
        $phone = sanitize_text_field( $_POST['phone'] );
        $phone_extension = sanitize_text_field( $_POST['phone_extension'] );
        $title = sanitize_text_field( $_POST['title'] );
        $company = sanitize_text_field( $_POST['company'] );

        // Insert data into Provider_Profiles table
        $wpdb->insert(
            $wpdb->prefix . 'solution_providers',
            array(
                'PU_ID' => $pu_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'phone' => $phone,
                'phone_extension' => $phone_extension,
                'title' => $title,
                'company' => $company,
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
        );
        wp_redirect( home_url() );
        exit;
    } else {
        wp_die( 'Nonce verification failed' );
    }
}

add_action( 'admin_post_handle_provider_form', 'handle_provider_form' );
add_action( 'admin_post_nopriv_handle_provider_form', 'handle_provider_form' );

//Function to display the provider account creation form which opens after the user selected to be a seeker in the create account form

function seeker_form() {
    ob_start(); // Start output buffering to capture HTML
    ?>
    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <label for="seeker_company">Company Name:</label>
        <input type="text" id="seeker_company" name="seeker_company" required><br><br>

        <label for="seeker_url">Company URL:</label>
        <input type="text" id="seeker_url" name="seeker_url"><br><br>

        <label for="seeker_location">Location:</label>
        <input type="text" id="seeker_location" name="seeker_location" required><br><br>

        <label for="seeker_industry">Industry:</label>
        <select id="seeker_industry" name="seeker_industry" required>
            <option value="">Select Industry</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Finance">Finance</option>
            <option value="Healthcare">Healthcare</option>
            <option value="Education">Education</option>
            <option value="Manufacturing">Manufacturing</option>
        </select><br><br>

        <label for="seeker_employee_count">Employee Count:</label>
        <input type="number" id="seeker_employee_count" name="seeker_employee_count" required><br><br>

        <label for="seeker_revenue">Revenue:</label>
        <input type="text" id="seeker_revenue" name="seeker_revenue"><br><br>

        <label for="seeker_leadership_level">Leadership Level:</label>
        <select id="seeker_leadership_level" name="seeker_leadership_level" required>
            <option value="">Select Leadership Level</option>
            <option value="C-Level">C-Level</option>
            <option value="Director">Director</option>
            <option value="Supervisor">Supervisor</option>
        </select><br><br>

        <label for="seeker_department">Department:</label>
        <select id="seeker_department" name="seeker_department" required>
            <option value="">Select Department</option>
            <option value="Marketing">Marketing</option>
            <option value="Sales">Sales</option>
            <option value="Operations">Operations</option>
            <option value="Human Resources">Human Resources</option>
            <option value="Finance">Finance</option>
        </select><br><br>

        <label for="seeker_name">Name:</label>
        <input type="text" id="seeker_name" name="seeker_name"><br><br>

        <label for="seeker_title">Title:</label>
        <input type="text" id="seeker_title" name="seeker_title"><br><br>

        <label for="seeker_email">Email:</label>
        <input type="email" id="seeker_email" name="seeker_email"><br><br>

        <label for="seeker_phone">Phone:</label>
        <input type="text" id="seeker_phone" name="seeker_phone"><br><br>

        <input type="hidden" name="action" value="handle_seeker_form">
        <?php wp_nonce_field( 'handle_seeker_form_nonce', 'handle_seeker_form_nonce' ); ?>
        <input type="submit" value="Submit">
    </form>
    <?php
    return ob_get_clean(); // Return the captured HTML content
}

// Hook to display form shortcode [seeker_form]
add_shortcode( 'seeker_form', 'seeker_form' );

//This function handles the responses of the seeker creation form by updating the solution_provider table with this new information.
function handle_seeker_form() {
    if ( isset( $_POST['handle_seeker_form_nonce'] ) && wp_verify_nonce( $_POST['handle_seeker_form_nonce'], 'handle_seeker_form_nonce' ) ) {
        global $wpdb;

        // Ensure PU_ID exists in session
        if ( ! isset( $_SESSION['PU_ID'] ) || empty( $_SESSION['PU_ID'] ) ) {
            wp_die( 'Session error: PU_ID not found.' );
        }

        // Sanitize and validate input
        $pu_id = $_SESSION['PU_ID'];
        $seeker_company = sanitize_text_field( $_POST['seeker_company'] );
        $seeker_url = esc_url_raw( $_POST['seeker_url'] );
        $seeker_location = sanitize_text_field( $_POST['seeker_location'] );
        $seeker_industry = sanitize_text_field( $_POST['seeker_industry'] );
        $seeker_employee_count = intval( $_POST['seeker_employee_count'] );
        $seeker_revenue = sanitize_text_field( $_POST['seeker_revenue'] );
        $seeker_leadership_level = sanitize_text_field( $_POST['seeker_leadership_level'] );
        $seeker_department = sanitize_text_field( $_POST['seeker_department'] );
        $seeker_name = sanitize_text_field( $_POST['seeker_name'] );
        $seeker_title = sanitize_text_field( $_POST['seeker_title'] );
        $seeker_email = sanitize_email( $_POST['seeker_email'] );
        $seeker_phone = sanitize_text_field( $_POST['seeker_phone'] );

        // Insert data into Solution_Seekers table
        $table_name = $wpdb->prefix . 'solution_seekers';

        $wpdb->insert(
            $table_name,
            array(
                'PU_id' => $pu_id,
                'Seeker_company' => $seeker_company,
                'Seeker_url' => $seeker_url,
                'Seeker_location' => $seeker_location,
                'Seeker_industry' => $seeker_industry,
                'Seeker_employee_count' => $seeker_employee_count,
                'Seeker_revenue' => $seeker_revenue,
                'Seeker_leadership_level' => $seeker_leadership_level,
                'Seeker_Department' => $seeker_department,
                'Seeker_name' => $seeker_name,
                'Seeker_title' => $seeker_title,
                'Seeker_email' => $seeker_email,
                'Seeker_phone' => $seeker_phone,
            ),
            array(
                '%d', // PU_id
                '%s', // Seeker_company
                '%s', // Seeker_url
                '%s', // Seeker_location
                '%s', // Seeker_industry
                '%d', // Seeker_employee_count
                '%s', // Seeker_revenue
                '%s', // Seeker_leadership_level
                '%s', // Seeker_Department
                '%s', // Seeker_name
                '%s', // Seeker_title
                '%s', // Seeker_email
                '%s', // Seeker_phone
            )
        );
        // Optionally, redirect the user after submission
        wp_redirect( home_url() );
        exit;
    } else {
        // Nonce verification failed; handle the error or redirect as needed
        wp_die( 'Nonce verification failed' );
    }
}

// Hook into admin_post action hook for seeker form
add_action( 'admin_post_handle_seeker_form', 'handle_seeker_form' );
add_action( 'admin_post_nopriv_handle_seeker_form', 'handle_seeker_form' ); // For non-logged in users


// Function to display the credit selection form with buttons that when clicked takes the user to checkout
function credit_selection_page() {
    ob_start(); // Start output buffering to capture HTML
    ?>
    <h1>Choose a Credit Amount</h1>
    <div style="display: flex; justify-content: space-around; margin-top: 20px;">
        <button onclick="location.href='<?php echo esc_url( add_query_arg( 'credits', '1000', home_url('/checkout/') ) ); ?>'" style="padding: 20px; font-size: 20px;">1000 Credits</button>
        <button onclick="location.href='<?php echo esc_url( add_query_arg( 'credits', '2000', home_url('/checkout/') ) ); ?>'" style="padding: 20px; font-size: 20px;">2000 Credits</button>
        <button onclick="location.href='<?php echo esc_url( add_query_arg( 'credits', '3000', home_url('/checkout/') ) ); ?>'" style="padding: 20px; font-size: 20px;">3000 Credits</button>
        <button onclick="location.href='<?php echo esc_url( add_query_arg( 'credits', '4000', home_url('/checkout/') ) ); ?>'" style="padding: 20px; font-size: 20px;">4000 Credits</button>
    </div>
    <?php
    return ob_get_clean(); // Return the captured HTML content
}
add_shortcode( 'credit_selection', 'credit_selection_page' );

// Shortcode to display checkout page
function checkout_page() {
    if ( !isset($_GET['credits']) ) {
        return '<p>Invalid access. Please select a credit amount first.</p>';
    }

    $credits = intval($_GET['credits']);
    $cost = ($credits / 1000) * 10;

    ob_start(); // Start output buffering to capture HTML
    ?>
    <style>
        .checkout-form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        .checkout-form h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .checkout-form label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        .checkout-form input[type="text"],
        .checkout-form select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .checkout-form input[type="submit"],
        .checkout-form .cancel-button {
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .checkout-form input[type="submit"] {
            background-color: #007bff;
            color: #fff;
        }
        .checkout-form .cancel-button {
            background-color: #dc3545;
            color: #fff;
            margin-right: 10px;
        }
        .checkout-form input[type="submit"]:hover,
        .checkout-form .cancel-button:hover {
            background-color: #0056b3;
        }
    </style>
    <div class="checkout-form">
        <h1>Checkout</h1>
        <form id="checkout-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <label>Credits Amount:</label>
            <input type="text" value="<?php echo $credits; ?>" readonly><br><br>

            <label>Cost:</label>
            <input type="text" value="$<?php echo $cost; ?>" readonly><br><br>

            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">Paypal</option>
            </select><br><br>

            <input type="hidden" name="credits_amount" value="<?php echo $credits; ?>">
            <input type="hidden" name="cost" value="<?php echo $cost; ?>">
            <input type="hidden" name="action" value="handle_checkout_form">
            <?php wp_nonce_field( 'handle_checkout_nonce', 'handle_checkout_nonce' ); ?>
            <input type="submit" value="Add Credits">
            <a href="<?php echo esc_url( home_url() ); ?>" class="cancel-button">Cancel</a>
        </form>
    </div>
    <?php
    return ob_get_clean(); // Return the captured HTML content
}
add_shortcode( 'checkout_page', 'checkout_page' );



// Function to handle checkout form submission
function handle_checkout_form() {
    if ( isset( $_POST['handle_checkout_nonce'] ) && wp_verify_nonce( $_POST['handle_checkout_nonce'], 'handle_checkout_nonce' ) ) {
        global $wpdb;

        // Ensure user is logged in and get PU_ID from session
        if ( !isset( $_SESSION['PU_ID'] ) ) {
            wp_die( 'Session error: PU_ID not found.' );
        }
        $user_id = $_SESSION['PU_ID'];

        // Sanitize and validate input (if necessary, although it's not needed for user_id)
        $credits_amount = intval( $_POST['credits_amount'] );

        // Perform credit addition logic on Portal_Users table
        $table_name = $wpdb->prefix . 'Portal_Users';
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name SET PU_credits = PU_credits + %d WHERE PU_id = %d",
                $credits_amount, 
                $user_id 
            )
        );

        // Redirect to the homepage with a success message
        wp_redirect( add_query_arg( 'credits_added', 'true', home_url() ) );
        exit;
    } else {
        // Nonce verification failed; handle the error or redirect as needed
        wp_die( 'Nonce verification failed' );
    }
}
add_action( 'admin_post_handle_checkout_form', 'handle_checkout_form' );
add_action( 'admin_post_nopriv_handle_checkout_form', 'handle_checkout_form' ); // For non-logged in users


// Function to display success message on the homepage
function display_success_message() {
    if ( isset($_GET['credits_added']) && $_GET['credits_added'] === 'true' ) {
        echo '<div id="credits-success-message" style="position: fixed; top: 20px; right: 20px; background: #dff0d8; color: #3c763d; padding: 10px; border: 1px solid #d6e9c6; z-index: 1000;">Credits successfully added to the provider\'s balance!</div>';
        echo '<script>setTimeout(function(){ document.getElementById("credits-success-message").style.display = "none"; }, 3000);</script>';
    }
}
add_action( 'wp_head', 'display_success_message' );

//function that displays the project creation form
function project_form() {
    ob_start(); // Start output buffering to capture HTML
    $tags = ['Operations', 'Innovation', 'Sustainability', 'Technology', 'Marketing', 'Product development', 'Customer service', 'Finance', 'Human resources', 'Sales']; // Define your tags here
    $campaigns = ['AWS', 'Cloud Migration', 'Digital Transformation','Customer Engagement','Cybersecurity Enhancement']; // Define your campaigns here
    ?>
    <style>
        /* Basic CSS styling for form elements */
        .project-form label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .project-form input[type="text"],
        .project-form textarea,
        .project-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        .project-form textarea {
            min-height: 100px;
        }
    </style>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="project-form">
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" required>

        <label for="project_timeline">Project Timeline:</label>
        <input type="text" id="project_timeline" name="project_timeline" required>

        <label for="project_campaign">Project Campaign:</label>
        <select id="project_campaign" name="project_campaign" required>
            <?php foreach ($campaigns as $campaign) : ?>
                <option value="<?php echo esc_attr($campaign); ?>"><?php echo esc_html($campaign); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="solution_type">Solution Type:</label>
        <input type="text" id="solution_type" name="solution_type" required>

        <label for="project_brief">Project Brief:</label>
        <textarea id="project_brief" name="project_brief" rows="4" required></textarea>

        <label for="project_goal">Project Goal:</label>
        <textarea id="project_goal" name="project_goal" rows="4" required></textarea>

        <label for="technical_considerations">Technical Considerations:</label>
        <input type="text" id="technical_considerations" name="technical_considerations" required>

        <label for="project_challenges">Project Challenges:</label>
        <textarea id="project_challenges" name="project_challenges" rows="4" required></textarea>

        <label for="project_solutions">Project Solutions:</label>
        <textarea id="project_solutions" name="project_solutions" rows="4" required></textarea>

        <label for="project_tags">Select Tags:</label><br>
        <?php foreach ($tags as $tag) : ?>
            <input type="checkbox" id="tag_<?php echo esc_attr($tag); ?>" name="project_tags[]" value="<?php echo esc_attr($tag); ?>">
            <label for="tag_<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></label><br>
        <?php endforeach; ?><br>

        <input type="hidden" name="action" value="handle_project_form">
        <?php wp_nonce_field('handle_project_form_nonce', 'handle_project_form_nonce'); ?>
        <input type="submit" value="Submit">
    </form>
    <?php
    return ob_get_clean(); // Return the captured HTML content
}

// Hook to display form shortcode [project_form]
add_shortcode('project_form', 'project_form');


// Function to handle the response of the project creation form
function handle_project_form() {
    if ( isset( $_POST['handle_project_form_nonce'] ) && wp_verify_nonce( $_POST['handle_project_form_nonce'], 'handle_project_form_nonce' ) ) {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT pu.PU_id AS PU_id, sk.Seeker_id AS SK_id
             FROM {$wpdb->prefix}portal_users pu
             INNER JOIN {$wpdb->prefix}solution_seekers sk ON pu.PU_id = sk.PU_id
             WHERE pu.PU_id = %d",
             $_SESSION['PU_ID']
        );
        $r = $wpdb->get_row($query);
        $type_id = $r->SK_id;
        // Sanitize and validate input
        $project_name = sanitize_text_field( $_POST['project_name'] );
        $project_timeline = sanitize_text_field( $_POST['project_timeline'] );
        $project_campaign = sanitize_text_field( $_POST['project_campaign'] );
        $solution_type = sanitize_text_field( $_POST['solution_type'] );
        $project_brief = sanitize_textarea_field( $_POST['project_brief'] );
        $project_goal = sanitize_textarea_field( $_POST['project_goal'] );
        $technical_considerations = sanitize_text_field( $_POST['technical_considerations'] );
        $project_challenges = sanitize_textarea_field( $_POST['project_challenges'] );
        $project_solutions = sanitize_textarea_field( $_POST['project_solutions'] );
        $project_tags = isset($_POST['project_tags']) ? $_POST['project_tags'] : [];
        $seeker_id = $type_id;
        // Set the current date for project creation date
        $project_creation_date = current_time('mysql');

        // Insert data into Projects table
        $projects_table = $wpdb->prefix . 'project_description';

        $wpdb->insert(
            $projects_table,
            array(
                'Project_name' => $project_name,
                'Project_timeline' => $project_timeline,
                'Project_campaign' => $project_campaign,
                'Solution_type' => $solution_type,
                'Project_brief' => $project_brief,
                'Project_goal' => $project_goal,
                'Technical_considerations' => $technical_considerations,
                'Project_creation_date' => $project_creation_date,
                'Project_challenges' => $project_challenges,
                'Project_solutions' => $project_solutions,
                'Seeker_id' => $seeker_id
            ),
            array(
                '%s', // project_name
                '%s', // project_timeline
                '%s', // project_campaign
                '%s', // solution_type
                '%s', // project_brief
                '%s', // project_goal
                '%s', // technical_considerations
                '%s', // project_creation_date
                '%s', // project_challenges
                '%s', // project_solutions
                '%d', // seeker_id
            )
        );

        // Get the ID of the newly inserted project
        $project_id = $wpdb->insert_id;

        // Map tags to their corresponding interest IDs
        $interest_map = [
            'Operations' => 1,
            'Innovation' => 2,
            'Sustainability' => 3,
            'Technology' => 4,
            'Marketing' => 5,
            'Product development' => 6,
            'Customer service' => 7,
            'Finance' => 8,
            'Human resources' => 9,
            'Sales' => 10,
        ];

        // Insert selected tags into Project_Interests table
        $project_interests_table = $wpdb->prefix . 'project_interests';

        foreach ($project_tags as $tag) {
            if (array_key_exists($tag, $interest_map)) {
                $interest_id = $interest_map[$tag];
                $wpdb->insert(
                    $project_interests_table,
                    array(
                        'interest_id' => $interest_id,
                        'Project_id' => $project_id,
                    ),
                    array(
                        '%d', // interest_id
                        '%d', // project_id
                    )
                );
            }
        }

        // Optionally, redirect the user after submission
        wp_redirect( home_url() );
        exit;
    } else {
        // Nonce verification failed; handle the error or redirect as needed
        wp_die( 'Nonce verification failed' );
    }
}

// Hook into admin_post action hook for Form 1
add_action( 'admin_post_handle_project_form', 'handle_project_form' );
add_action( 'admin_post_nopriv_handle_project_form', 'handle_project_form' ); // For non-logged in users


// Shortcode to display projects, having different displays for whether a user is not logged in, a seeker, or a provider
function display_projects() {
    global $wpdb;
    
    $current_user_id = $_SESSION['PU_ID'];
    
    // Query to get the Seeker_id for the current user
    $query = $wpdb->prepare(
        "SELECT sk.Seeker_id AS SK_id
        FROM {$wpdb->prefix}solution_seekers sk
        WHERE sk.PU_id = %d",
        $current_user_id
    );
    $r = $wpdb->get_row($query);
    if($r){
        $type_id = $r->SK_id;
        $project_descriptions_table = $wpdb->prefix . 'project_description';
        
        // Initialize variables for search and filter
        $search_project_name = isset($_GET['project_name']) ? sanitize_text_field($_GET['project_name']) : '';
        $selected_campaign = isset($_GET['campaign_type']) ? sanitize_text_field($_GET['campaign_type']) : '';
        $selected_interests = isset($_GET['interests']) ? $_GET['interests'] : array();

        // Build SQL query based on filters, excluding projects where Seeker_id matches the current user's Seeker_id
        $sql = "SELECT * FROM $project_descriptions_table WHERE Seeker_id != %d";
        $sql = $wpdb->prepare($sql, $type_id);
        
        if (!empty($search_project_name)) {
            $sql .= $wpdb->prepare(" AND Project_name LIKE %s", '%' . $wpdb->esc_like($search_project_name) . '%');
        }
        
        if (!empty($selected_campaign)) {
            $sql .= $wpdb->prepare(" AND Project_campaign = %s", $selected_campaign);
        }
        
        if (!empty($selected_interests)) {
            // Prepare placeholders for interests
            $placeholders = implode(',', array_fill(0, count($selected_interests), '%d'));
            
            // Prepare interest IDs for IN clause
            $interest_ids = array_map('intval', $selected_interests);
            
            // Add condition to filter by selected interests
            $sql .= " AND Project_id IN (SELECT Project_id FROM " . $wpdb->prefix . "project_interests WHERE interest_id IN ($placeholders))";
            $sql = $wpdb->prepare($sql, $interest_ids);
        }

        // Retrieve data from the database
        $project_descriptions = $wpdb->get_results($sql, ARRAY_A);

        // Retrieve campaign types and interests for dropdowns
        $campaign_types = ['AWS', 'Cloud Migration', 'Digital Transformation', 'Customer Engagement', 'Cybersecurity Enhancement'];
        $interests = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "interests", ARRAY_A);

        // Output HTML for displaying project descriptions with filters
        ob_start();
        ?>
        <style>
        .project-descriptions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .project-descriptions-table th,
        .project-descriptions-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .project-descriptions-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .project-descriptions-table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .project-descriptions-form {
            margin-bottom: 20px;
        }
        .collapsible {
            background-color: #f4f4f4;
            color: #333;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
        }
        .content {
            padding: 0 10px;
            display: none;
            overflow: hidden;
            background-color: #f9f9f9;
        }
        .content.show {
            display: block;
        }
        </style>

        <div class="project-descriptions">
            <form method="get" action="">
                <div class="project-descriptions-form">
                    <label for="project_name">Project Name:</label>
                    <input type="text" id="project_name" name="project_name" value="<?php echo esc_attr($search_project_name); ?>">
                    
                    <label for="campaign_type">Campaign Type:</label>
                    <select id="campaign_type" name="campaign_type">
                        <option value="">Select Campaign Type</option>
                        <?php foreach ($campaign_types as $campaign) : ?>
                            <option value="<?php echo esc_attr($campaign); ?>" <?php selected($selected_campaign, $campaign); ?>><?php echo esc_html($campaign); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="button" class="collapsible">Select Interests</button>
                    <div class="content">
                        <?php foreach ($interests as $interest) : ?>
                            <label>
                                <input type="checkbox" name="interests[]" value="<?php echo esc_attr($interest['interest_id']); ?>" <?php checked(in_array($interest['interest_id'], $selected_interests)); ?>>
                                <?php echo esc_html($interest['interest_name']); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                    
                    <input type="submit" value="Filter">
                </div>
            </form>
            
            <table class="wp-list-table widefat striped project-descriptions-table">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Project Timeline</th>
                        <th>Project Campaign</th>
                        <th></th>
                        <!-- Add more table headers as needed for other columns -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($project_descriptions as $project) : ?>
                        <tr>
                            <td><?php echo esc_html($project['Project_name']); ?></td>
                            <td><?php echo esc_html($project['Project_timeline']); ?></td>
                            <td><?php echo esc_html($project['Project_campaign']); ?></td>
                            <td>
                                <a href="<?php echo esc_url(add_query_arg('project_id', $project['Project_id'], home_url('/view-project-page/'))); ?>">View Page</a>
                            </td>
                            <!-- Add more columns based on your table structure -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var coll = document.getElementsByClassName("collapsible");
                for (var i = 0; i < coll.length; i++) {
                    coll[i].addEventListener("click", function() {
                        this.classList.toggle("active");
                        var content = this.nextElementSibling;
                        if (content.style.display === "block") {
                            content.style.display = "none";
                        } else {
                            content.style.display = "block";
                        }
                    });
                }
            });
        </script>
        <?php
        return ob_get_clean();
        }
    //end of seeker specific display function
    //Checks if user is provider:
    $query2 = $wpdb->prepare(
        "SELECT sp.Provider_id AS Provider_id
        FROM {$wpdb->prefix}solution_providers sp
        WHERE sp.PU_id = %d",
        $current_user_id
    );
    $r2 = $wpdb->get_row($query2);
    //Displays projects with option to request meeting if meeting doesn't exist
    if($r2){
        $provider_id = $r2->Provider_id;
        // Table names
        $project_descriptions_table = $wpdb->prefix . 'Project_Description';
        $solution_seekers_table = $wpdb->prefix . 'Solution_Seekers';
        $portal_user_table = $wpdb->prefix . 'Portal_User';

        // Initialize variables for search and filter
        $search_project_name = isset( $_GET['project_name'] ) ? sanitize_text_field( $_GET['project_name'] ) : '';
        $selected_campaign = isset( $_GET['campaign_type'] ) ? sanitize_text_field( $_GET['campaign_type'] ) : '';
        $selected_interests = isset( $_GET['interests'] ) ? $_GET['interests'] : array();

        // Build SQL query based on filters
        $sql = "SELECT pd.*, ss.Seeker_name, GROUP_CONCAT(DISTINCT it.interest_name SEPARATOR ', ') AS interests 
                FROM $project_descriptions_table pd 
                LEFT JOIN $solution_seekers_table ss ON pd.Seeker_id = ss.Seeker_id 
                LEFT JOIN " . $wpdb->prefix . "project_interests pi ON pd.Project_id = pi.Project_id 
                LEFT JOIN " . $wpdb->prefix . "interests it ON pi.interest_id = it.interest_id 
                WHERE 1=1";

        if ( ! empty( $search_project_name ) ) {
            $sql .= $wpdb->prepare(" AND pd.Project_name LIKE %s", '%' . $wpdb->esc_like($search_project_name) . '%');
        }

        if ( ! empty( $selected_campaign ) ) {
            $sql .= $wpdb->prepare(" AND pd.Project_campaign = %s", $selected_campaign);
        }

        if ( ! empty( $selected_interests ) ) {
            // Prepare placeholders for interests
            $placeholders = implode( ',', array_fill( 0, count( $selected_interests ), '%d' ));
            
            // Prepare interest IDs for IN clause
            $interest_ids = array_map( 'intval', $selected_interests );
            
            // Add condition to filter by selected interests
            $sql .= " AND pd.Project_id IN (SELECT Project_id FROM " . $wpdb->prefix . "project_interests WHERE interest_id IN ($placeholders))";
            $sql = $wpdb->prepare( $sql, $interest_ids );
        }

        $sql .= " GROUP BY pd.Project_id";

        // Retrieve data from the database
        $project_descriptions = $wpdb->get_results( $sql, ARRAY_A );

        // Retrieve campaign types for dropdown
        $campaign_types = ['AWS', 'Cloud Migration', 'Digital Transformation', 'Customer Engagement', 'Cybersecurity Enhancement'];

        // Retrieve all interests for checkboxes
        $all_interests = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "interests", ARRAY_A );
        // Output HTML for displaying project descriptions with filters
        ob_start();
        ?>
        <style>
        .project-descriptions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .project-descriptions-table th,
        .project-descriptions-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .project-descriptions-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .project-descriptions-table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .project-descriptions-form {
            margin-bottom: 20px;
        }
        .collapsible {
            background-color: #f4f4f4;
            color: #333;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
        }
        .content {
            padding: 0 10px;
            display: none;
            overflow: hidden;
            background-color: #f9f9f9;
        }
        .content.show {
            display: block;
        }
        </style>

        <div class="project-descriptions">
            <form method="get" action="">
                <div class="project-descriptions-form">
                    <label for="project_name">Project Name:</label>
                    <input type="text" id="project_name" name="project_name" value="<?php echo esc_attr( $search_project_name ); ?>">
                    
                    <label for="campaign_type">Campaign Type:</label>
                    <select id="campaign_type" name="campaign_type">
                        <option value="">Select Campaign Type</option>
                        <?php foreach ( $campaign_types as $campaign ) : ?>
                            <option value="<?php echo esc_attr( $campaign ); ?>" <?php selected( $selected_campaign, $campaign ); ?>><?php echo esc_html( $campaign ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button type="button" class="collapsible">Select Interests</button>
                    <div class="content">
                        <?php foreach ( $all_interests as $interest ) : ?>
                            <label>
                                <input type="checkbox" name="interests[]" value="<?php echo esc_attr( $interest['interest_id'] ); ?>" <?php checked( in_array( $interest['interest_id'], $selected_interests ) ); ?>>
                                <?php echo esc_html( $interest['interest_name'] ); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                    
                    <input type="submit" value="Filter">
                </div>
            </form>
            
            <table class="wp-list-table widefat striped project-descriptions-table">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Project Timeline</th>
                        <th>Project Campaign</th>
                        <th>Solution Type</th>
                        <th>User</th>
                        <?php if ( ! empty( $_SESSION['PU_ID'] ) ) : ?>
                            <th>Action</th>
                        <?php endif; ?>
                        <th></th>
                        <!-- Add more table headers as needed for other columns -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $project_descriptions as $project ) : ?>
                        <tr>
                            <td><?php echo esc_html( $project['Project_name'] ); ?></td>
                            <td><?php echo esc_html( $project['Project_timeline'] ); ?></td>
                            <td><?php echo esc_html( $project['Project_campaign'] ); ?></td>
                            <td><?php echo esc_html( $project['Solution_type'] ); ?></td>
                            <td><?php echo esc_html( $project['Seeker_name'] ); ?></td>
                            <?php 
                                    $query3 = $wpdb->prepare(
                                        "SELECT *
                                        FROM {$wpdb->prefix}meeting_information
                                        WHERE Provider_id = %d AND Project_id = %d",
                                        $provider_id,
                                        $project['Project_id']
                                    );
                                    $r3 = $wpdb->get_row($query3);
                                    if (!$r3) : ?>
                                        <td>
                                            <a href="<?php echo esc_url( add_query_arg( array(
                                                'Seeker_id' => $project['Seeker_id'],
                                                'Project_id' => $project['Project_id'],
                                                'Project_name' => $project['Project_name'],
                                                'Seeker_name' => $project['Seeker_name']
                                            ), home_url('/request-meeting/') ) ); ?>">Request Meeting</a>
                                        </td>
                                    <?php else:?>
                                    <td></td>
                                    
                                    <?php endif; ?>
                                

                                    
                            <td>
                                <a href="<?php echo esc_url( add_query_arg( 'project_id', $project['Project_id'], home_url('/view-project-page/') ) ); ?>">View Page</a>
                            </td>
                            <!-- Add more columns based on your table structure -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var coll = document.getElementsByClassName("collapsible");
                for (var i = 0; i < coll.length; i++) {
                    coll[i].addEventListener("click", function() {
                        this.classList.toggle("active");
                        var content = this.nextElementSibling;
                        if (content.style.display === "block") {
                            content.style.display = "none";
                        } else {
                            content.style.display = "block";
                        }
                    });
                }
            });
        </script>
        <?php
        return ob_get_clean();

    }
    //If user isn't logged in:

    // Table names
    $project_descriptions_table = $wpdb->prefix . 'Project_Description';
    $solution_seekers_table = $wpdb->prefix . 'Solution_Seekers';
    $portal_user_table = $wpdb->prefix . 'Portal_User';

    // Initialize variables for search and filter
    $search_project_name = isset( $_GET['project_name'] ) ? sanitize_text_field( $_GET['project_name'] ) : '';
    $selected_campaign = isset( $_GET['campaign_type'] ) ? sanitize_text_field( $_GET['campaign_type'] ) : '';
    $selected_interests = isset( $_GET['interests'] ) ? $_GET['interests'] : array();

    // Build SQL query based on filters
    $sql = "SELECT pd.*, ss.Seeker_name, GROUP_CONCAT(DISTINCT it.interest_name SEPARATOR ', ') AS interests 
            FROM $project_descriptions_table pd 
            LEFT JOIN $solution_seekers_table ss ON pd.Seeker_id = ss.Seeker_id 
            LEFT JOIN " . $wpdb->prefix . "project_interests pi ON pd.Project_id = pi.Project_id 
            LEFT JOIN " . $wpdb->prefix . "interests it ON pi.interest_id = it.interest_id 
            WHERE 1=1";

    if ( ! empty( $search_project_name ) ) {
        $sql .= $wpdb->prepare(" AND pd.Project_name LIKE %s", '%' . $wpdb->esc_like($search_project_name) . '%');
    }

    if ( ! empty( $selected_campaign ) ) {
        $sql .= $wpdb->prepare(" AND pd.Project_campaign = %s", $selected_campaign);
    }

    if ( ! empty( $selected_interests ) ) {
        // Prepare placeholders for interests
        $placeholders = implode( ',', array_fill( 0, count( $selected_interests ), '%d' ));
        
        // Prepare interest IDs for IN clause
        $interest_ids = array_map( 'intval', $selected_interests );
        
        // Add condition to filter by selected interests
        $sql .= " AND pd.Project_id IN (SELECT Project_id FROM " . $wpdb->prefix . "project_interests WHERE interest_id IN ($placeholders))";
        $sql = $wpdb->prepare( $sql, $interest_ids );
    }

    $sql .= " GROUP BY pd.Project_id";

    // Retrieve data from the database
    $project_descriptions = $wpdb->get_results( $sql, ARRAY_A );

    // Retrieve campaign types for dropdown
    $campaign_types = ['AWS', 'Cloud Migration', 'Digital Transformation', 'Customer Engagement', 'Cybersecurity Enhancement'];

    // Retrieve all interests for checkboxes
    $all_interests = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "interests", ARRAY_A );

    // Output HTML for displaying project descriptions with filters
    ob_start();
    ?>
    <style>
    .project-descriptions-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .project-descriptions-table th,
    .project-descriptions-table td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .project-descriptions-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .project-descriptions-table tbody tr:hover {
        background-color: #f5f5f5;
    }
    .project-descriptions-form {
        margin-bottom: 20px;
    }
    .collapsible {
        background-color: #f4f4f4;
        color: #333;
        cursor: pointer;
        padding: 10px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
    }
    .content {
        padding: 0 10px;
        display: none;
        overflow: hidden;
        background-color: #f9f9f9;
    }
    .content.show {
        display: block;
    }
    </style>

    <div class="project-descriptions">
        <form method="get" action="">
            <div class="project-descriptions-form">
                <label for="project_name">Project Name:</label>
                <input type="text" id="project_name" name="project_name" value="<?php echo esc_attr( $search_project_name ); ?>">
                
                <label for="campaign_type">Campaign Type:</label>
                <select id="campaign_type" name="campaign_type">
                    <option value="">Select Campaign Type</option>
                    <?php foreach ( $campaign_types as $campaign ) : ?>
                        <option value="<?php echo esc_attr( $campaign ); ?>" <?php selected( $selected_campaign, $campaign ); ?>><?php echo esc_html( $campaign ); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <button type="button" class="collapsible">Select Interests</button>
                <div class="content">
                    <?php foreach ( $all_interests as $interest ) : ?>
                        <label>
                            <input type="checkbox" name="interests[]" value="<?php echo esc_attr( $interest['interest_id'] ); ?>" <?php checked( in_array( $interest['interest_id'], $selected_interests ) ); ?>>
                            <?php echo esc_html( $interest['interest_name'] ); ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
                
                <input type="submit" value="Filter">
            </div>
        </form>
        
        <table class="wp-list-table widefat striped project-descriptions-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Project Timeline</th>
                    <th>Project Campaign</th>
                    <th>Solution Type</th>
                    <th>User</th>
                    <th></th>
                    <!-- Add more table headers as needed for other columns -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $project_descriptions as $project ) : ?>
                    <tr>
                        <td><?php echo esc_html( $project['Project_name'] ); ?></td>
                        <td><?php echo esc_html( $project['Project_timeline'] ); ?></td>
                        <td><?php echo esc_html( $project['Project_campaign'] ); ?></td>
                        <td><?php echo esc_html( $project['Solution_type'] ); ?></td>
                        <td><?php echo esc_html( $project['Seeker_name'] ); ?></td>
                        <td>
                            <a href="<?php echo esc_url( add_query_arg( 'project_id', $project['Project_id'], home_url('/view-project-page/') ) ); ?>">View Page</a>
                        </td>
                        <!-- Add more columns based on your table structure -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var coll = document.getElementsByClassName("collapsible");
            for (var i = 0; i < coll.length; i++) {
                coll[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    var content = this.nextElementSibling;
                    if (content.style.display === "block") {
                        content.style.display = "none";
                    } else {
                        content.style.display = "block";
                    }
                });
            }
        });
    </script>
    <?php
    return ob_get_clean();
    

}

// Register shortcode
add_shortcode( 'display_projects', 'display_projects' );

function view_project() {
    if ( ! isset( $_GET['project_id'] ) ) {
        return '<p style="color: red;">Invalid access. Please select a project first.</p>';
    }
    
    global $wpdb;
    $project_id = intval( $_GET['project_id'] );
    $project_table = $wpdb->prefix . 'project_description';
    $project_match_table = $wpdb->prefix . 'project_match';
    
    // Retrieve project details
    $project = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM $project_table WHERE Project_id = %d",
        $project_id
    ), ARRAY_A );
    
    if ( ! $project ) {
        return '<p style="color: red;">Project not found.</p>';
    }
    
    // Retrieve related projects based on Project_Match table
    $related_projects = $wpdb->get_results( $wpdb->prepare(
        "SELECT pd.Project_id, pd.Project_name
         FROM $project_match_table pm
         JOIN $project_table pd ON pm.Project_id2 = pd.Project_id
         WHERE pm.Project_id1 = %d",
        $project_id
    ), ARRAY_A );

    // Start output buffering
    ob_start();

    // HTML output with enhanced styling
    ?>
    <style>
        .project-details {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .project-details h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }
        .project-details p {
            margin-bottom: 5px;
            line-height: 1.6;
        }
        .related-projects {
            margin-top: 20px;
        }
        .related-projects h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .related-projects ul {
            list-style-type: none;
            padding: 0;
        }
        .related-projects ul li {
            margin-bottom: 5px;
        }
        .related-projects ul li a {
            text-decoration: none;
            color: #0073aa;
        }
    </style>
    <div class="project-details">
        <h2>Project Details</h2>
        <p><strong>Project ID:</strong> <?php echo esc_html( $project['Project_id'] ); ?></p>
        <p><strong>Project Name:</strong> <?php echo esc_html( $project['Project_name'] ); ?></p>
        <p><strong>Project Timeline:</strong> <?php echo esc_html( $project['Project_timeline'] ); ?></p>
        <p><strong>Project Campaign:</strong> <?php echo esc_html( $project['Project_campaign'] ); ?></p>
        <p><strong>Solution Type:</strong> <?php echo esc_html( $project['Solution_type'] ); ?></p>
        <p><strong>Project Brief:</strong> <?php echo esc_html( $project['Project_brief'] ); ?></p>
        <p><strong>Project Goal:</strong> <?php echo esc_html( $project['Project_goal'] ); ?></p>
        <p><strong>Technical Considerations:</strong> <?php echo esc_html( $project['Technical_considerations'] ); ?></p>
        <p><strong>Project Creation Date:</strong> <?php echo esc_html( $project['Project_creation_date'] ); ?></p>
        <p><strong>Project Challenges:</strong> <?php echo esc_html( $project['Project_challenges'] ); ?></p>
        <p><strong>Project Solutions:</strong> <?php echo esc_html( $project['Project_solutions'] ); ?></p>
    </div>

    <div class="related-projects">
        <h3>Related Projects</h3>
        <?php if ( $related_projects ) : ?>
            <ul>
                <?php foreach ( $related_projects as $related_project ) : ?>
                    <li><a href="<?php echo esc_url( add_query_arg( 'project_id', $related_project['Project_id'], home_url('/view-project/') ) ); ?>"><?php echo esc_html( $related_project['Project_name'] ); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No related projects found.</p>
        <?php endif; ?>
    </div>
    <?php

    // End output buffering and return the content
    return ob_get_clean();
}

// Register shortcode
add_shortcode( 'view_project_shortcode', 'view_project' );


//Function that displays projects that were created specifically by the current user. 
function my_projects() {
    global $wpdb;
    $query = $wpdb->prepare(
        "SELECT pu.PU_id AS PU_id, sk.Seeker_id AS SK_id
         FROM {$wpdb->prefix}portal_users pu
         INNER JOIN {$wpdb->prefix}solution_seekers sk ON pu.PU_id = sk.PU_id
         WHERE pu.PU_id = %d",
         $_SESSION['PU_ID']
    );
    $r = $wpdb->get_row($query);
    $type_id = $r->SK_id;

    $project_descriptions_table = $wpdb->prefix . 'project_description';
    
    // Initialize variables for search and filter
    $search_project_name = isset( $_GET['project_name'] ) ? sanitize_text_field( $_GET['project_name'] ) : '';
    $selected_campaign = isset( $_GET['campaign_type'] ) ? sanitize_text_field( $_GET['campaign_type'] ) : '';
    $selected_interests = isset( $_GET['interests'] ) ? $_GET['interests'] : array();
    $seeker_id = $type_id;

    // Build SQL query based on filters
    $sql = "SELECT * FROM $project_descriptions_table WHERE Seeker_id = %d";
    $sql = $wpdb->prepare($sql, $seeker_id);
    
    if ( ! empty( $search_project_name ) ) {
        $sql .= $wpdb->prepare(" AND Project_name LIKE %s", '%' . $wpdb->esc_like($search_project_name) . '%');
    }
    
    if ( ! empty( $selected_campaign ) ) {
        $sql .= $wpdb->prepare(" AND Project_campaign = %s", $selected_campaign);
    }
    
    if ( ! empty( $selected_interests ) ) {
        // Prepare placeholders for interests
        $placeholders = implode( ',', array_fill( 0, count( $selected_interests ), '%d' ));
        
        // Prepare interest IDs for IN clause
        $interest_ids = array_map( 'intval', $selected_interests );
        
        // Add condition to filter by selected interests
        $sql .= " AND Project_id IN (SELECT Project_id FROM " . $wpdb->prefix . "project_interests WHERE interest_id IN ($placeholders))";
        $sql = $wpdb->prepare( $sql, $interest_ids );
    }

    // Retrieve data from the database
    $project_descriptions = $wpdb->get_results( $sql, ARRAY_A );

    // Retrieve campaign types and interests for dropdowns
    $campaign_types = ['AWS', 'Cloud Migration', 'Digital Transformation', 'Customer Engagement', 'Cybersecurity Enhancement'];
    $interests = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "interests", ARRAY_A );

    // Output HTML for displaying project descriptions with filters
    ob_start();
    ?>
    <style>
    .project-descriptions-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .project-descriptions-table th,
    .project-descriptions-table td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .project-descriptions-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .project-descriptions-table tbody tr:hover {
        background-color: #f5f5f5;
    }
    .project-descriptions-form {
        margin-bottom: 20px;
    }
    .collapsible {
        background-color: #f4f4f4;
        color: #333;
        cursor: pointer;
        padding: 10px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
    }
    .content {
        padding: 0 10px;
        display: none;
        overflow: hidden;
        background-color: #f9f9f9;
    }
    .content.show {
        display: block;
    }
    </style>

    <div class="project-descriptions">
        <form method="get" action="">
            <div class="project-descriptions-form">
                <label for="project_name">Project Name:</label>
                <input type="text" id="project_name" name="project_name" value="<?php echo esc_attr( $search_project_name ); ?>">
                
                <label for="campaign_type">Campaign Type:</label>
                <select id="campaign_type" name="campaign_type">
                    <option value="">Select Campaign Type</option>
                    <?php foreach ( $campaign_types as $campaign ) : ?>
                        <option value="<?php echo esc_attr( $campaign ); ?>" <?php selected( $selected_campaign, $campaign ); ?>><?php echo esc_html( $campaign ); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <button type="button" class="collapsible">Select Interests</button>
                <div class="content">
                    <?php foreach ( $interests as $interest ) : ?>
                        <label>
                            <input type="checkbox" name="interests[]" value="<?php echo esc_attr( $interest['interest_id'] ); ?>" <?php checked( in_array( $interest['interest_id'], $selected_interests ) ); ?>>
                            <?php echo esc_html( $interest['interest_name'] ); ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
                
                <input type="submit" value="Filter">
            </div>
        </form>
        
        <table class="wp-list-table widefat striped project-descriptions-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Project Timeline</th>
                    <th>Project Campaign</th>
                    <th></th>
                    <!-- Add more table headers as needed for other columns -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $project_descriptions as $project ) : ?>
                    <tr>
                        <td><?php echo esc_html( $project['Project_name'] ); ?></td>
                        <td><?php echo esc_html( $project['Project_timeline'] ); ?></td>
                        <td><?php echo esc_html( $project['Project_campaign'] ); ?></td>
                        <td>
                            <a href="<?php echo esc_url( add_query_arg( 'project_id', $project['Project_id'], home_url('/view-project-page/') ) ); ?>">View Page</a>
                        </td>
                        <!-- Add more columns based on your table structure -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var coll = document.getElementsByClassName("collapsible");
            for (var i = 0; i < coll.length; i++) {
                coll[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    var content = this.nextElementSibling;
                    if (content.style.display === "block") {
                        content.style.display = "none";
                    } else {
                        content.style.display = "block";
                    }
                });
            }
        });
    </script>
    <?php
    return ob_get_clean();
}

// Register shortcode
add_shortcode( 'my_projects', 'my_projects' );

// Shortcode function to display request meeting form
function meeting_form() {
    global $wpdb;

    // Retrieve parameters from URL
    $current_user_id = $_SESSION['PU_ID'];
        
    // Query to get the Provider_id for the current user
    $query = $wpdb->prepare(
        "SELECT sp.Provider_id AS Provider_id, sp.First_name AS First_name
        FROM {$wpdb->prefix}solution_providers sp
        WHERE sp.PU_id = %d",
        $current_user_id
    );
    $r = $wpdb->get_row($query);
    $provider_id = $r->Provider_id;
    $provider_name = $r->First_name;
    $seeker_id = isset( $_GET['Seeker_id'] ) ? intval( $_GET['Seeker_id'] ) : '';
    $project_id = isset( $_GET['Project_id'] ) ? intval( $_GET['Project_id'] ) : '';
    $project_name = isset( $_GET['Project_name'] ) ? sanitize_text_field( $_GET['Project_name'] ) : '';
    $seeker_name = isset( $_GET['Seeker_name'] ) ? sanitize_text_field( $_GET['Seeker_name'] ) : '';

    ob_start();
    ?>
    <style>
        .meeting-form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        .meeting-form label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        .meeting-form p {
            margin: 8px 0;
        }
        .meeting-form textarea {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        .meeting-form input[type="date"] {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-buttons {
            margin-top: 16px;
        }
        .form-buttons input[type="submit"],
        .form-buttons .cancel-button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            transition: background-color 0.3s ease;
        }
        .form-buttons input[type="submit"]:hover,
        .form-buttons .cancel-button:hover {
            background-color: #0056b3;
        }
        .form-buttons .cancel-button {
            background-color: #6c757d;
            margin-left: 10px;
        }
    </style>
    <div class="meeting-form">
        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <input type="hidden" name="provider_id" value="<?php echo esc_attr( $provider_id ); ?>">
            <input type="hidden" name="seeker_id" value="<?php echo esc_attr( $seeker_id ); ?>">
            <input type="hidden" name="project_id" value="<?php echo esc_attr( $project_id ); ?>">
            
            <label>Provider Name:</label>
            <p><?php echo esc_html( $provider_name ); ?></p>
            
            <label>Seeker Name:</label>
            <p><?php echo esc_html( $seeker_name ); ?></p>
            
            <label>Project Name:</label>
            <p><?php echo esc_html( $project_name ); ?></p>
            
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="4" cols="50" required></textarea>
            
            <label for="meeting_date">Meeting Date:</label>
            <input type="date" id="meeting_date" name="meeting_date" required>
            
            <div class="form-buttons">
                <input type="submit" name="submit_meeting_request" value="Send Meeting Request">
                <a href="<?php echo esc_url( home_url( '/view-projects/' ) ); ?>" class="cancel-button">Cancel</a>
            </div>
            <input type="hidden" name="action" value="handle_meeting_form">
            <?php wp_nonce_field( 'handle_meeting_nonce', 'handle_meeting_nonce' ); ?>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'meeting_form', 'meeting_form' );

// Function to handle meeting request form submission
function handle_meeting_form() {
    if ( isset( $_POST['handle_meeting_nonce'] ) && wp_verify_nonce( $_POST['handle_meeting_nonce'], 'handle_meeting_nonce' ) ) {
        global $wpdb;

        // Retrieve data from form submission
        $provider_id = isset( $_POST['provider_id'] ) ? intval( $_POST['provider_id'] ) : '';
        $seeker_id = isset( $_POST['seeker_id'] ) ? intval( $_POST['seeker_id'] ) : '';
        $project_id = isset( $_POST['project_id'] ) ? intval( $_POST['project_id'] ) : '';
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
        $meeting_date = isset( $_POST['meeting_date'] ) ? sanitize_text_field( $_POST['meeting_date'] ) : '';
        $creation_date = current_time( 'mysql' );

        // Format meeting_date to MySQL format if it's not already
        $formatted_meeting_date = date( 'Y-m-d', strtotime( $meeting_date ) );

        // Insert into Meeting_Information table
        $table_name = $wpdb->prefix . 'Meeting_Information';
        $insert_result = $wpdb->insert(
            $table_name,
            array(
                'Provider_id' => $provider_id,
                'Seeker_id' => $seeker_id,
                'Project_id' => $project_id,
                'Message' => $message,
                'Creation_date' => $creation_date,
                'Meeting_date' => $formatted_meeting_date, // Set meeting date in MySQL format
                'isAccepted' => false, // Automatically set isAccepted to false
            ),
            array( '%d', '%d', '%d', '%s', '%s', '%s', '%d' )
        );

        if ( $insert_result ) {
            // Meeting request successfully inserted
            wp_redirect( add_query_arg( 'meeting_success', 'true', home_url( '/view-projects/' ) ) );
            exit;
        } else {
            // Error occurred during insertion
            wp_die( 'Error: Failed to insert meeting request.' );
        }
    } else {
        // Nonce verification failed; handle the error or redirect as needed
        wp_die( 'Nonce verification failed' );
    }
}

// Hook for handling meeting form submission
add_action( 'admin_post_handle_meeting_form', 'handle_meeting_form' );
add_action( 'admin_post_nopriv_handle_meeting_form', 'handle_meeting_form' ); // For non-logged in users

//Function that displays all meetings involving the current user.
function display_meetings() {
    if (!isset($_SESSION['PU_ID'])) {
        // User is not logged in, display a message or handle as needed
        return 'Please log in to view meetings.';
    }

    global $wpdb;

    $user_id = $_SESSION['PU_ID'];

    // Check if the user is a provider or seeker
    $provider_query = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}solution_providers WHERE PU_id = %d",
        $user_id
    );
    $seeker_query = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}solution_seekers WHERE PU_id = %d",
        $user_id
    );

    $is_provider = $wpdb->get_row($provider_query);
    $is_seeker = $wpdb->get_row($seeker_query);

    if ($is_provider) {
        // User is a provider, display meetings for providers
        $provider_id = $is_provider->Provider_id;

        $query = $wpdb->prepare(
            "SELECT mi.*, pd.Project_name, ss.Seeker_name
            FROM {$wpdb->prefix}meeting_information mi
            INNER JOIN {$wpdb->prefix}project_description pd ON mi.Project_id = pd.Project_id
            INNER JOIN {$wpdb->prefix}solution_seekers ss ON mi.Seeker_id = ss.Seeker_id
            WHERE mi.Provider_id = %d
            ORDER BY pd.Project_name ASC",
            $provider_id
        );

        $meetings = $wpdb->get_results($query, ARRAY_A);

        ob_start();
        ?>
        <style>
            .meetings-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .meetings-table th,
            .meetings-table td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            .meetings-table th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            .meetings-table tbody tr:hover {
                background-color: #f5f5f5;
            }
            .meetings-table td.actions a {
                display: inline-block;
                margin-right: 5px;
                padding: 5px 10px;
                text-decoration: none;
                border: 1px solid #ccc;
                border-radius: 4px;
                background-color: #f2f2f2;
                color: #333;
            }
            .meetings-table td.actions a:hover {
                background-color: #e2e2e2;
            }
        </style>

        <table class="meetings-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Seeker Name</th>
                    <th>Message</th>
                    <th>Meeting Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meetings as $meeting) : ?>
                    <tr>
                        <td><?php echo esc_html($meeting['Project_name']); ?></td>
                        <td><?php echo esc_html($meeting['Seeker_name']); ?></td>
                        <td><?php echo esc_html($meeting['Message']); ?></td>
                        <td><?php echo esc_html($meeting['Meeting_date']); ?></td>
                        <td>
                            <?php if ($meeting['isWaiting']) : ?>
                                    Pending
                            <?php elseif (!$meeting['isWaiting'] && $meeting['isAccepted']) : ?>
                                Accepted
                            <?php else : ?>
                                Denied
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();

    } elseif ($is_seeker) {
        // User is a seeker, display meetings for seekers
        $seeker_id = $is_seeker->Seeker_id;

        $query = $wpdb->prepare(
            "SELECT mi.*, pd.Project_name, sp.First_name, sp.Last_name
            FROM {$wpdb->prefix}meeting_information mi
            INNER JOIN {$wpdb->prefix}project_description pd ON mi.Project_id = pd.Project_id
            INNER JOIN {$wpdb->prefix}solution_providers sp ON mi.Provider_id = sp.Provider_id
            WHERE mi.Seeker_id = %d
            ORDER BY pd.Project_name ASC",
            $seeker_id
        );

        $meetings = $wpdb->get_results($query, ARRAY_A);

        ob_start();
        ?>
        <style>
            .meetings-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .meetings-table th,
            .meetings-table td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            .meetings-table th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            .meetings-table tbody tr:hover {
                background-color: #f5f5f5;
            }
            .meetings-table td.actions a {
                display: inline-block;
                margin-right: 5px;
                padding: 5px 10px;
                text-decoration: none;
                border: 1px solid #ccc;
                border-radius: 4px;
                background-color: #f2f2f2;
                color: #333;
            }
            .meetings-table td.actions a:hover {
                background-color: #e2e2e2;
            }
        </style>

        <table class="meetings-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Provider Name</th>
                    <th>Message</th>
                    <th>Meeting Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meetings as $meeting) : ?>
                    <tr>
                        <td><?php echo esc_html($meeting['Project_name']); ?></td>
                        <td><?php echo esc_html($meeting['First_name']); ?></td>
                        <td><?php echo esc_html($meeting['Message']); ?></td>
                        <td><?php echo esc_html($meeting['Meeting_date']); ?></td>
                        <td>
                            <?php if ($meeting['isWaiting']) : ?>
                                <?php if ($meeting['isAccepted']) : ?>
                                    Accepted
                                <?php else : ?>
                                    Pending
                                <?php endif; ?>
                            <?php elseif (!$meeting['isWaiting'] && $meeting['isAccepted']) : ?>
                                Accepted
                            <?php else : ?>
                                Denied
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if ($meeting['isWaiting']) : ?>
                                <a href="<?php echo esc_url(add_query_arg(array(
                                    'action' => 'accept',
                                    'meeting_id' => $meeting['Meeting_id'],
                                    'provider_name' => $meeting['First_name'],
                                    'project_name' => $meeting['Project_name'],
                                ), home_url('/meeting-confirmation/'))); ?>">Accept</a>
                                <a href="<?php echo esc_url(add_query_arg(array(
                                    'action' => 'deny',
                                    'meeting_id' => $meeting['Meeting_id'],
                                    'provider_name' => $meeting['First_name'],
                                    'project_name' => $meeting['Project_name'],
                                ), home_url('/meeting-confirmation/'))); ?>">Deny</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();

    } else {
        // User is neither a provider nor a seeker, handle accordingly
        return 'You are not authorized to view this page.';
    }
}
add_shortcode('display_meetings', 'display_meetings');

//Function to handle the action a seeker chooses in the display meetings form.
function meeting_confirmation() {
    global $wpdb;

    $meeting_id = isset($_GET['meeting_id']) ? intval($_GET['meeting_id']) : '';
    $project_name = isset($_GET['project_name']) ? sanitize_text_field($_GET['project_name']) : '';
    $provider_name = isset($_GET['provider_name']) ? sanitize_text_field($_GET['provider_name']) : '';
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

    ob_start();
    ?>
    <style>
        .meeting-form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        .meeting-form label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        .meeting-form p {
            margin: 8px 0;
        }
        .meeting-form textarea {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        .meeting-form input[type="date"] {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-buttons {
            margin-top: 16px;
        }
        .form-buttons input[type="submit"],
        .form-buttons .cancel-button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            transition: background-color 0.3s ease;
        }
        .form-buttons input[type="submit"]:hover,
        .form-buttons .cancel-button:hover {
            background-color: #0056b3;
        }
        .form-buttons .cancel-button {
            background-color: #6c757d;
            margin-left: 10px;
        }
    </style>
    <div class="meeting-form">
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="meeting_id" value="<?php echo esc_attr($meeting_id); ?>">
            <input type="hidden" name="action" value="handle_meeting_confirmation">
            <label>Provider Name:</label>
            <p><?php echo esc_html($provider_name); ?></p>
            
            <label>Project Name:</label>
            <p><?php echo esc_html($project_name); ?></p>
            <br>
            <label>Are you sure you want to <?php echo esc_html($action); ?> the meeting?</label>
            <div class="form-buttons">
                <input type="hidden" name="confirmation_action" value="<?php echo esc_attr($action); ?>">
                <input type="submit" name="submit_meeting_confirmation" value="<?php echo esc_html($action); ?>">
                <a href="<?php echo esc_url(home_url('/view-meetings/')); ?>" class="cancel-button">Cancel</a>
            </div>
            <?php wp_nonce_field('handle_meeting_confirmation_nonce', 'handle_meeting_confirmation_nonce'); ?>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('meeting_confirmation', 'meeting_confirmation');

//Function handles the meeting confirmation results
function handle_meeting_confirmation() {
    if (isset($_POST['handle_meeting_confirmation_nonce']) && wp_verify_nonce($_POST['handle_meeting_confirmation_nonce'], 'handle_meeting_confirmation_nonce')) {
        global $wpdb;

        // Retrieve data from form submission
        $meeting_id = isset($_POST['meeting_id']) ? intval($_POST['meeting_id']) : '';
        $action = isset($_POST['confirmation_action']) ? sanitize_text_field($_POST['confirmation_action']) : '';

        // Determine the values for isAccepted and isWaiting based on the action
        $isAccepted = ($action === 'accept') ? 1 : 0;
        $isWaiting = 0;

        // Update the Meeting_Information table
        $table_name = $wpdb->prefix . 'meeting_information';
        $update_result = $wpdb->update(
            $table_name,
            array(
                'isAccepted' => $isAccepted,
                'isWaiting' => $isWaiting
            ),
            array('Meeting_id' => $meeting_id),
            array('%d', '%d'),
            array('%d')
        );

        if ($update_result !== false) {
            // Meeting status successfully updated
            wp_redirect(add_query_arg('meeting_confirmation_success', 'true', home_url('/view-meetings/')));
            exit;
        } else {
            // Error occurred during update
            wp_die('Error: Failed to update meeting status.');
        }
    } else {
        // Nonce verification failed; handle the error or redirect as needed
        wp_die('Nonce verification failed');
    }
}

// Hook for handling meeting form submission
add_action('admin_post_handle_meeting_confirmation', 'handle_meeting_confirmation');
add_action('admin_post_nopriv_handle_meeting_confirmation', 'handle_meeting_confirmation'); // For non-logged in users

//Function that generates a profile bar for a user depending if the user is logged in, and if they are logged in as a provider or a seeker.
function profile_bar_shortcode() {
    if (!isset($_SESSION['PU_ID'])) {
        // PU_ID not set in session, assume user is not logged in
        ob_start();
        ?>
        <div class="profile-bar" style="color: black; display: flex; align-items: center;">
            <p><a style="color: black; margin-right: 10px;" href="<?php echo esc_url(home_url('/login-page/')); ?>">Login</a></p>
            <p><a style="color: black;" href="<?php echo esc_url(home_url('/create-account/')); ?>">Create Account</a></p>
            <p></p>
            <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(home_url('/view-projects/')); ?>">View Projects</a></p>
        </div>
        <?php
        return ob_get_clean();
    }

    global $wpdb;
    $user_id = $_SESSION['PU_ID'];

    // Query to fetch user details including credits and user type
    $query = $wpdb->prepare(
        "SELECT pu.PU_id AS PU_id, pu.PU_credits AS PU_credits, sp.First_name AS PU_name, pu.PU_profile_pic AS PU_profile_pic
         FROM {$wpdb->prefix}portal_users pu
         INNER JOIN {$wpdb->prefix}solution_providers sp ON pu.PU_id = sp.PU_id
         WHERE pu.PU_id = %d",
         $user_id
    );

    $user = $wpdb->get_row($query);

    if (!$user) {
        // User not found or not a provider, display seeker bar
        $query2 = $wpdb->prepare(
            "SELECT pu.PU_id AS PU_id, pu.PU_credits AS PU_credits, sk.Seeker_name AS PU_name, pu.PU_profile_pic AS PU_profile_pic
             FROM {$wpdb->prefix}portal_users pu
             INNER JOIN {$wpdb->prefix}solution_seekers sk ON pu.PU_id = sk.PU_id
             WHERE pu.PU_id = %d",
             $user_id
        );
        $user2 = $wpdb->get_row($query2);
        if(!$user2){
            ob_start();
            ?>
            <div class="profile-bar" style="color: black; display: flex; align-items: center;">
                <p><a style="color: black; margin-right: 30px;" href="<?php echo esc_url(home_url('/login-page/')); ?>">Login</a></p>
                <p><a style="color: black;" href="<?php echo esc_url(home_url('/create-account/')); ?>">Create Account</a></p>
                <p style="margin-right: 30px;"><a style="color: black;" href="<?php echo esc_url(home_url('/view-projects/')); ?>">View Projects</a></p>
            </div>
            <?php
            return ob_get_clean();
        }
        ob_start();
        ?>
        <div class="profile-bar" style="color: black; display: flex; align-items: center;">
            <?php if (isset($user2->PU_credits)) : ?>
                <p style="margin-right: 20px;">Credits: <span style="color: purple;"><?php echo esc_html($user2->PU_credits); ?></span></p>
            <?php endif; ?>
            <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(home_url('/view-projects/')); ?>">View projects</a></p>
            <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(admin_url('admin-post.php?action=custom_logout')); ?>">Logout</a></p>
            <p><?php echo esc_html($user2->PU_name); ?></p>
            <?php if (isset($user2->PU_profile_pic)) : ?>
                <img src="<?php echo esc_url($user2->PU_profile_pic); ?>" alt="Profile Picture" style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px; margin-right: 10px;">
            <?php endif; ?>
            <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(home_url('/my-projects/')); ?>">My projects</a></p>
            <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(home_url('/create-project/')); ?>">Create a project</a></p>
            <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(home_url('/view-meetings/')); ?>">View meetings</a></p>
            <p><a style="color: black; margin-left: 20px;" href="<?php echo esc_url(home_url('/purchase-credits/')); ?>">Buy credits</a></p>
        </div>
        <?php
        return ob_get_clean();
    }

    // User is logged in, display appropriate profile bar
    ob_start();
    ?>
    <div class="profile-bar" style="color: black; display: flex; align-items: center;">
        <?php if (isset($user->PU_credits)) : ?>
            <p style="margin-right: 20px;">Credits: <span style="color: purple;"><?php echo esc_html($user->PU_credits); ?></span></p>
        <?php endif; ?>
        <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(home_url('/view-projects/')); ?>">View projects</a></p>
        <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(admin_url('admin-post.php?action=custom_logout')); ?>">Logout</a></p>
        <p><?php echo esc_html($user->PU_name); ?></p>
        <?php if (isset($user->PU_profile_pic)) : ?>
            <img src="<?php echo esc_url($user->PU_profile_pic); ?>" alt="Profile Picture" style="width: 30px; height: 30px; border-radius: 50%; margin-left: 10px; margin-right: 10px;">
        <?php endif; ?>
        <p style="margin-right: 20px;"><a style="color: black;" href="<?php echo esc_url(home_url('/view-meetings/')); ?>">View meetings</a></p>
        <p><a style="color: black; margin-left: 20px;" href="<?php echo esc_url(home_url('/purchase-credits/')); ?>">Buy credits</a></p>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('profile_bar', 'profile_bar_shortcode');






