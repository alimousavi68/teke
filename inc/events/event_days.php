<?php 
//  Event Days

//Create Event days table
function create_events_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'events';

    // بررسی می‌کنیم که آیا جدول قبلاً وجود دارد یا خیر
    if($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
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

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Hook the function to WordPress 'init'
add_action('init', 'create_events_table');


// Insert Event New Day
function insert_event($event_id, $title, $capacity, $date, $start_time, $duration) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'events';

    $wpdb->insert(
        $table_name,
        array(
            'event_id' => $event_id,
            'title' => $title,
            'capacity' => $capacity,
            'date' => $date,
            'start_time' => $start_time,
            'duration' => $duration  // تغییر داده شده
        ),
        array('%d', '%s', '%d', '%s', '%s', '%d')  // تغییر داده شده
    );
}

// Update A Event Day by id
function update_event($id, $event_id, $title, $capacity, $date, $start_time, $duration) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'events';

    $wpdb->update(
        $table_name,
        array(
            'event_id' => $event_id,
            'title' => $title,
            'capacity' => $capacity,
            'date' => $date,
            'start_time' => $start_time,
            'duration' => $duration  // اکنون به عنوان عدد صحیح ذخیره می‌شود
        ),
        array('id' => $id),
        array('%d', '%s', '%d', '%s', '%s', '%d'),  // تغییر فرمت duration به عدد صحیح
        array('%d')
    );
}


// Delete A Event Day by id
function delete_event($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'events';

    $wpdb->delete($table_name, array('id' => $id), array('%d'));
}

// Select All Event Days by Event_id
function get_events_by_event_id($event_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'events';

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE event_id = %d",
        $event_id
    ));

    return $results;
}

