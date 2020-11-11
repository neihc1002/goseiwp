<?php
/**
 * The admin-specific functionality of the plugin.
 */
class WP_UTZ_Setting 
{
    private $plugin;
    private $page_slug;
    private $opts = array();

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin) 
    {
        $this->plugin = $plugin;
        $this->page_slug = 'wp_utz_settings';
        
        $this->opts_settings = array(
            'admin_bar_display' => array(
                'lbl' => __('Admin Bar Display', $this->plugin->slug)
                ,'desc' => __("If enabled, the user's and/or site's GMT offset will be displayed in the admin bar", $this->plugin->slug)
                ,'type' => 'boolean'
                ,'required' => 0
                ,'array' => 0
            )
            ,'admin_bar_display_24' => array(
                'lbl' => __("Admin Bar Force 24 hour clock", $this->plugin->slug)
                ,'desc' => __("If enabled, force a 24 hour clock to be displayed for both the user and site's time.", $this->plugin->slug)
                ,'type' => 'boolean'
                ,'required' => 0
                ,'array' => 0
            )
            ,'admin_bar_display_tz' => array(
                'lbl' => __("Admin Bar Display GMT Timezone Offset", $this->plugin->slug)
                ,'desc' => __("If enabled, this will display the GTM offset of both the user and site's timezone.", $this->plugin->slug)
                ,'type' => 'boolean'
                ,'required' => 0
                ,'array' => 0
            )
            /*,'affect_input' => array(
                'lbl' => __("Affect Date/Time Inputs", $this->plugin->slug)
                ,'desc' => __("If enabled, assumes the date/time entered is in the user's local timezone.", $this->plugin->slug)
                ,'type' => 'boolean'
                ,'required' => 0
                ,'array' => 0
            )*/          
        );

        // loads up admin menu
        add_action('admin_menu', array($this, 'load_admin_menu'));
    }

    /**
     * handles admin menu
     */
    public function load_admin_menu()
    {            
        // adds the settings as a sub page
        add_submenu_page('options-general.php', __('WP User Timezone', $this->plugin->slug), __('WP User Timezone', $this->plugin->slug), 'administrator', $this->page_slug, array($this, 'settings'), 99);
    }

    /**
     * displays the settings form
     */
    public function settings()
    {
        if (isset($_POST[$this->plugin->slug]) && $_POST[$this->plugin->slug] == 1)
        {
            $this->save_settings();
        }
        
        echo '
            <div class="wrap">
                <h1>'.$this->plugin->name.' '.__('Settings', $this->plugin->slug).'</h1>

                <form method="post" action="'.admin_url('options-general.php?page='.$this->page_slug).'">
                <input type=hidden name="'.$this->plugin->slug.'" value="1">
                    <table class="form-table '.$this->plugin->slug.' settings">
        ';
        
        foreach ($this->opts_settings as $k => $opt)
        {
            if ($opt['array'])
            {
                $value = implode(', ', $this->plugin->opts[$k]);
            }
            else
            {
                $value = $this->plugin->opts[$k];
            }
            
            if ($opt['required'])
            {
                $required = '<span class="required">*</span>';
            }
            else
            {
                $required = '';
            }
            
            if ($opt['type'] == 'num')
            {
                echo '
                    <tr>
                        <th scope="row"><label for="'.$k.'">'.$opt['lbl'].'</label> '.$required.'</th>
                        <td>
                            <input type="number" name="'.$k.'" id="'.$k.'" value="'.$value.'" />
                            <p class="description">'.$opt['desc'].'</p>
                        </td>
                    </tr>
                ';
            }
            else if ($opt['type'] == 'boolean')
            {
                echo '
                    <tr>
                        <th scope="row"><label for="'.$k.'">'.$opt['lbl'].'</label> '.$required.'</th>
                        <td>
                            <input type="radio" name="'.$k.'" id="'.$k.'" value="1" '.($value ? 'checked' : '').'/> <label for="'.$k.'">'.__('Yes').'</label> &nbsp;&nbsp; 
                            <input type="radio" name="'.$k.'" id="'.$k.'_no" value="0" '.($value ? '' : 'checked').'/> <label for="'.$k.'_no">'.__('No').'</label>
                            <p class="description">'.$opt['desc'].'</p>
                        </td>
                    </tr>
                ';
            }            
            else if ($opt['type'] == 'text')
            {
                echo '
                    <tr>
                        <th scope="row"><label for="'.$k.'">'.$opt['lbl'].'</label> '.$required.'</th>
                        <td>
                            <input type="text" name="'.$k.'" id="'.$k.'" value="'.$value.'" />
                            <p class="description">'.$opt['desc'].'</p>
                        </td>
                    </tr>
                ';
            }
        }
        
        echo '
                </table>
        ';
        
        submit_button();
        
        echo '
            </form>
            </div>
        ';
    }
    
    /**
     * saves settings
     */
    private function save_settings()
    {
        foreach($this->plugin->opts as $k => $v)
        {
            if (isset($_POST[$k]))
            {
                if ($this->opts_settings[$k]['required'])
                {
                    if (!empty($_POST[$k]))
                    {
                        if ($this->opts_settings[$k]['array'])
                        {
                            $this->plugin->opts[$k] = array_filter(array_map('trim', explode(',', sanitize_text_field($_POST[$k]))));
                        }
                        else
                        {
                            $this->plugin->opts[$k] = $_POST[$k];
                        }
                    }
                }
                else
                {
                    if ($this->opts_settings[$k]['array'])
                    {
                        $this->plugin->opts[$k] = array_filter(array_map('trim', explode(',', sanitize_text_field($_POST[$k]))));
                    }
                    else
                    {
                        $this->plugin->opts[$k] = $_POST[$k];
                    }
                }
            }
        }
        
        update_option($this->plugin->slug, $this->plugin->opts);
    }
}
