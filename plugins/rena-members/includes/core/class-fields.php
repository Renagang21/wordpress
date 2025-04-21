
<?php
namespace RenaMembers\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Form fields management class
 */
class Fields {
    /**
     * Get all form fields
     * 
     * @param string $form_key
     * @return array
     */
    public function get_form_fields($form_key) {
        global $wpdb;
        
        $forms_table = $wpdb->prefix . 'rena_members_forms';
        
        $form = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $forms_table WHERE form_key = %s", $form_key)
        );
        
        if (!$form) {
            return array();
        }
        
        $fields = json_decode($form->form_fields, true);
        
        if (!is_array($fields)) {
            return array();
        }
        
        // Sort fields by position
        uasort($fields, function($a, $b) {
            return $a['position'] - $b['position'];
        });
        
        return $fields;
    }
    
    /**
     * Get form settings
     * 
     * @param string $form_key
     * @return array
     */
    public function get_form_settings($form_key) {
        global $wpdb;
        
        $forms_table = $wpdb->prefix . 'rena_members_forms';
        
        $form = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $forms_table WHERE form_key = %s", $form_key)
        );
        
        if (!$form) {
            return array();
        }
        
        $settings = json_decode($form->form_settings, true);
        
        if (!is_array($settings)) {
            return array();
        }
        
        return $settings;
    }
    
    /**
     * Save form fields
     * 
     * @param string $form_key
     * @param array $fields
     * @return bool
     */
    public function save_form_fields($form_key, $fields) {
        global $wpdb;
        
        $forms_table = $wpdb->prefix . 'rena_members_forms';
        
        // Update fields
        $result = $wpdb->update(
            $forms_table,
            array(
                'form_fields' => json_encode($fields),
                'date_updated' => current_time('mysql')
            ),
            array('form_key' => $form_key)
        );
        
        return $result !== false;
    }
    
    /**
     * Save form settings
     * 
     * @param string $form_key
     * @param array $settings
     * @return bool
     */
    public function save_form_settings($form_key, $settings) {
        global $wpdb;
        
        $forms_table = $wpdb->prefix . 'rena_members_forms';
        
        // Update settings
        $result = $wpdb->update(
            $forms_table,
            array(
                'form_settings' => json_encode($settings),
                'date_updated' => current_time('mysql')
            ),
            array('form_key' => $form_key)
        );
        
        return $result !== false;
    }
    
    /**
     * Render form field
     * 
     * @param string $field_key
     * @param array $field
     * @param mixed $value
     * @return string
     */
    public function render_field($field_key, $field, $value = '') {
        $required = !empty($field['required']) ? ' required' : '';
        $html = '';
        
        switch ($field['type']) {
            case 'text':
            case 'email':
            case 'url':
            case 'number':
                $html .= '<div class="rena-members-field">';
                $html .= '<label for="' . esc_attr($field_key) . '">' . esc_html($field['label']) . '</label>';
                $html .= '<input type="' . esc_attr($field['type']) . '" name="' . esc_attr($field_key) . '" id="' . esc_attr($field_key) . '" class="rena-members-input" value="' . esc_attr($value) . '"' . $required . ' />';
                $html .= '</div>';
                break;
                
            case 'textarea':
                $html .= '<div class="rena-members-field">';
                $html .= '<label for="' . esc_attr($field_key) . '">' . esc_html($field['label']) . '</label>';
                $html .= '<textarea name="' . esc_attr($field_key) . '" id="' . esc_attr($field_key) . '" class="rena-members-textarea"' . $required . '>' . esc_textarea($value) . '</textarea>';
                $html .= '</div>';
                break;
                
            case 'password':
                $html .= '<div class="rena-members-field">';
                $html .= '<label for="' . esc_attr($field_key) . '">' . esc_html($field['label']) . '</label>';
                $html .= '<input type="password" name="' . esc_attr($field_key) . '" id="' . esc_attr($field_key) . '" class="rena-members-input"' . $required . ' />';
                $html .= '</div>';
                break;
                
            case 'checkbox':
                $html .= '<div class="rena-members-field">';
                $html .= '<label>';
                $html .= '<input type="checkbox" name="' . esc_attr($field_key) . '" value="1"' . checked($value, 1, false) . $required . ' />';
                $html .= esc_html($field['label']);
                $html .= '</label>';
                $html .= '</div>';
                break;
                
            case 'select':
                $html .= '<div class="rena-members-field">';
                $html .= '<label for="' . esc_attr($field_key) . '">' . esc_html($field['label']) . '</label>';
                $html .= '<select name="' . esc_attr($field_key) . '" id="' . esc_attr($field_key) . '" class="rena-members-select"' . $required . '>';
                
                if (!empty($field['options']) && is_array($field['options'])) {
                    foreach ($field['options'] as $option_value => $option_label) {
                        $html .= '<option value="' . esc_attr($option_value) . '"' . selected($value, $option_value, false) . '>' . esc_html($option_label) . '</option>';
                    }
                }
                
                $html .= '</select>';
                $html .= '</div>';
                break;
                
            case 'radio':
                $html .= '<div class="rena-members-field">';
                $html .= '<label>' . esc_html($field['label']) . '</label>';
                
                if (!empty($field['options']) && is_array($field['options'])) {
                    foreach ($field['options'] as $option_value => $option_label) {
                        $html .= '<label class="rena-members-radio">';
                        $html .= '<input type="radio" name="' . esc_attr($field_key) . '" value="' . esc_attr($option_value) . '"' . checked($value, $option_value, false) . $required . ' />';
                        $html .= esc_html($option_label);
                        $html .= '</label>';
                    }
                }
                
                $html .= '</div>';
                break;
        }
        
        return $html;
    }
}