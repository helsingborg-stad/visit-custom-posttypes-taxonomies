<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_63f8b99f12d0f',
    'title' => __('Visitor information', 'visit'),
    'fields' => array(
        0 => array(
            'key' => 'field_63f8b99f9d02e',
            'label' => __('Website', 'visit'),
            'name' => 'website',
            'aria-label' => '',
            'type' => 'website',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'website_title' => 1,
            'internal_link' => 0,
            'output_format' => 1,
            'default_value' => '',
        ),
        1 => array(
            'key' => 'field_63f8baae9d02f',
            'label' => __('Phone', 'visit'),
            'name' => 'phone',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'maxlength' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
        ),
        2 => array(
            'key' => 'field_63f8badb9d030',
            'label' => __('Public transport', 'visit'),
            'name' => 'public_transport',
            'aria-label' => '',
            'type' => 'textarea',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'acfe_textarea_code' => 0,
            'maxlength' => '',
            'rows' => '',
            'placeholder' => '',
            'new_lines' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'place',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
    'acfe_display_title' => '',
    'acfe_autosync' => '',
    'acfe_form' => 0,
    'acfe_meta' => '',
    'acfe_note' => '',
));
}