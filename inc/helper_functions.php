<?php


function enqueue_bootstrap()
{
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <?php
}

add_action('admin_enqueue_scripts', 'enqueue_bootstrap', 50);
add_action('wp_enqueue_scripts', 'enqueue_bootstrap', 50);

function custom_theme_setup()
{
    // اضافه کردن پشتیبانی منو
    add_theme_support('menus');

    // ثبت منوها
    register_nav_menus(
        array(
            'primary' => __('Primary Menu', 'textdomain'),
            'footer' => __('Footer Menu', 'textdomain')
        )
    );
}
add_action('after_setup_theme', 'custom_theme_setup');


function my_enqueue_scripts()
{
    wp_enqueue_script('jquery'); // اطمینان از بارگذاری jQuery
    // wp_enqueue_script('my-custom-script', get_template_directory_uri() . '/js/my-custom-script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');


function add_shortcode_after_content($content)
{
    if (is_singular('events')) {

        ob_start();
        $nonce = wp_create_nonce('my_form_nonce');
        ?>
        <h1 class=""> رزرو</h1>
        <form action="<?php echo admin_url('admin-ajax.php'); ?>" id="regitration_form">
            <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">

            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div id="toastContainer" class="toast-container position-absolute bottom-0 start-0 p-3">
                    <!-- Toasts will be dynamically added here -->
                </div>
            </div>

            <div class=" d-flex flex-column gap-3 pb-5">

                <div class="border g-2 gap-3 py-3 rounded-3 row" style="box-shadow: 0 0.1rem 0.3rem rgba(0, 0, 0, 0.10); ">
                    <div class="row g-2">
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

                    <div class="row g-2">
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
                        <input type="hidden" name="event_id" value="<?php the_ID(); ?>">
                    </div>
                </div>

                <div class="row g-2" id="companionContainer"></div>

                <div class="row g-2">
                    <?php
                    $event_day = get_the_ID();
                    $all_event_days = get_event_days_by_event_id($event_day);
                    foreach ($all_event_days as $day):
                        $capacity_is_full = get_capacity_is_fill(get_the_ID(), $day->id);

                        ?>
                        <div class="col-lg-4  col-sm-6 pb-2">
                            <input type="radio" class="btn-check" name="event_day" value="<?php echo $day->id; ?>"
                                id="event_day<?php echo $day->id; ?>" autocomplete="off" checked="" <?php echo ($capacity_is_full) ? ' disabled ' : ''; ?>>
                            <label class="border btn d-flex overflow-hidden p-0 rounded-2 " for="event_day<?php echo $day->id; ?>">
                                <div
                                    class="col-4 <?php echo ($capacity_is_full) ? ' bg-danger ' : ' bg-success '; ?>  p-2 d-flex flex-warp flex-column justify-content-center align-content-center text-white">
                                    <span class=""
                                        style="padding:5px;font-size:20px;font-weight:normal;"><?php echo $day->title; ?></span>
                                </div>
                                <div class="col-8 d-flex flex-column flex-wrap justify-content-center gap-1 justify-content-end"
                                    style="padding: 5px;font-size: 12px;font-weight:normal;text-align: right;padding-right: 15px;color: #666;">
                                    <span><?php echo $day->start_time; ?></span>
                                    <span>وضعیت: <?php echo ($capacity_is_full) ? 'اتمام ظرفیت' : 'ظرفیت دارد'; ?></span>
                                    <span>مدت: <?php echo $day->duration; ?> دقیقه</span>
                                </div>
                            </label>
                        </div>
                        <?php
                    endforeach;
                    ?>

                </div>





                <div class="row g-2">
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
                                            <div class="row g-1 my-3 p-3 alert alert-success bg-light alert-dismissible fade show border " id="companion-${index}">
                                                <span class="mb-2 h4 text-success">همـراه</span>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="remove" onclick="removeCompanion(${index})"></button>

                                                <div class="col-12 form-floating pb-3">
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

        <style>
            .btn-check:checked+.btn,
            .btn.active,
            .btn.show,
            .btn:first-child:active,
            :not(.btn-check)+.btn:active {
                color: var(--bs-btn-active-color);
                background-color: var(--bs-btn-active-bg);
                border-color: var(--bs-secondary-color) !important;
                border-width: 1px !important;
            }
        </style>
        <?php
        return ob_get_clean();
    }
    // return $content;

}
add_filter('the_content', 'add_shortcode_after_content');