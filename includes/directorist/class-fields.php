<?php

/**
 * @author  mahfuz
 * @since   1.0
 * @version 1.0
 */

class MPP_Directorist_Fields
{

    public function __construct()
    {
        add_filter('atbdp_form_custom_widgets', array($this, 'atbdp_form_custom_widgets'));
        add_filter('atbdp_single_listing_content_widgets', array($this, 'atbdp_single_listing_content_widgets'));
        add_filter('directorist_field_template', array($this, 'directorist_field_template'), 10, 2);
        add_filter('directorist_single_item_template', array($this, 'directorist_single_item_template'), 10, 2);
    }

    public function atbdp_form_custom_widgets($widgets)
    {
        $widgets['mpp_vacancy'] = array(
            'label' => 'Vacancy',
            'icon' => 'uil uil-arrow',
            'options' => [
                'type' => [
                    'type'  => 'hidden',
                    'value' => 'mpp_vacancy',
                ],
                'label' => [
                    'type'  => 'text',
                    'label' => __('Label', 'directorist'),
                    'value' => 'Vacancy',
                ],
                'field_key' => apply_filters('directorist_custom_field_meta_key_field_args', [
                    'type'  => 'hidden',
                    'label' => __('Key', 'directorist'),
                    'value' => 'mpp-vacancy',
                    'rules' => [
                        'unique' => true,
                        'required' => true,
                    ]
                ]),
                'options' => [
                    'type' => 'multi-fields',
                    'label' => __('Options', 'directorist'),
                    'add-new-button-label' => __('Add Option', 'directorist'),
                    'options' => [
                        'option_value' => [
                            'type'  => 'text',
                            'label' => __('Option Value', 'directorist'),
                            'value' => '',
                        ],
                        'option_label' => [
                            'type'  => 'text',
                            'label' => __('Option Label', 'directorist'),
                            'value' => '',
                        ],
                        'option_icon' => [
                            'type'  => 'text',
                            'label' => __('Option Icon', 'directorist'),
                            'value' => '',
                        ],
                        'option_class' => [
                            'type'  => 'text',
                            'label' => __('Option Class', 'directorist'),
                            'value' => '',
                        ],
                    ]
                ],
                'class' => [
                    'type'  => 'text',
                    'label' => __('Class', 'directorist'),
                    'value' => 'directorist-field-shortcode',
                ],
                'placeholder' => [
                    'type'  => 'text',
                    'label' => __('Placeholder', 'directorist'),
                    'value' => '',
                ],
                'description' => [
                    'type'  => 'text',
                    'label' => __('Description', 'directorist'),
                    'value' => '',
                ],
                'required' => [
                    'type'  => 'toggle',
                    'label'  => __('Required', 'directorist'),
                    'value' => false,
                ],
                'only_for_admin' => [
                    'type'  => 'toggle',
                    'label'  => __('Only For Admin Use', 'directorist'),
                    'value' => false,
                ],
                'assign_to' => [
                    'type' => 'radio',
                    'label' => __('Assign to', 'directorist'),
                    'value' => 'form',
                    'options' => [
                        [
                            'label' => __('Form', 'directorist'),
                            'value' => 'form',
                        ],
                        [
                            'label' => __('Category', 'directorist'),
                            'value' => 'category',
                        ],
                    ],
                ]
            ]
        );

        $widgets['mpp_housing'] = array(
            'label' => 'Select Housing',
            'icon' => 'uil uil-home',
            'options' => [
                'type' => [
                    'type'  => 'hidden',
                    'value' => 'mpp_housing',
                ],
                'label' => [
                    'type'  => 'text',
                    'label' => __('Label', 'directorist'),
                    'value' => 'Housing',
                ],
                'field_key' => apply_filters('directorist_custom_field_meta_key_field_args', [
                    'type'  => 'hidden',
                    'label' => __('Key', 'directorist'),
                    'value' => 'mpp-apartment',
                    'rules' => [
                        'unique' => true,
                        'required' => true,
                    ]
                ]),
                'class' => [
                    'type'  => 'text',
                    'label' => __('Class', 'directorist'),
                    'value' => 'directorist-field-housing',
                ],
                'placeholder' => [
                    'type'  => 'text',
                    'label' => __('Placeholder', 'directorist'),
                    'value' => '',
                ],
                'description' => [
                    'type'  => 'text',
                    'label' => __('Description', 'directorist'),
                    'value' => '',
                ],
                'required' => [
                    'type'  => 'toggle',
                    'label'  => __('Required', 'directorist'),
                    'value' => false,
                ],
                'only_for_admin' => [
                    'type'  => 'toggle',
                    'label'  => __('Only For Admin Use', 'directorist'),
                    'value' => false,
                ],
                'assign_to' => [
                    'type' => 'radio',
                    'label' => __('Assign to', 'directorist'),
                    'value' => 'form',
                    'options' => [
                        [
                            'label' => __('Form', 'directorist'),
                            'value' => 'form',
                        ],
                        [
                            'label' => __('Category', 'directorist'),
                            'value' => 'category',
                        ],
                    ],
                ]
            ]
        );
        return $widgets;
    }

    public function atbdp_single_listing_content_widgets($widgets)
    {
        $widgets['mpp_vacancy'] = [
            'options' => [
                'icon' => [
                    'type'  => 'icon',
                    'label' => 'Widget Icon',
                    'value' => 'las la-list-alt',
                ],
            ]
        ];

        // Housing
        $widgets['mpp_housing'] = [
            'options' => [
                'icon' => [
                    'type'  => 'icon',
                    'label' => 'Widget Icon',
                    'value' => 'las la-home',
                ],
            ]
        ];
        return $widgets;
    }

    public function directorist_field_template($template, $field_data)
    {
        if ('mpp_vacancy' === $field_data['widget_name']) {
            $data = $field_data;
            require(get_stylesheet_directory() . '/includes/directorist/templates/listing-form/vacancy.php');
        }
        if ('mpp_housing' === $field_data['widget_name']) {
            $data = $field_data;
            require(get_stylesheet_directory() . '/includes/directorist/templates/listing-form/housing.php');
        }
        return $template;
    }

    public function directorist_single_item_template($template, $field_data)
    {
        if ('mpp_vacancy' === $field_data['widget_name']) {
            $data = $field_data;
            require(get_stylesheet_directory() . '/includes/directorist/templates/single/vacancy.php');
        }
        if ('mpp_housing' === $field_data['widget_name']) {
            $data = $field_data;
            require(get_stylesheet_directory() . '/includes/directorist/templates/single/housing.php');
        }
        return $template;
    }
}

new MPP_Directorist_Fields;
