<?php

/**
 * The core plugin class.
 */
class WP_UTZ 
{
    public $slug;
    public $name;
    public $version;
    public $opts;
    public $u_opts;
    
    private $debug = 0;
    
    public function __construct() 
    {
        $this->slug = 'wp-utz';
        $this->name = __('User Timezone', $this->slug);
        $this->version = '1.0.1';
        
        // default option values
        $this->opts = array(
            'admin_bar_display' => 1
            ,'admin_bar_display_24' => 0
            ,'admin_bar_display_tz' => 0
            ,'affect_input' => 0
        );
        
        // default option values
        $this->u_opts = array(
            'timezone' => ''
            ,'date_format' => ''
            ,'time_format' => ''
        );

        $this->load_dependencies();
        
        // loads up admin menu
        add_action('init', array($this, 'load_user_opts'));
    }
    
    /**
     * Load the required dependencies for this plugin.
     * The class responsible for defining all actions that occur in the admin area.
     */
    private function load_dependencies() 
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/post.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/comment.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/user.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/setting.php';
    }   
    
    /**
     * Runs what needs to run for plugin
     */
    public function run()
    {
        /**
         * Load the plugin text domain for translation.
         */    
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        
        /**
         * Load the plugin settings
         */
        $opts = get_option($this->slug);
        
        if (!empty($opts))
        {
            foreach($opts as $opt => $v)
            {
                $this->opts[$opt] = $v;
            }
        }

        /**
         * Calls Classes that needs to be loaded
         */ 
        $this->admin = new WP_UTZ_Admin($this);
        $this->post = new WP_UTZ_Post($this);
        $this->comment = new WP_UTZ_Comment($this);
        $this->user = new WP_UTZ_User($this);
        $this->setting = new WP_UTZ_Setting($this);
    }
    
    /**
     * Load the user settings
     */
    public function load_user_opts()
    {
        $uid = get_current_user_id();
        
        if (!empty($uid))
        {
            $opts = get_user_meta($uid, '_wp_utz_opts', 1);

            if (!empty($opts))
            {
                foreach($opts as $opt => $v)
                {
                    $this->u_opts[$opt] = $v;
                }
            }
        }
    }
    
    /**
     * Used to format WP's manual UTC offsets 
     */
    public function tz_format($tz)
    {
        if (strpos($tz, 'UTC') > -1)
        {
            $offset = str_replace('UTC', '', $tz);
            $hours = (int) $offset;
            
            if (empty($offset))
                $offset = 0;
                
            $minutes = ( $offset - $hours );

            $sign = ( $offset < 0 ) ? '-' : '+';
            $abs_hour = abs( $hours );
            $abs_mins = abs( $minutes * 60 );
            $tz = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
        }
        
        return $tz;
    }
    
    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() 
    {
        load_plugin_textdomain(
            $this->slug,
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }
    
    /**
     * Log out a message
     * Seperate from error_log() because a lot of warnings get written there
     * trying to keep this seperate so problems are more obvious
     */
    public function _log_msg($msg, $suffix = '')
    {
        if (!$this->debug)
        {
            return;
        }
        
        // gather stack trace
        $trace = debug_backtrace(); 
        $caller = array_shift($trace); 
        $function_name = $caller['function']; 
        $stack = sprintf("%s: Called from %s:%s\n", $function_name, $caller['file'], $caller['line']); 
    
        // check for old files
        // only keep 30 days worth
        $dir = dirname(__FILE__) . "/logs/";
        $files = scandir($dir);

        foreach($files as $f)
        {
            if ((strpos($f, ".") !== 0) && (strpos($f, ".txt")))
            {
                // if log files are older then 30 days
                if (filemtime($dir.$f) < (time() - (3600 * 24) * 30))
                {
                    unlink($dir.$f);
                }
            }
        }

        // write out to file
        file_put_contents($dir.date('Ymd').$suffix.'.txt', date('c', time())." (".getmypid().") : ".print_r($msg, 1)."\n".$stack."\n", FILE_APPEND);
    }
}
