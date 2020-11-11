<?php
/**
 * The admin-specific functionality of the plugin.
 */
class WP_UTZ_Comment 
{
    private $plugin;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin) 
    {
        $this->plugin = $plugin;
        
        // add JS and CSS scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // capture date values from comment editor on save
        add_filter('wp_update_comment_data', array($this, 'saving_date'), 1);       
    }
    
    /**
     * Register the stylesheets & JavaScript for the admin area.
     */
    public function enqueue_scripts($page) 
    {
        if ($page == 'comment-new.php' || $page == 'comment.php')
        {
            if (isset($_GET['c']))
            {
                wp_enqueue_script( $this->plugin->slug.'-comment', plugin_dir_url( __FILE__ ) . 'js/comment.js', array( 'jquery' ), $this->plugin->version, false );
                $comment = get_comment(intval($_GET['c']));

                if ($this->plugin->opts['affect_input'] && !empty($this->plugin->u_opts['timezone']) && isset($comment->comment_date))
                {
                    // add localized labels to JS
                    wp_localize_script($this->plugin->slug, 'wp_utz_comment_obj', array(
                        'date_aa' => wp_date('Y', strtotime($comment->comment_date))
                        ,'date_mm' => wp_date('m', strtotime($comment->comment_date))
                        ,'date_jj' => wp_date('d', strtotime($comment->comment_date))
                        ,'date_hh' => wp_date('H', strtotime($comment->comment_date))
                        ,'date_mn' => wp_date('i', strtotime($comment->comment_date))
                        ,'date_ss' => wp_date('s', strtotime($comment->comment_date))
                    ));
                }
            }
        }
    }
    
    /**
     * Translates the user TZ back to the sites TZ 
     * when a post is updated or created
     */
    public function saving_date($data)
    {
        if ($this->plugin->opts['affect_input'] && !empty($this->plugin->u_opts['timezone']))
        {            
            $tz = new DateTimeZone($this->plugin->tz_format($this->plugin->u_opts['timezone']));
            $tzdate = new DateTime($data['comment_date'], $tz);
            $offset = $tzdate->getOffset();

            if ($offset != 0)
            {
                $data['comment_date'] = date('Y-m-d H:i:s', strtotime($data['comment_date']) - $offset);
            }

            // first get the offset between the user and site tz
            $tz = new DateTimeZone($this->plugin->tz_format($this->plugin->u_opts['timezone']));
            $tzdate = new DateTime($data['comment_date_gmt'], $tz);
            $offset = $tzdate->getOffset();

            if ($offset != 0)
            {
                $data['comment_date_gmt'] = date('Y-m-d H:i:s', strtotime($data['comment_date_gmt']) - $offset);
                
                // next check the offset between the site and GMT
                $tz = new DateTimeZone('GMT');
                $tzdate = new DateTime($data['comment_date_gmt'], $tz);
                $offset = $tzdate->getOffset();

                if ($offset != 0)
                {
                    $data['comment_date_gmt'] = date('Y-m-d H:i:s', strtotime($data['comment_date_gmt']) - $offset);
                }
            }
        }

        return $data;
    }
}
