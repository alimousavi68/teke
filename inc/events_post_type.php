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
        'supports' => array('title', 'custom-fields'),
        'menu_position' => 3,
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'capability_type' => 'page',
        'public' => false,  // این باعث می‌شود که پست‌ها در فرانت سایت نمایش داده نشوند
        'publicly_queryable' => false,  // این گزینه جلوی دسترسی عمومی به این نوع پست را می‌گیرد
        'show_ui' => true,   // نمایش در بخش مدیریت
        'show_in_menu' => true,   // نمایش در منوی مدیریت
        'query_var' => false,  // جلوگیری از استفاده از query vars برای دسترسی به پست‌ها
        'rewrite' => false,  // غیرفعال کردن rewrite rules
        'has_archive' => false,  // غیرفعال کردن بایگانی برای این نوع پست
        'exclude_from_search' => true  // این پست‌ها در جستجوهای سایت نمایش داده نمی‌شوند
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
    ?>


    <div class="row p-2">
        <div class="col-xl-4 col-12 ">
            <label for="floatingTextarea">تاریخ برگزاری </label>
            <input type="date" name="i8_event_date" id="" value="<?php echo $i8_event_date; ?>"
                class="form-control form-control-lg">
        </div>
    </div>
    <div class="row p-2">
        <div class="col-xl-4 col-12 ">
            <label for="floatingTextarea">ظرفیت برگزاری</label>
            <input type="number" name="i8_event_capacity" id="" min="0" value="<?php echo $i8_event_capacity; ?>"
                class="form-control form-control-lg">
        </div>
    </div>
    <div class="row p-2">
        <div class="col-12">
            <label for="floatingTextarea2">توضیحات</label>
            <textarea class="form-control" name="i8_event_descriptions" placeholder="توضیحات رویداد را در اینجا وارد کنید"
                style="height: 200px"><?php echo $i8_event_descriptions; ?></textarea>
        </div>
    </div>

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