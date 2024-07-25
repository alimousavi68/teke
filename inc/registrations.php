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
            PRIMARY KEY  (id)
            ) $charset_collate";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

        //Excute Query
        dbDelta($sql);

    endif;
}

// Insert Event Registration
function insert_event_registration($full_name, $sex, $code_meli, $mobile, $age, $group_count = 0, $register_date, $parent_id, $event_id, $group_data)
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
        )
    );

    error_log('parent_id : ' . $parent_id);
    error_log('group_data: ' . print_r($group_data, true));
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
                    'event_id' => $event_id
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
    elseif ($parent_id != false):
        return true;
    else:
        error_log('خطا در ثبت رزرو');
        return false;
    endif;
}


add_shortcode('i8_event_regostration', 'render_event_regostration_form');
function render_event_regostration_form($atts)
{
    ob_start();
    $nonce = wp_create_nonce('my_form_nonce');
    ?>

    <form action="<?php echo admin_url('admin-ajax.php'); ?>" id="regitration_form">
        <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">

        <div aria-live="polite" aria-atomic="true" class="position-relative">
            <div id="toastContainer" class="toast-container position-absolute bottom-0 start-0 p-3">
                <!-- Toasts will be dynamically added here -->
            </div>
        </div>

        <div class="row g-1">
            <div class="row g-1">
                <div class="col-12 col-md-6 form-floating">
                    <input type="text" name="full_name" id="full_name" class="form-control"
                        placeholder="نام و نام خانوادگی">
                    <label for="full_name">نام و نام خانوادگی: </label>
                </div>
                <div class="col-12 col-md-6 form-floating">
                    <input type="text" name="code_meli" id="code_meli" class="form-control" placeholder="1">
                    <label for="code_meli">کد ملی: </label>
                </div>
            </div>
            <div class="row g-1">
                <div class="col-12 col-md-4 form-floating">
                    <input type="number" min="1" max="100" name="age" id="age" class="form-control" placeholder="10">
                    <label for="age">سـن: </label>
                </div>
                <div class="col-12 col-md-4 form-floating">
                    <select name="sex" id="sex" class="form-select">
                        <option value="0">خانم</option>
                        <option value="1" selected>آقا</option>
                    </select>
                    <label for="sex">جنسیت: </label>
                </div>
                <div class="col-12 col-md-4 form-floating">
                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="09121234567">
                    <label for="mobile">شماره همراه: </label>
                </div>
            </div>

            <div id="companionContainer" class="p-2"></div>
            <div class="row g-1">
                <div class="col-12 col-lg-6">
                    <button type="button" class="btn btn-outline-success btn-lg w-100" onclick="addCompanion()">اضافه کردن
                        همراه</button>
                </div>
                <div class="col-12 col-lg-6">
                    <button id="i8_submit_send" class="btn btn-success btn-lg w-100" type="submit">
                        ثبت نام
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        // Add Companion Item
        function addCompanion() {
            const container = document.getElementById('companionContainer');
            const index = container.children.length;

            const html = `
                                                            <div class="row g-1 my-3 p-3 alert alert-success bg-light alert-dismissible fade show border border-success" id="companion-${index}">
                                                                <span class="mb-2 h4 text-success">همـراه</span>
                                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="remove" onclick="removeCompanion(${index})"></button>

                                                                <div class="col-12 form-floating">
                                                                    <input type="text" name="companion_full_name[${index}]" class="form-control" placeholder="نام و نام خانوادگی همراه">
                                                                    <label>نام و نام خانوادگی همراه: </label>
                                                                </div>
                                                                <div class="col-12 col-md-4 form-floating">
                                                                    <input type="text" name="companion_code_meli[${index}]" class="form-control" placeholder="کد ملی همراه">
                                                                    <label>کد ملی همراه: </label>
                                                                </div>
                                                                <div class="col-12 col-md-4 form-floating">
                                                                    <select name="companion_sex[${index}]" class="form-select">
                                                                        <option value="0">خانم</option>
                                                                        <option value="1">آقا</option>
                                                                    </select>
                                                                    <label>جنسیت همراه: </label>
                                                                </div>
                                                                <div class="col-12 col-md-4 form-floating">
                                                                    <input type="number" min="1" max="100" name="companion_age[${index}]" class="form-control" placeholder="سن همراه">
                                                                    <label>سـن همراه: </label>
                                                                </div>
                                                            </div>
                                                        `;

            container.insertAdjacentHTML('beforeend', html);
        }

        // Remove Companion Item
        function removeCompanion(index) {
            const element = document.getElementById(`companion-${index}`);
            element.parentNode.removeChild(element);
        }
    </script>

    <script type="text/javascript">
        // تعریف ajaxurl مستقیماً در اسکریپت
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        jQuery(document).ready(function ($) {
            $('#regitration_form').submit(function (event) {
                event.preventDefault();
                var isValid = true;
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                // ولیدیشن نام
                if ($('#full_name').val().trim() === '') {
                    $('#full_name').addClass('is-invalid');
                    $('#full_name').after('<div class="invalid-feedback">لطفاً نام و نام خانوادگی را وارد کنید.</div>');
                    isValid = false;
                }

                // ولیدیشن کد ملی
                var codeMeli = $('#code_meli').val().trim();
                if (codeMeli === '' || codeMeli.length !== 10 || !codeMeli.match(/^\d{10}$/)) {
                    $('#code_meli').addClass('is-invalid');
                    $('#code_meli').after('<div class="invalid-feedback">کد ملی باید دقیقاً ۱۰ رقم باشد و فقط شامل اعداد باشد.</div>');
                    isValid = false;
                }

                // ولیدیشن سن
                if ($('#age').val().trim() === '') {
                    $('#age').addClass('is-invalid');
                    $('#age').after('<div class="invalid-feedback">سن را وارد کنید.</div>');
                    isValid = false;
                }

                // ولیدیشن شماره همراه
                if ($('#mobile').val().trim() === '' || !$('#mobile').val().trim().match(/^09\d{9}$/)) {
                    $('#mobile').addClass('is-invalid');
                    $('#mobile').after('<div class="invalid-feedback">شماره همراه معتبر نیست.</div>');
                    isValid = false;
                }

                // ولیدیشن المان‌های همراه
                $('#companionContainer').find('.form-control').each(function () {
                    var input = $(this);
                    var name = input.attr('name');
                    if (input.val().trim() === '') {
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">این فیلد نباید خالی باشد.</div>');
                        isValid = false;
                    }
                    if (name.includes('code_meli') && (input.val().length !== 10 || !input.val().match(/^\d{10}$/))) {
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">کد ملی باید دقیقاً ۱۰ رقم باشد.</div>');
                        isValid = false;
                    }
                });

                if (!isValid) {
                    return;
                }

                $('#i8_submit_send').html("<span class='spinner-border spinner-border-sm' aria-hidden='true'></span><span role='status'>در حال پردازش‭...</span>");
                $('#i8_submit_send').prop('disabled', true);

                var formData = $(this).serializeArray();
                formData.push({ name: 'action', value: 'i8_proccess_registration_form_data' });

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            showToast('success', response.data.message, '#toastContainer');
                            $('#i8_submit_send').html("ثبت نام");
                            $('#i8_submit_send').prop('disabled', false);
                        } else {
                            showToast('danger', response.data.message, '#toastContainer');
                            $('#i8_submit_send').html("ثبت نام");
                            $('#i8_submit_send').prop('disabled', false);
                        }
                    },
                    error: function (response) {
                        showToast('danger', 'خطا در برقراری ارتباط با سرور.', '#toastContainer');
                        $('#i8_submit_send').html("ثبت نام‭");
                        $('#i8_submit_send').prop('disabled', false);
                    }
                });
            });

            function showToast(type, message, container) {
                var toastHTML = `<div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                                    <div class="d-flex">
                                                        <div class="toast-body">${message}</div>
                                                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                                    </div>
                                                </div>`;

                $(container).append(toastHTML);
                var toastEl = $(container + ' .toast').last();
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });



    </script>

    <?php
    return ob_get_clean();

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
        if (is_registered($sub_code_meli) > 0) {
            wp_send_json_error(['message' => 'کد ملی  ' . $sub_code_meli . ' از قبل وجود دارد.']);
            wp_die();
            return;
        }

        $sub_full_name = sanitize_text_field($_POST['companion_full_name'][$i]);
        $sub_code_meli = sanitize_text_field($_POST['companion_code_meli'][$i]);
        $sub_sex = sanitize_text_field($_POST['companion_sex'][$i]);
        $sub_age = sanitize_text_field($_POST['companion_age'][$i]);

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
    $result = insert_event_registration($full_name, $sex, $code_meli, $mobile, $age, count($companions), current_time('mysql'), get_current_user_id(), 1, $companions);
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

$sql = $wpdb->prepare(
    "SELECT COUNT(*) FROM $table_name WHERE code_meli = $code_meli"
    
);

    // اجرای کوئری
    $result = $wpdb->query($sql);
    return $result;
}
