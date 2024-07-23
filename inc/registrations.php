<?php

// Create Event Registreations Table 
add_action('admin_init', 'create_event_registration_table');
function create_event_registration_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'i8_event_registrations';
    $charset_collate = $wpdb->get_charset_collate();

    // If Table Not Exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name):
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            full_name varchar(50) NOT NULL,
            sex tinyint(1),
            code_meli varchar(10) NOT NULL UNIQUE,
            mobile varchar(11) UNIQUE,
            age tinyint(2),
            group_count tinyint(2),
            register_date datetime DEFAULT CURRENT_TIMESTAMP,
            parent_id mediumint(9),
            event_id mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
            ) $charset_collate";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

        //Excute Query
        dbDelta($sql);

    endif;
}

// Insert Event Registration
function insert_event_registeration($full_name, $sex, $code_meli, $mobile, $age, $group_count, $register_date, $parent_id, $event_id, $group_data)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'i8_event_registrations';

    // Insert Parent Data
    $parent_id = $wpdb->insert(
        $table_name,
        array(
            'full_name' => $full_name,
            'sex' => $sex,
            'code_meli' => $code_meli,
            'mobile' => $mobile,
            'age' => $age,
            'group_count' => $group_count,
            'register_date' => $register_date,
            'parent_id' => $parent_id,
            'event_id' => $event_id,
        )
    );

    // Insert Group Data
    if ($parent_id!= false && $group_data != null):
        foreach ($group_data as $key => $value):
            $wpdb->insert(
                $table_name,
                array(
                    'full_name' => $value['full_name'],
                    'sex' => $value['sex'],
                    'code_meli' => $value['code_meli'],
                    'mobile' => 0 ,
                    'age' => $value['age'],
                    'parent_id' => $parent_id,
                    'event_id' => $event_id
                )
            );
        endforeach;
    endif;
}

insert_event_registeration('علی موسوی', 1, '0011223344', '09123456789', 20, 2, '2021-07-10 10:00:00', 0, 1, null);
