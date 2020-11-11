<?php
/**
 * The admin-specific functionality of the plugin.
 */
class WP_UTZ_Post 
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
        
        //works with custom post type
        add_action('add_inline_data', array($this, 'inline_edit'));

        // capture date values from post type editor on save
        add_filter('pre_post_date', array($this, 'saving_date'), 1);
        add_filter('pre_post_date_gmt', array($this, 'saving_date_gmt'), 1);
        
        add_filter( 'rest_post_dispatch', array($this, 'post_dispatch'), 10, 3 );
    }
    
    /**
     * Register the stylesheets & JavaScript for the admin area.
     */
    public function enqueue_scripts($page) 
    {
        global $post;

        if ($page == 'post-new.php' || $page == 'post.php' || $page == 'edit.php')
        {
            wp_enqueue_script( $this->plugin->slug.'-post', plugin_dir_url( __FILE__ ) . 'js/post.js', array( 'jquery','wp-data','wp-blocks' ), $this->plugin->version, false );

            if ($this->plugin->opts['affect_input'] && !empty($this->plugin->u_opts['timezone']) && isset($post->post_date))
            {
                // add localized labels to JS
                wp_localize_script($this->plugin->slug, 'wp_utz_post_obj', array(
                    'date_aa' => wp_date('Y', strtotime($post->post_date))
                    ,'date_mm' => wp_date('m', strtotime($post->post_date))
                    ,'date_jj' => wp_date('d', strtotime($post->post_date))
                    ,'date_hh' => wp_date('H', strtotime($post->post_date))
                    ,'date_mn' => wp_date('i', strtotime($post->post_date))
                    ,'date_ss' => wp_date('s', strtotime($post->post_date))
                ));
            }
        }
    }
    
    /**
     * Adds hidden values for inline quick edits
     */
    public function inline_edit($post)
    {
        if ($this->plugin->opts['affect_input'] && !empty($this->plugin->u_opts['timezone']))
        {
            echo '
                <div id="utz_'.$post->ID.'_aa">'.wp_date('Y', strtotime($post->post_date)).'</div>
                <div id="utz_'.$post->ID.'_mm">'.wp_date('m', strtotime($post->post_date)).'</div>
                <div id="utz_'.$post->ID.'_jj">'.wp_date('d', strtotime($post->post_date)).'</div>
                <div id="utz_'.$post->ID.'_hh">'.wp_date('H', strtotime($post->post_date)).'</div>
                <div id="utz_'.$post->ID.'_mn">'.wp_date('i', strtotime($post->post_date)).'</div>
                <div id="utz_'.$post->ID.'_ss">'.wp_date('s', strtotime($post->post_date)).'</div>
            ';
        }
    }
    
    /**
     * Translates the user TZ back to the sites TZ 
     * when a post is updated or created
     */
    public function saving_date($date)
    {
        if ($this->plugin->opts['affect_input'] && !empty($this->plugin->u_opts['timezone']))
        {            
            $tz = new DateTimeZone($this->plugin->tz_format($this->plugin->u_opts['timezone']));
            $tzdate = new DateTime($date, $tz);
            $offset = $tzdate->getOffset();

            if ($offset != 0)
            {
                $date = date('Y-m-d H:i:s', strtotime($date) - $offset);
            }
        }

        return $date;
    }
    
    /**
     * Translates the user TZ back to GMT
     * when a post is updated or created
     */
    public function saving_date_gmt($date)
    {
        if ($this->plugin->opts['affect_input'] && !empty($this->plugin->u_opts['timezone']) && !empty($date))
        {
            // first get the offset between the user and site tz
            $tz = new DateTimeZone($this->plugin->tz_format($this->plugin->u_opts['timezone']));
            $tzdate = new DateTime($date, $tz);
            $offset = $tzdate->getOffset();

            if ($offset != 0)
            {
                $date = date('Y-m-d H:i:s', strtotime($date) - $offset);
                
                // next check the offset between the site and GMT
                $tz = new DateTimeZone('GMT');
                $tzdate = new DateTime($date, $tz);
                $offset = $tzdate->getOffset();

                if ($offset != 0)
                {
                    $date = date('Y-m-d H:i:s', strtotime($date) - $offset);
                }
            }
        }

        return $date;
    }

    /**
     * For the Gutenburg editor we need to 
     * convert the date back to user's timezone
     */
    public function post_dispatch( $rest_ensure_response, $instance, $request )
    {
        if ($this->plugin->opts['affect_input'] && !empty($this->plugin->u_opts['timezone']))
        {
            if (isset($rest_ensure_response->data['date']))
            {
                $rest_ensure_response->data['date'] = wp_date('Y-m-dTH:i:s', strtotime($rest_ensure_response->data['date']));
                $rest_ensure_response->data['modified'] = wp_date('Y-m-dTH:i:s', strtotime($rest_ensure_response->data['modified']));
            }
        }

        return $rest_ensure_response; 
    }
}
