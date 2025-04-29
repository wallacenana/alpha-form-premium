<?php
defined('ABSPATH') || exit;

function afp_is_license_valid() {
    $status = get_option('alpha_form_license_status');
    $expires = get_option('alpha_form_license_expires');

    if ($status !== 'valid') return false;
    if ($expires && strtotime($expires) < time()) return false;

    return true;
}
