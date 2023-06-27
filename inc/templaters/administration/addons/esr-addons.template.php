<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ESR_Addons_Template {

    const MENU_SLUG = 'esr_admin_sub_page_addons';

    public static function print_page() {
        ?>
        <div class="wrap esr-addons esr-students">
        </div>
        <?php
    }
}