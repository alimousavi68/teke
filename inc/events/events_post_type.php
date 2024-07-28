<?php


// Register Post Type Events
add_action('init', 'events_post_type');
function events_post_type()
{
    $lable = array(
        'name' => _x('رویداد‌ها', 'Post Type General Name', 'i8_publisher_copilot'),
        'singular_name' => _x('رویداد', 'Post Type Singular Name', 'i8_publisher_copilot'),
        'menu_name' => __('رویداد‌ها', 'i8_publisher_copilot'),
        'name_admin_bar' => __('رویداد‌ها', 'i8_publisher_copilot'),
        'archives' => __('آرشیو رویداد‌ها', 'i8_publisher_copilot'),
        'attributes' => __('خصوصیات رویداد‌ها', 'i8_publisher_copilot'),
        'parent_item_colon' => __('مادر', 'i8_publisher_copilot'),
        'all_items' => __('همه رویداد‌ها', 'i8_publisher_copilot'),
        'add_new_item' => __('افزودن رویداد', 'i8_publisher_copilot'),
        'add_new' => __('افزودن جدید', 'i8_publisher_copilot'),
        'new_item' => __('رویداد‌ جدید', 'i8_publisher_copilot'),
        'edit_item' => __('ویرایش رویداد', 'i8_publisher_copilot'),
        'update_item' => __('به روزرسانی رویداد', 'i8_publisher_copilot'),
        'view_item' => __('نمایش رویداد', 'i8_publisher_copilot'),
        'view_items' => __('نمایش رویداد‌ها', 'i8_publisher_copilot'),
        'search_items' => __('جستجوی رویداد', 'i8_publisher_copilot'),
        'not_found' => __('پیدا نشد', 'i8_publisher_copilot'),
        'not_found_in_trash' => __('در زباله دان پیدا نشد', 'i8_publisher_copilot'),
        'insert_into_item' => __('درج در رویداد', 'i8_publisher_copilot'),
        'uploaded_to_this_item' => __('در این رویدادآپلود شد', 'i8_publisher_copilot'),
        'items_list' => __('لیست رویداد‌ها', 'i8_publisher_copilot'),
        'items_list_navigation' => __('پیمایش فهرست رویداد‌ها', 'i8_publisher_copilot'),
        'filter_items_list' => __('لیست رویداد‌ها را فیلتر کنید', 'i8_publisher_copilot'),
    );
    $args = array(
        'label' => 'رویداد ها',
        'description' => 'در این بخش رویداد ها را میتوانید ایجاد و مدیریت کنید',
        'labels' => $lable,
        'supports' => array('title', 'custom-fields','thumbnail','excerpt'),
        'menu_position' => 3,
        'taxonomies'  => array('category'),
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'capability_type' => 'post',
        'public' => true,  // این باعث می‌شود که پست‌ها در فرانت سایت نمایش داده نشوند
        'publicly_queryable' => true,  // این گزینه جلوی دسترسی عمومی به این نوع پست را می‌گیرد
        'show_ui' => true,   // نمایش در بخش مدیریت
        'show_in_menu' => true,   // نمایش در منوی مدیریت
        'query_var' => true,  // جلوگیری از استفاده از query vars برای دسترسی به پست‌ها
        'rewrite' => true,  // غیرفعال کردن rewrite rules
        'has_archive' => true,  // غیرفعال کردن بایگانی برای این نوع پست
        'exclude_from_search' => false  // این پست‌ها در جستجوهای سایت نمایش داده نمی‌شوند
    );

    register_post_type('events', $args);
}



// Register Post Meta Box for Evenets
add_action('add_meta_boxes', 'display_events_post_meta');
function display_events_post_meta($post)
{
    add_meta_box('i8_event_post_meta', 'جزییات رویداد', 'render_custom_meta_fields', 'events', 'normal', 'high');
}

// Render Custom Meta Fields
function render_custom_meta_fields($post)
{
    $i8_event_date = get_post_meta($post->ID, 'i8_event_date', true);
    $i8_event_capacity = get_post_meta($post->ID, 'i8_event_capacity', true);
    $i8_event_descriptions = get_post_meta($post->ID, 'i8_event_descriptions', true);

    $event_days = get_event_days_by_event_id(get_the_ID());



    ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .day-box {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 10px;
            position: relative;
            border-radius: 10px;
            transition: opacity 0.5s;
            opacity: 0;
        }

        .close-button,
        .toggle-button {
            position: absolute;
            top: 25px;
            cursor: pointer;
        }

        .close-button {
            left: 10px;
        }

        .toggle-button {
            left: 50px;
        }

        .fixed-buttons {
            position: fixed;
            bottom: 10px;
            right: 10px;
            left: 10px;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            z-index: 1000;
            border-radius: 10px;
        }

        .container {
            padding-bottom: 100px;
        }

        .form-floating>.form-control {
            height: auto;
        }

        .day-title {
            color: #6c757d;
            /* Bootstrap's secondary color */
        }

        .day-title i {
            font-size: 0.8rem;
            margin-right: 5px;
        }

        .control-buttons {
            color: #000;
            /* Black color for buttons */
        }

        .btn-primary {
            background-color: #198754;
            /* Bootstrap's success color */
        }

        .spinner-border {
            color: #dc3545;
            /* Bootstrap's danger color */
        }
    </style>

    <div class="mb-2">
        <label for="floatingTextarea2">توضیحات</label>
        <textarea class="form-control" name="i8_event_descriptions" placeholder="توضیحات رویداد را در اینجا وارد کنید"
            style="height: 200px"><?php echo $i8_event_descriptions; ?></textarea>
    </div>
    <div class="">
        <div class="d-flex flex-row-reverse justify-content-between align-items-center mb-3">
            <div>
                <button class="btn btn-outline-dark control-buttons" onclick="expandAll()">
                    <i class="fas fa-expand-arrows-alt"></i>
                </button>
                <button class="btn btn-outline-dark control-buttons" onclick="collapseAll()">
                    <i class="fas fa-compress-arrows-alt"></i>
                </button>
            </div>
            <h1 class="text-end">روزهای رویداد</h1>
        </div>
        <div class="fixed-buttons">
            <button type="button" class="btn btn-primary" onclick="addDay()">
                <i class="fas fa-plus"></i> افزودن روز
                <div class="spinner-border spinner-border-sm" role="status" style="display: none;"></div>
            </button>
        </div>
        <form id="eventForm">
            <div id="daysContainer">
                <?php if (isset($event_days)):
                    foreach ($event_days as $index => $event_day):
                        ?>
                        <div class="day-box opacity-100">
                            <div class="close-button btn btn-danger" onclick="removeDay(this)"><i class="fas fa-times"></i></div>
                            <div class="toggle-button btn btn-secondary" onclick="toggleContent(this)"><i
                                    class="fas fa-chevron-down"></i></div>
                            <h4 class="text-start day-title"><i class="fas fa-calendar-day"></i> <?php echo $event_day->title; ?>
                            </h4>
                            <div class="content row">
                                <input type="hidden" name="event_days[<?php echo $index; ?>][id]"
                                    value="<?php echo $event_day->id; ?>">

                                <div class="col-6 form-floating mb-3">
                                    <input type="text" class="form-control" id="event_days[<?php echo $index; ?>][title]"
                                        name="event_days[<?php echo $index; ?>][title]" placeholder="عنوان"
                                        value="<?php echo $event_day->title; ?>">
                                    <label for="event_days[<?php echo $index; ?>][title]">عنوان</label>
                                </div>

                                <div class="col-6 form-floating mb-3">
                                    <input type="number" class="form-control" id="event_days[<?php echo $index; ?>][capacity]"
                                        name="event_days[<?php echo $index; ?>][capacity]" placeholder="ظرفیت"
                                        value="<?php echo $event_day->capacity; ?>" required>
                                    <label for="event_days[<?php echo $index; ?>][capacity]">ظرفیت</label>
                                </div>

                                <div class="col-4 form-floating mb-3">
                                    <input type="date" class="form-control" id="event_days[<?php echo $index; ?>][date]"
                                        name="event_days[<?php echo $index; ?>][date]" value="<?php echo $event_day->date; ?>"
                                        required>
                                    <label for="event_days[<?php echo $index; ?>][date]">تاریخ</label>
                                </div>

                                <div class="col-4 form-floating mb-3">
                                    <input type="time" class="form-control" id="event_days[<?php echo $index; ?>][start_time]"
                                        name="event_days[<?php echo $index; ?>][start_time]"
                                        value="<?php echo $event_day->start_time; ?>" required>
                                    <label for="event_days[<?php echo $index; ?>][start_time]">زمان شروع</label>
                                </div>
                                <div class="col-4 form-floating mb-3">
                                    <input type="number" class="form-control" id="event_days[<?php echo $index; ?>][duration]"
                                        name="event_days[<?php echo $index; ?>][duration]"
                                        value="<?php echo $event_day->duration; ?>" required>
                                    <label for="event_days[<?php echo $index; ?>][duration]">مدت زمان (دقیقه)</label>
                                </div>
                            </div>
                        </div>
                        <?php
                    endforeach;
                endif; ?>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let dayCount = document.getElementsByClassName('day-box').length;

        function addDay() {
            const spinner = document.querySelector('.spinner-border');
            spinner.style.display = 'inline-block';
            setTimeout(() => {
                dayCount++;
                const dayBox = document.createElement('div');
                dayBox.classList.add('day-box');
                dayBox.innerHTML = `
                                                            <div class="close-button btn btn-danger" onclick="removeDay(this)"><i class="fas fa-times"></i></div>
                                                            <div class="toggle-button btn btn-secondary" onclick="toggleContent(this)"><i class="fas fa-chevron-down"></i></div>
                                                            <h4 class="text-start day-title"><i class="fas fa-calendar-day"></i> روز ${dayCount}</h4>
                                                            <div class="content row">
                                                                <div class="col-6 form-floating mb-3">
                                                                    <input type="text" class="form-control" id="event_days[${dayCount}][title]" name="event_days[${dayCount}][title]" placeholder="عنوان">
                                                                    <label for="event_days[${dayCount}][title]">عنوان</label>
                                                                </div>
                                                                <div class="col-6 form-floating mb-3">
                                                                    <input type="number" class="form-control" id="event_days[${dayCount}][capacity]" name="event_days[${dayCount}][capacity]" placeholder="ظرفیت" required>
                                                                    <label for="event_days[${dayCount}][capacity]">ظرفیت</label>
                                                                </div>
                                                                <div class="col-4 form-floating mb-3">
                                                                    <input type="date" class="form-control" id="event_days[${dayCount}][date]" name="event_days[${dayCount}][date]" required>
                                                                    <label for="event_days[${dayCount}][date]">تاریخ</label>
                                                                </div>
                                                                <div class="col-4 form-floating mb-3">
                                                                    <input type="time" class="form-control" id="event_days[${dayCount}][start_time]" name="event_days[${dayCount}][start_time]" required>
                                                                    <label for="event_days[${dayCount}][start_time]">زمان شروع</label>
                                                                </div>
                                                                <div class="col-4 form-floating mb-3">
                                                                    <input type="number" class="form-control" id="event_days[${dayCount}][duration]" name="event_days[${dayCount}][duration]" required>
                                                                    <label for="event_days[${dayCount}][duration]">مدت زمان (دقیقه)</label>
                                                                </div>
                                                            </div>
                                                        `;
                document.getElementById('daysContainer').appendChild(dayBox);
                dayBox.style.opacity = 1;
                dayBox.scrollIntoView({ behavior: 'smooth' });
                spinner.style.display = 'none';
            }, 500);
        }

        function removeDay(button) {
            button.parentElement.style.opacity = 0;
            setTimeout(() => {
                button.parentElement.remove();
                updateDayNumbers();
            }, 500);
        }

        function updateDayNumbers() {
            const dayBoxes = document.querySelectorAll('.day-box');
            dayBoxes.forEach((box, index) => {
                box.querySelector('h4').textContent = `روز ${index + 1}`;
            });
        }

        function toggleContent(button) {
            const content = button.parentElement.querySelector('.content');
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
            button.innerHTML = content.style.display === 'none' ? '<i class="fas fa-chevron-up"></i>' : '<i class="fas fa-chevron-down"></i>';
        }

        function collapseAll() {
            document.querySelectorAll('.content').forEach(content => {
                content.style.display = 'none';
                content.previousElementSibling.querySelector('.toggle-button').innerHTML = '<i class="fas fa-chevron-up"></i>';
            });
        }

        function expandAll() {
            document.querySelectorAll('.content').forEach(content => {
                content.style.display = 'block';
                content.previousElementSibling.querySelector('.toggle-button').innerHTML = '<i class="fas fa-chevron-down"></i>';
            });
        }

        function showSpinner() {
            const spinner = document.querySelector('.spinner-border');
            spinner.style.display = 'block';
        }

        function hideSpinner() {
            const spinner = document.querySelector('.spinner-border');
            spinner.style.display = 'none';
        }
    </script>





    <?php
}


add_action('save_post', 'save_custom_meta_fields');

function save_custom_meta_fields($post_id)
{

    // If Auto Saving Dont Save
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // Save Event Date to Db
    if (isset($_POST['i8_event_date'])) {
        update_post_meta($post_id, 'i8_event_date', sanitize_text_field($_POST['i8_event_date']));
    }

    // Save Event Capacity to Db
    if (isset($_POST['i8_event_capacity'])) {
        update_post_meta($post_id, 'i8_event_capacity', sanitize_text_field($_POST['i8_event_capacity']));
    }

    // Save Event Description to Db
    if (isset($_POST['i8_event_descriptions'])) {
        update_post_meta($post_id, 'i8_event_descriptions', sanitize_textarea_field($_POST['i8_event_descriptions']));
    }

}


