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
            mobile varchar(11),
            age tinyint(2),
            group_count tinyint(2),
            register_date datetime DEFAULT CURRENT_TIMESTAMP,
            parent_id mediumint(9),
            event_id mediumint(9) NOT NULL,
            event_day_id mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
            ) $charset_collate";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

        //Excute Query
        dbDelta($sql);

    endif;
}

// Insert Event Registration
function insert_event_registration($full_name, $sex, $code_meli, $mobile, $age, $group_count = 0, $register_date, $parent_id, $event_id,$event_day_id, $group_data)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'i8_event_registrations';

    // Insert Parent Data
    $result = $wpdb->insert(
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
            'event_day_id' => $event_day_id,
        )
    );

    // Insert Group Data
    if ($result != false && $group_count != 0):
        $parent_id = $wpdb->insert_id;
        error_log('here: sub group cjecker');
        foreach ($group_data as $key => $value):
            $sub_result = $wpdb->insert(
                $table_name,
                array(
                    'full_name' => $value['full_name'],
                    'sex' => $value['sex'],
                    'code_meli' => $value['code_meli'],
                    'mobile' => '',
                    'age' => $value['age'],
                    'parent_id' => $parent_id,
                    'event_id' => $event_id,
                    'event_day_id' => $event_day_id
                )
            );
            if (!$sub_result) {
                error_log('یک زیر گروه مشکل دارد');
                // ساخت کوئری برای حذف رکوردهایی که id یا parent_id آنها برابر با parent_id است
                $sql = $wpdb->prepare(
                    "DELETE FROM $table_name WHERE id = %d OR parent_id = %d",
                    $parent_id,
                    $parent_id
                );

                // اجرای کوئری
                $result = $wpdb->query($sql);

                return false;
            }
        endforeach;
        return true;
    elseif ($result != false && $group_count == 0):
        return true;
    elseif ($result == false):
        return false;
    endif;
}

add_action('wp_ajax_i8_proccess_registration_form_data', 'i8_proccess_registration_form_data');
add_action('wp_ajax_nopriv_i8_proccess_registration_form_data', 'i8_proccess_registration_form_data');

function i8_proccess_registration_form_data()
{
    // اطمینان از اعتبار nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_form_nonce')) {
        wp_send_json_error(['message' => 'Nonce نامعتبر است.']);
        wp_die();
    }

    error_log('post: ' . print_r($_POST, true));
    // ولیدیشن داده‌ها
    $full_name = sanitize_text_field($_POST['full_name']);
    $code_meli = sanitize_text_field($_POST['code_meli']);
    $age = sanitize_text_field($_POST['age']);
    $sex = sanitize_text_field($_POST['sex']);
    $mobile = sanitize_text_field($_POST['mobile']);
    $event_id = sanitize_text_field($_POST['event_id']);
    $event_day = sanitize_text_field($_POST['event_day']);

    // ولیدیشن‌های ساده
    if (empty($full_name) || empty($code_meli) || empty($age) || empty($mobile) || !preg_match('/^09\d{9}$/', $mobile)) {
        wp_send_json_error(['message' => 'لطفا تمام فیلدهای ضروری را با اطلاعات صحیح پر کنید.']);
        wp_die();
    }

    // // اینجا فرض می‌کنیم که تابع check_duplicate_code_meli وجود دارد که بررسی می‌کند کد ملی تکراری است یا خیر
    // if (check_duplicate_code_meli($code_meli)) {
    //     wp_send_json_error(['message' => 'کد ملی وارد شده تکراری است.']);
    //     wp_die();
    // }

    // جمع‌آوری اطلاعات همراهان
    // ایجاد آرایه خالی برای نگهداری اطلاعات همراهان
    $companions = [];

    // تعداد همراهان بر اساس تعداد المان‌های آرایه‌ها
    $companion_count = isset($_POST['companion_full_name']) ? count($_POST['companion_full_name']) : 0;
    error_log('companion_count: ' . $companion_count);

    error_log('code meli' . $code_meli);
    $res = is_registered($code_meli);
    error_log('res' . $res);

    if ($res > 0) {
        error_log('result' . $res);

        wp_send_json_error(['message' => 'کد ملی  ' . $code_meli . ' از قبل وجود دارد.']);
        wp_die();
        return;
    }
    // پردازش داده‌های هر همراه
    for ($i = 0; $i < $companion_count; $i++) {


        $sub_full_name = sanitize_text_field($_POST['companion_full_name'][$i]);
        $sub_code_meli = sanitize_text_field($_POST['companion_code_meli'][$i]);
        $sub_sex = sanitize_text_field($_POST['companion_sex'][$i]);
        $sub_age = sanitize_text_field($_POST['companion_age'][$i]);

        if (is_registered($sub_code_meli) > 0) {
            wp_send_json_error(['message' => 'کد ملی  ' . $sub_code_meli . ' از قبل وجود دارد.']);
            wp_die();
            return;
        }

        // افزودن همراه به آرایه $companions اگر تمام فیلدها پر شده باشند
        // if (!empty($full_name) && !empty($code_meli) && !empty($sex) && !empty($age)) {
        $companions[] = [
            'full_name' => $sub_full_name,
            'code_meli' => $sub_code_meli,
            'sex' => $sub_sex,
            'age' => $sub_age
        ];
        // }
    }


    error_log('group in sender: ' . print_r($companions, true));
    // ارسال اطلاعات به تابع ثبت
    $result = insert_event_registration($full_name, $sex, $code_meli, $mobile, $age, count($companions), current_time('mysql'), 0 , $event_id, $event_day, $companions);
    error_log($result);
    if ($result) {
        wp_send_json_success(['message' => 'فرم با موفقیت ارسال شد.']);
    } else {
        wp_send_json_error(['message' => 'خطا در ثبت اطلاعات.']);
    }

    wp_die();
}



function is_registered($code_meli)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'i8_event_registrations';

    // استفاده صحیح از $wpdb->prepare
    $sql = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE code_meli = %s", $code_meli);

    // استفاده از get_var برای دریافت یک مقدار
    $count = $wpdb->get_var($sql);

    // بررسی خطا
    if ($count === null) {
        // مدیریت خطا
        error_log("خطا در اجرای کوئری: " . $wpdb->last_error);
        return false;
    }

    return intval($count);


}
