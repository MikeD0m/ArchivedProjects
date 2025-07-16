<?php
/*
Plugin Name: Michael's Trigger Plugin
Description: Creates triggers for project matching in WordPress database.
Version: 1.0
Author: Michael Dominguez
*/

// Hook to create triggers on plugin activation
register_activation_hook( __FILE__, 'project_triggers_activate' );

function project_triggers_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Trigger to match projects based on shared interests
    $sql_interests_trigger = "
    CREATE TRIGGER trg_match_interests AFTER INSERT ON wp_project_description
    FOR EACH ROW
    BEGIN
        DECLARE done INT DEFAULT 0;
        DECLARE existing_project_id INT;
        DECLARE cur CURSOR FOR 
            SELECT project_id 
            FROM wp_project_description
            WHERE project_id != NEW.project_id;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        OPEN cur;

        read_loop: LOOP
            FETCH cur INTO existing_project_id;
            IF done THEN
                LEAVE read_loop;
            END IF;

            IF EXISTS (
                SELECT 1
                FROM wp_project_interests pi1
                JOIN wp_project_interests pi2 ON pi1.interest_id = pi2.interest_id
                WHERE pi1.project_id = NEW.project_id 
                AND pi2.project_id = existing_project_id
            ) THEN
                IF NOT EXISTS (
                    SELECT 1
                    FROM wp_project_match
                    WHERE ((Project_id1 = NEW.project_id AND Project_id2 = existing_project_id)
                        OR (Project_id1 = existing_project_id AND Project_id2 = NEW.project_id))
                        AND (IsPartial = FALSE OR IsCampaign = FALSE)
                ) THEN
                    INSERT INTO wp_project_match (Project_id1, Project_id2, IsCampaign, IsPartial)
                    VALUES (NEW.project_id, existing_project_id, FALSE, TRUE);
                END IF;
            END IF;
        END LOOP;

        CLOSE cur;
    END;
    ";

    // Trigger to match projects based on shared campaign types
    $sql_campaigns_trigger = "
    CREATE TRIGGER trg_match_campaigns AFTER INSERT ON wp_project_description
    FOR EACH ROW
    BEGIN
        DECLARE done INT DEFAULT 0;
        DECLARE existing_project_id INT;
        DECLARE cur CURSOR FOR 
            SELECT project_id 
            FROM wp_project_description
            WHERE project_id != NEW.project_id;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        OPEN cur;

        read_loop: LOOP
            FETCH cur INTO existing_project_id;
            IF done THEN
                LEAVE read_loop;
            END IF;

            IF NEW.project_campaign = (
                SELECT project_campaign 
                FROM wp_project_description 
                WHERE project_id = existing_project_id
            ) THEN
                IF NOT EXISTS (
                    SELECT 1
                    FROM wp_project_match
                    WHERE ((Project_id1 = NEW.project_id AND Project_id2 = existing_project_id)
                        OR (Project_id1 = existing_project_id AND Project_id2 = NEW.project_id))
                        AND (IsPartial = FALSE OR IsCampaign = FALSE)
                ) THEN
                    INSERT INTO wp_project_match (Project_id1, Project_id2, IsCampaign, IsPartial)
                    VALUES (NEW.project_id, existing_project_id, TRUE, FALSE);
                END IF;
            END IF;
        END LOOP;

        CLOSE cur;
    END;
    ";

    // Include WordPress upgrade.php for dbDelta
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Execute the queries using dbDelta
    $result1 = dbDelta( $sql_interests_trigger );
    $result2 = dbDelta( $sql_campaigns_trigger );

    // Check and log any errors
    if ( is_wp_error( $result1 ) ) {
        error_log( 'Failed to create trg_match_interests trigger: ' . $result1->get_error_message() );
    }
    if ( is_wp_error( $result2 ) ) {
        error_log( 'Failed to create trg_match_campaigns trigger: ' . $result2->get_error_message() );
    }
}
