<?php
/**
 * The admin-specific functionality of the plugin.
 */
class WP_UTZ_User 
{
    private $plugin;
    private $tz_set = 0;
    private $search = array();

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin) 
    {
        $this->plugin = $plugin;

        // add field to user forms
        add_action('user_new_form', array($this, 'profile_fields'), 1);
        add_action('show_user_profile', array($this, 'profile_fields'), 1);
        add_action('edit_user_profile', array($this, 'profile_fields'), 1);
        
        // add to save actions
        add_action('user_register', array($this, 'save_profile'));
        add_action('personal_options_update', array($this, 'save_profile') );
        add_action('edit_user_profile_update', array($this, 'save_profile') );
    }

    /**
     * Register input field on edit user form
     */
    public function profile_fields($u)
    {
        $settings = array(
            'timezones' => wp_timezone_choice('')
            ,'date_format' => ''
            ,'time_format' => ''
        );

        if (is_object($u))
        {
            $tz = '';        
            $u_opts = get_user_meta($u->ID, '_wp_utz_opts', 1);

            if (!empty($u_opts))
            {
                $tz = $u_opts['timezone'];
                $settings['date_format'] = $u_opts['date_format'];
                $settings['time_format'] = $u_opts['time_format'];
            }
            
            $settings['timezones'] = wp_timezone_choice($tz);
        
            // add a blank value to drop down so this can be unset
            if (!empty($tz))
            {
                $settings['timezones'] = '<option selected="selected" value="">' . __( 'Select a city' ) . '</option>'.$settings['timezones'];
            }
        }

        echo '
            <hr>
            <h2 id="wp_utz_settings">'.__('Timezone Settings', $this->plugin->slug).'</h2>
            <table class="form-table">
            <tr>
                <th scope="row"><label for="_wp_utz_timezone">'.__('Timezone', $this->plugin->slug).'</label></th>
                <td>
                    <select id="_wp_utz_timezone" name="_wp_utz_timezone" aria-describedby="timezone-description">
                        '.$settings['timezones'].'
                    </select>
                    <p class="description" id="timezone-description">
                        '.__('Choose either a city in the same timezone as you or a UTC (Coordinated Universal Time) time offset.', $this->plugin->slug).'
                    </p>
                </td>
            </tr>
            <!--<tr>
                <th scope="row"><label for="_wp_utz_date_format">'.__('Date Format', $this->plugin->slug).'</label></th>
                <td>
                    <input type="text" name="_wp_utz_date_format" id="_wp_utz_date_format" value="'.$settings['date_format'].'" aria-describedby="date_format-description" class="regular-text ltr">
                    <p class="description" id="date_format-description">
                        '.__('Enter the custom date format.', $this->plugin->slug).'
                        '.__('<a href="https://wordpress.org/support/article/formatting-date-and-time/">Documentation on date and time formatting</a>.').'
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="_wp_utz_time_format">'.__('Time Format', $this->plugin->slug).'</label></th>
                <td>
                    <input type="text" name="_wp_utz_time_format" id="_wp_utz_time_format" value="'.$settings['time_format'].'" aria-describedby="time_format-description" class="regular-text ltr">
                    <p class="description" id="time_format-description">
                        '.__('Enter the custom time format.', $this->plugin->slug).'
                        '.__('<a href="https://wordpress.org/support/article/formatting-date-and-time/">Documentation on date and time formatting</a>.').'
                    </p>
                </td>
            </tr>-->
            </table>
        ';
    }

    /**
     * Save custom field to user profile
     */
    public function save_profile($user_id)
    {
        foreach($this->plugin->u_opts as $opt => $v)
        {
            // is the tz parameter passed
            if (isset($_POST['_wp_utz_'.$opt]))
            {
                $this->plugin->u_opts[$opt] = sanitize_text_field($_POST['_wp_utz_'.$opt]);
            }
        }

        update_user_meta($user_id, '_wp_utz_opts', $this->plugin->u_opts);
    }
}