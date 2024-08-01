<?php
//  Event Days

//Create Event days table
function create_event_days_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_days';

    // بررسی می‌کنیم که آیا جدول قبلاً وجود دارد یا خیر
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            event_id mediumint(9) NOT NULL,
            title text DEFAULT '' NOT NULL,
            capacity mediumint(9) NOT NULL,
            date date NOT NULL,
            start_time time NOT NULL,
            duration mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Hook the function to WordPress 'init'
add_action('init', 'create_event_days_table');



// Assuming the 'insert_event_day' and 'update_event_day' functions are defined as in your previous example
function insert_event_day($event_id, $title, $capacity, $date, $start_time, $duration)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_days';

    $wpdb->insert(
        $table_name,
        array(
            'event_id' => $event_id,
            'title' => $title,
            'capacity' => $capacity,
            'date' => $date,
            'start_time' => $start_time,
            'duration' => $duration
        ),
        array('%d', '%s', '%d', '%s', '%s', '%d')
    );
}

function update_event_day($id, $event_id, $title, $capacity, $date, $start_time, $duration)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_days';

    $wpdb->update(
        $table_name,
        array(
            'event_id' => $event_id,
            'title' => $title,
            'capacity' => $capacity,
            'date' => $date,
            'start_time' => $start_time,
            'duration' => $duration
        ),
        array('id' => $id),
        array('%d', '%s', '%d', '%s', '%s', '%d'),
        array('%d')
    );
}

function delete_event_day($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_days';

    $wpdb->delete($table_name, array('id' => $id), array('%d'));
}

function get_event_days_by_event_id($event_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_days';

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE event_id = %d",
            $event_id
        )
    );

    return $results;
}



// Hook into WordPress save_post action
add_action('save_post_events', 'save_event_days_data', 10, 3);

// Assuming data is sent via POST in an array with 'event_days' as key
function save_event_days_data($post_id, $post, $update)
{
    // Check if it's not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // Check if the user has permissions to save data.
    if (!current_user_can('edit_post', $post_id))
        return;

    // Check if it's not a revision
    if (wp_is_post_revision($post_id))
        return;

    if (!isset($_POST['event_days']) || !is_array($_POST['event_days'])) {
        return;
    }

    $submitted_days = $_POST['event_days'];
    $existing_days = get_event_days_by_event_id($post_id);

    foreach ($existing_days as $day) {
        if (!in_array($day->id, array_column($submitted_days, 'id'))) {
            delete_event_day($day->id);
        }
    }

    foreach ($submitted_days as $day) {
        if (empty($day['id'])) {
            insert_event_day($post_id, $day['title'], $day['capacity'], $day['date'], $day['start_time'], $day['duration']);
        } else {
            update_event_day($day['id'], $post_id, $day['title'], $day['capacity'], $day['date'], $day['start_time'], $day['duration']);
        }
    }
}


function get_capacity_is_fill($event_id, $event_day_id)
{
    global $wpdb;
    // event day capacity
    $event_day_table_name = $wpdb->prefix . 'event_days';
    $event_day_query = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $event_day_table_name WHERE event_id = %d AND id = %d",
            $event_id,
            $event_day_id
        )
    );

    if (!empty($event_day_query)) {
        $event_day_capacity = $event_day_query[0]->capacity;
    } else {
        $event_day_capacity = 0;
    }

    error_log('capacity for a day: ' . print_r($event_day_capacity, true));

    // refisteration person in a event id and event day
    $registration_table_name = $wpdb->prefix . 'i8_event_registrations';
    $count_registration_for_a_event_day = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $registration_table_name WHERE event_id = %d AND event_day_id = %d",
            $event_id,
            $event_day_id
        )
    );

    error_log('resiteriation for a day: ' . $count_registration_for_a_event_day);

    // compare capacity with count of resitration

    if ($count_registration_for_a_event_day >= $event_day_capacity) {
        return true;
    } else {
        return false;
    }
}










