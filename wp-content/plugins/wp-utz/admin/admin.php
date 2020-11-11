<?php
/**
 * The admin-specific functionality of the plugin.
 */
class WP_UTZ_Admin 
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

        // add JS and CSS scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // add filter to offset users profile tz
        add_filter('wp_date', array($this, 'wp_date'), 10, 4);
        
        // add filter to date/time options for user
        add_filter('option_date_format', array($this, 'date_format'), 10, 2);
        add_filter('option_time_format', array($this, 'time_format'), 10, 2);
        
        // display timezone information in the Admin bar
        add_action('admin_bar_menu', array($this, 'admin_bar'), 999);
        
        // setup AJAX endpoints
        add_action('wp_ajax_wp_utz_admin_bar', array($this, 'ajax_admin_bar') );
        
        // add settings link
        add_filter( 'plugin_action_links_'.$this->plugin->slug.'/'.$this->plugin->slug.'.php', array($this, 'settings_link') );
    }

    /**
     * Register the stylesheets & JavaScript for the admin area.
     */
    public function enqueue_scripts($page) 
    {
        global $post;
        wp_enqueue_style( $this->plugin->slug, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->plugin->version, 'all' );
        wp_enqueue_script( $this->plugin->slug, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery'), $this->plugin->version, false );

        // add localized labels to JS
        wp_localize_script($this->plugin->slug, 'wp_utz_obj', array(
            'ajax_url' => admin_url('admin-ajax.php')
            ,'utz' => $this->tz_offset()
            ,'stz' => $this->tz_offset(1, 'GMT', 1)
            ,'affect_input' => $this->plugin->opts['affect_input']
        ));
    }
    
    /**
     * Used to reset the date/time based on user's setting
     */
    public function wp_date($date, $format, $timestamp, $timezone)
    {
        // because we call the same function we are 
        // filtering, use this to prevent an endless loop 
        if (!$this->tz_set)
        {
            if (!empty($this->plugin->u_opts['timezone']))
            {
                $timezone = new DateTimeZone($this->plugin->tz_format($this->plugin->u_opts['timezone']));
            }

            $this->tz_set = 1;
            $date = wp_date($format, $timestamp, $timezone);
        }
        
        $this->tz_set = 0;
        return $date;
    }
    
    /**
     * Used to reset the date format base on user settings
     */
    public function date_format($value, $option)
    {
        if (!empty($this->plugin->u_opts['date_format']))
        {
            $value = $this->plugin->u_opts['date_format'];
        }
        
        return $value;
    }
    
    /**
     * Used to reset the time format base on user settings
     */
    public function time_format($value, $option)
    {
        if (!empty($this->plugin->u_opts['time_format']))
        {
            $value = $this->plugin->u_opts['time_format'];
        }
        
        return $value;
    }
    
    /**
     * Used to convert date/time in users TZ, into site's date/time offset
     */
    public function input_offset($datetime, $format = '')
    {
        $rtn_time = strtotime($datetime);
        
        if (!empty($this->plugin->u_opts['timezone']))
        {
            $tz = new DateTimeZone($this->plugin->tz_format($this->plugin->u_opts['timezone']));
            $tzdate = new DateTime(date('Y-m-d H:i:s', strtotime($datetime)), $tz);          
            $offset = $tzdate->getOffset();

            if ($offset != 0)
            {
                $rtn_time = strtotime($datetime) - $offset;
            }
        }
        
        if (empty($format))
        {
            return date(get_option('date_format').' '.get_option('time_format'), $rtn_time);
        }
        else if ($format == 'mysql')
        {
            return date('Y-m-d H:i:s', $rtn_time);
        }
        else if ($format == 'timestamp')
        {
            return $rtn_time;
        }
        
        return date($format, $rtn_time);
    }
    
    /**
     * Used to format WP's manual UTC offsets 
     */
    public function tz_offset($display = 1, $label = 'GMT', $site = 0)
    {
        if (!empty($this->plugin->u_opts['timezone']) && $site == 0)
        {
            $tz = new DateTimeZone($this->plugin->tz_format($this->plugin->u_opts['timezone']));
        }
        else
        {
            $tz = wp_timezone();
        }

        $tzdate = new DateTime("now", $tz);
        $offset = $tzdate->getOffset();

        if ($offset != 0)
        {
            $offset = $offset/(60 * 60);
        }

        if ($display)
        {
            if ($offset == 0)
            {
                return $label;
            }
            else if ($offset > 0)
            {
                return $label.'+'.$offset;
            }
            
            return $label.$offset;
        }

        if ($offset > 0)
        {
            return '+'.$offset;
        }
        
        return $offset;
    }

    /**
     * Used to display site's and user's TZ info in admin bar 
     */
    public function admin_bar($wp_admin_bar = '')
    {
        if ($this->plugin->opts['admin_bar_display'])
        {
            $format = get_option('time_format');

            if ($this->plugin->opts['admin_bar_display_24'])
            {
                $format = 'H:i';
            }
            
            $title = date($format, current_time('timestamp'));
            $stz = $this->tz_offset(1, 'GMT', 1);
            
            if ($this->plugin->opts['admin_bar_display_tz'])
            {
                $title .= ' '.$stz;
            }
            
            if (!empty($this->plugin->u_opts['timezone']))
            {
                $utz = $this->tz_offset();

                if ($utz != $stz)
                {
                    $local = ' '.wp_date($format, current_time('timestamp'));
                    
                    if ($this->plugin->opts['admin_bar_display_tz'])
                    {
                        $title = __('Local:', $this->plugin->slug).' '.$local.' '.$utz.', '.__('Site:', $this->plugin->slug).' '.$title;
                    }
                    else
                    {
                        $title = __('Local:', $this->plugin->slug).' '.$local.', '.__('Site:', $this->plugin->slug).' '.$title;
                    }
                }
            }

            if ($wp_admin_bar)
            {
                $wp_admin_bar->add_node(array(
                    'id' => $this->plugin->slug
                    ,'title' => '<span class="ab-icon dashicons dashicons-clock"></span> <span id="wp_utz_admin_bar_time">'.$title."</span>"
                    ,'parent' => false
                    ,'href' => admin_url('profile.php#wp_utz_settings')
                ));
            }
            
            return $title;
        }
    }
    
    /**
     * Called every 60 seconds to update the admin bar 
     */
    public function ajax_admin_bar()
    {
        header( "Content-Type: application/json" );
        echo json_encode(array('status' => 1, 'title' => $this->admin_bar()));
        wp_die();
    }
    
    /**
     * Add link to settings page on plugin page
     */
    public function settings_link($links)
    {
        $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
        // Adds the link to the end of the array.
        array_push($links, '<a href="'.admin_url('options-general.php?page=wp_utz_settings').'">' . __( 'Settings' ) . '</a>');
        return $links;
    }
}
