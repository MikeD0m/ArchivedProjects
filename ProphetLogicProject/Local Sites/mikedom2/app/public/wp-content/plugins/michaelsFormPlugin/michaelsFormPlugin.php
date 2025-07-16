<?php
/*
Plugin Name: Michael's Form Plugin
Description: Plugin to handle multiple forms for different tables.
Version: 2.0
Author: Michael Dominguez
*/
//Provider Form
// Provider Form
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

// Hook to display form shortcode [provider_form]
add_shortcode( 'provider_form', 'provider_form' );

// Function to handle form submission
function handle_provider_form() {
    if ( isset( $_POST['handle_provider_form_nonce'] ) && wp_verify_nonce( $_POST['handle_provider_form_nonce'], 'handle_provider_form_nonce' ) ) {
        global $wpdb;

        // Sanitize and validate input
        $first_name = sanitize_text_field( $_POST['first_name'] );
        $last_name = sanitize_text_field( $_POST['last_name'] );
        $phone = sanitize_text_field( $_POST['phone'] );
        $phone_extension = isset( $_POST['phone_extension'] ) ? absint( $_POST['phone_extension'] ) : null; // Convert to integer if provided
        $title = sanitize_text_field( $_POST['title'] );
        $company = sanitize_text_field( $_POST['company'] );
        $provider_credits = 0; // Example value, adjust as per your logic

        // Insert data into Solution_Providers table
        $table_name = $wpdb->prefix . 'Solution_Providers';

        $wpdb->insert(
            $table_name,
            array(
                'First_name' => $first_name,
                'Last_name' => $last_name,
                'Phone' => $phone,
                'Phone_extension' => $phone_extension,
                'Title' => $title,
                'Company' => $company,
                'Provider_credits' => $provider_credits,
            ),
            array(
                '%s', // First_name
                '%s', // Last_name
                '%s', // Phone 
                '%d', // Phone_extension
                '%s', // Title
                '%s', // Company
                '%d', // Provider_credits
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

// Hook into admin_post action hook for Form 1
add_action( 'admin_post_handle_provider_form', 'handle_provider_form' );
add_action( 'admin_post_nopriv_handle_provider_form', 'handle_provider_form' ); // For non-logged in users


//project form
function project_form() {
    ob_start(); // Start output buffering to capture HTML
    $tags = ['Operations', 'Innovation', 'Sustainability', 'Technology', 'Marketing', 'Product development', 'Customer service', 'Finance', 'Human resources', 'Sales']; // Define your tags here
    $campaigns = ['AWS', 'Cloud Migration', 'Digital Transformation','Customer Engagement','Cybersecurity Enhancement']; // Define your campaigns here
    ?>
    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <label for="project_name">Project Name:</label>
        <input type="text" id="project_name" name="project_name" required><br><br>

        <label for="project_timeline">Project Timeline:</label>
        <input type="text" id="project_timeline" name="project_timeline" required><br><br>

        <label for="project_campaign">Project Campaign:</label>
        <select id="project_campaign" name="project_campaign" required>
            <?php foreach ($campaigns as $campaign) : ?>
                <option value="<?php echo esc_attr($campaign); ?>"><?php echo esc_html($campaign); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="solution_type">Solution Type:</label>
        <input type="text" id="solution_type" name="solution_type" required><br><br>

        <label for="project_brief">Project Brief:</label>
        <textarea id="project_brief" name="project_brief" rows="4" cols="50" required></textarea><br><br>

        <label for="project_goal">Project Goal:</label>
        <textarea id="project_goal" name="project_goal" rows="4" cols="50" required></textarea><br><br>

        <label for="technical_considerations">Technical Considerations:</label>
        <input type="text" id="technical_considerations" name="technical_considerations" required><br><br>

        <label for="project_challenges">Project Challenges:</label>
        <textarea id="project_challenges" name="project_challenges" rows="4" cols="50" required></textarea><br><br>

        <label for="project_solutions">Project Solutions:</label>
        <textarea id="project_solutions" name="project_solutions" rows="4" cols="50" required></textarea><br><br>

        <label for="project_tags">Select Tags:</label><br>
        <?php foreach ($tags as $tag) : ?>
            <input type="checkbox" id="tag_<?php echo esc_attr($tag); ?>" name="project_tags[]" value="<?php echo esc_attr($tag); ?>">
            <label for="tag_<?php echo esc_attr($tag); ?>"><?php echo esc_html($tag); ?></label><br>
        <?php endforeach; ?><br>

        <input type="hidden" name="action" value="handle_project_form">
        <?php wp_nonce_field( 'handle_project_form_nonce', 'handle_project_form_nonce' ); ?>
        <input type="submit" value="Submit">
    </form>
    <?php
    return ob_get_clean(); // Return the captured HTML content
}

// Hook to display form shortcode [provider_form]
add_shortcode( 'project_form', 'project_form' );

// Function to handle form submission
function handle_project_form() {
    if ( isset( $_POST['handle_project_form_nonce'] ) && wp_verify_nonce( $_POST['handle_project_form_nonce'], 'handle_project_form_nonce' ) ) {
        global $wpdb;

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

//seeker form
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

function handle_seeker_form() {
    if ( isset( $_POST['handle_seeker_form_nonce'] ) && wp_verify_nonce( $_POST['handle_seeker_form_nonce'], 'handle_seeker_form_nonce' ) ) {
        global $wpdb;

        // Sanitize and validate input
        $seeker_company = sanitize_text_field( $_POST['seeker_company'] );
        $seeker_url = sanitize_text_field( $_POST['seeker_url'] );
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
                'Seeker_credits' => 0, // Example value, adjust as per your logic
            ),
            array(
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
                '%d', // Seeker_credits
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

// Shortcode to display project descriptions
function display_projects() {
    global $wpdb;

    $project_descriptions_table = $wpdb->prefix . 'project_description';
    
    // Initialize variables for search and filter
    $search_project_name = isset( $_GET['project_name'] ) ? sanitize_text_field( $_GET['project_name'] ) : '';
    $selected_campaign = isset( $_GET['campaign_type'] ) ? sanitize_text_field( $_GET['campaign_type'] ) : '';
    $selected_interests = isset( $_GET['interests'] ) ? $_GET['interests'] : array();

    // Build SQL query based on filters
    $sql = "SELECT * FROM $project_descriptions_table WHERE 1=1";
    
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
        <h2>Project Descriptions</h2>
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
                    <th>Project ID</th>
                    <th>Project Name</th>
                    <th>Project Timeline</th>
                    <th>Project Campaign</th>
                    <th>Actions</th>
                    <!-- Add more table headers as needed for other columns -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $project_descriptions as $project ) : ?>
                    <tr>
                        <td><?php echo esc_html( $project['Project_id'] ); ?></td>
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
add_shortcode( 'display_projects', 'display_projects' );


// Shortcode to display provider credits form
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
//view project page
function view_project() {
    if ( ! isset( $_GET['project_id'] ) ) {
        return '<p>Invalid access. Please select a project first.</p>';
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
        return '<p>Project not found.</p>';
    }
    
    // Retrieve related projects based on Project_Match table
    $related_projects = $wpdb->get_results( $wpdb->prepare(
        "SELECT pd.Project_id, pd.Project_name
         FROM $project_match_table pm
         JOIN $project_table pd ON pm.Project_id2 = pd.Project_id
         WHERE pm.Project_id1 = %d",
        $project_id
    ), ARRAY_A );

    // Output HTML for displaying project details
    ob_start();
    ?>
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
    return ob_get_clean();
}

// Register shortcode
add_shortcode( 'view_project_shortcode', 'view_project' );


// Shortcode to display checkout page
function checkout_page() {
    if ( !isset($_GET['credits']) ) {
        return '<p>Invalid access. Please select a credit amount first.</p>';
    }

    $credits = intval($_GET['credits']);
    $cost = ($credits / 1000) * 10;

    ob_start(); // Start output buffering to capture HTML
    ?>
    <h1>Checkout</h1>
    <form id="checkout-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <label for="provider_id">Provider ID:</label>
        <input type="text" id="provider_id" name="provider_id" required><br><br>

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
    </form>
    <?php
    return ob_get_clean(); // Return the captured HTML content
}
add_shortcode( 'checkout_page', 'checkout_page' );

// Function to handle checkout form submission
function handle_checkout_form() {
    if ( isset( $_POST['handle_checkout_nonce'] ) && wp_verify_nonce( $_POST['handle_checkout_nonce'], 'handle_checkout_nonce' ) ) {
        global $wpdb;

        // Sanitize and validate input
        $provider_id = sanitize_text_field( $_POST['provider_id'] );
        $credits_amount = intval( $_POST['credits_amount'] );

        // Perform credit addition logic
        $table_name = $wpdb->prefix . 'solution_providers';
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name SET Provider_credits = Provider_credits + %d WHERE Provider_id = %d",
                $credits_amount, 
                $provider_id 
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