<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/*
 * Plugin Name: Enhanced-Calendar
 * Plugin URI:  http://michaelwalsh.org/wordpress-stuff/wordpress-plugins/enhanced-calendar
 * Description: This plugin enhances <a href="http://www.kieranoshea.com">Kieran O'Shea's</a> <a href="http://wordpress.org/extend/plugins/calendar/">Calendar plugin</a>.
 * Author: Mike Walsh
 * Author URI: http://www.michaelwalsh.org
 * Version: 0.0.7
 */

/*
 * Copyright 2009  Mike Walsh  (email : mpwalsh8@gmail.com)
 *
 * GPL stuff goes here ...
 */

//  Need a better way to know the path to the Calendar plugin
//  but unless someone has customized it, this should be fine.

define("CALENDAR_PLUGIN", "calendar/calendar.php") ;

/**
 * Perform a plugin dependency check to make sure Calendar is
 * installed and activated.  Do this checking for the existance
 * of the calendar() function.
 */
function enahnced_calendar_dependency_check()
{
    global $pagenow ;

    if ( $pagenow != 'plugins.php' ) return ;

    // Set your requirements
    //
    $required_plugin = CALENDAR_PLUGIN ;
    $dependant_plugin = plugin_basename(dirname(__FILE__)) .  "/" . basename(__FILE__) ;

    // If this plugin is being activated
 
    if (isset($_GET['activate']) && $_GET['activate'] == 'true')
    {
        if ($plugins = get_option('active_plugins'))
        {
            if (!in_array( $required_plugin , $plugins ))
            {
                if ($keys = array_keys($plugins, $dependant_plugin))
                {
                    unset($plugins[$keys[0]]) ;

                    if ( update_option('active_plugins', $plugins))
                    {
                        unset($_GET['activate']) ;
                        add_action('admin_notices', 'enahnced_calendar_required_plugin_missing_warning') ;
                    }
                }            
            }
        }
    }
    elseif (((isset($_GET['action']) && $_GET['action'] == 'deactivate'))
        && (isset($_GET['plugin']) && $_GET['plugin'] == $required_plugin))
    {
        if ($plugins = get_option('active_plugins'))
        {
            if (in_array( $dependant_plugin , $plugins ))
            {
                if ($keys = array_keys($plugins,$dependant_plugin))
                {
                    unset($plugins[$keys[0]]) ;
                    if (update_option('active_plugins', $plugins))
                    {
                        add_action('admin_notices',
                            'enahnced_calendar_dependant_plugin_deactivated') ;
                    }
                }            
            }
        }        
    }
}

//  Add a hook for Admin Dashboard

if (is_admin())
{
    add_action('plugins_loaded','enahnced_calendar_dependency_check');
}

// Add a notification div when admin attempts to activate dependent
// plugin without without required plugin

function enahnced_calendar_required_plugin_missing_warning()
{
?>
<div id='required_plugin_missing_warning' class='updated fade'>
<p><strong>Plugin Not Activated.</strong>
You must install and activate <a href="http://wordpress.org/extend/plugins/calendar/">Calendar</a> in order for Calendar-Plus to work.
</p>
</div>
<?php
}

// NOT WORKING YET BECAUSE WP REDIRECTS AFTER I ADD MY HOOK
// - Adds notification div when admin deactivates required plugin

function enahnced_calendar_dependant_plugin_deactivated()
{
    //die('here') ;
?>
<div id='dependant_plugin_deactivated' class='updated fade'>
<p><strong>Calendar-Plus deactivated.</strong>
CalndarPlus is dependant on Calendar.  It cannot be reactivated until
Calendar is reactivated first.
</p>
</div>
<?php
}

/**
 * CalendarPlus class definition
 *
 * @author Mike Walsh <mike_walsh@mindspring.com>
 * @access public
 *
 * The CalendarPlus class wraps Kieran O'Shea's Calendar plugin
 * in a class variable so multiple instances of it can appear on
 * a page.
 *
 * Calendar plugin:  http://wordpress.org/extend/plugins/calendar/
 *
 * This plugin requires the Calendar plugin to be installed and
 * active in order to work.
 *
 * Combine this plugin with the Sidebar Shortcodes plugin (see
 * http://wordpress.org/extend/plugins/sidebar-shortcodes/) to
 * display a calendar in a sidebar widget.
 *
 */
class CalendarPlus
{
    /**
     * class property to store the calendar content
     */
    var $__calendarplus = null ;

    /**
     * class property to store the calendar id
     */
    var $__calendarplusid = 0 ;

    /**
     * Constructor
     *
     * This constructor accounts for the fact that the way the
     * Calendar plugin is coded will try to redefine functions
     * if it is called twice on the same page.
     *
     * To get arround that limitation, the constructor only calls
     * the Calendar plugin once and if another instatnce is created
     * a reference to the first instance is kept and returned for
     * all subsequent instances.
     */
    function CalendarPlus()
    {
        static $CalendarPlusId = 0 ;
        static $CalendarPlusInit = null ;

        //  Is this the first instance?

        if ($CalendarPlusInit === null)
        {
            $CalendarPlusInit = calendar() ;
        }

        //  Make sure this instance points to the calendar content

        $this->__calendarplus = &$CalendarPlusInit ;
        $this->__calendarplusid = ++$CalendarPlusId ;
    }

    /**
     * getCalendar() - return calendar content
     *
     * @return string - calendar content
     */
    function getCalendar()
    {
        return $this->__calendarplus ;
    }

    /**
     * getCalendarId() - return calendar id
     *
     * @return int - calendar id
     */
    function getCalendarId()
    {
        return $this->__calendarplusid ;
    }
}


/**
 * calendar shortcode handler
 *
 * Build a short code handler to display a calendar.
 *
 * [calendar [weekday=0] [dateswitcher='on'|'off'] [descriptions='on'|'off']]
 *
 * Example uses this shortcode:
 *
 * [calendar weekday=3]
 * [calendar dateswitcher='off']
 * [calendar weekday=1 dateswitcher='off' descriptions='off']
 *
 *
 * @param array - shortcode attributes
 * @return string -  HTML code
 *
 */
function enahnced_calendar_sc_handler($atts)
{
    //  Parse the shortcode
 
    extract(shortcode_atts(array(
        'weekday' => 0
       ,'descriptions' => 'on'
       ,'dateswitcher' => 'on'
    ), $atts)) ;

    $cal = new CalendarPlus() ;
    $id = $cal->getCalendarId() ;
    $calendar = $cal->getCalendar() ;

    $calendar = preg_replace("/<table/i",
        "<table id=\"calendar-table-{$id}\"", $calendar) ;

    //  Start creating the jQuery script that
    //  will tweak the output of the Calendar.
    //  Some of the script is included conditionally
    //  but the beginning of it is always included
    //  and updates any TD element which contains
    //  events by appending the "events" class.

    $js = "<script type=\"text/javascript\">
        jQuery(document).ready(
        function()
        {
            //  Add a DIV with a class to any day with an
            //  event on it so it can be styled via CSS.
            jQuery(\"td.day-with-date:not(.no-events)\",
                \"#calendar-table-{$id}\").addClass(\"events\") ;" ;

    //  Trim the weekday heading?  Any value other than zero gets trimmed

    if ($weekday != 0)  //  0 means don't do any trimming!
    {
        // Deal with the week not starting on a Monday
        // Choose Monday if anything other than Sunday is set

        if (get_option('start_of_week') == 0)
        {
            $name_days = array(1 => __('Sunday','calendar'),
                __('Monday','calendar'), __('Tuesday','calendar'),
                __('Wednesday','calendar'), __('Thursday','calendar'),
                __('Friday','calendar'), __('Saturday','calendar')) ;
        }
        else
        {
            $name_days = array(1=>__('Monday','calendar'),
                __('Tuesday','calendar'), __('Wednesday','calendar'),
                __('Thursday','calendar'), __('Friday','calendar'),
                __('Saturday','calendar'), __('Sunday','calendar')) ;
        }

        foreach ($name_days as $name_day)
        {
            $calendar = preg_replace("/$name_day/",
                substr($name_day, 0, $weekday), $calendar) ;
        }
    }

    //  Turn off the date switcher?

    if ($dateswitcher == 'off')
    {
        //  Include jQuery script to hide the date switcher

        $js .= "
            //  Hide the Date Switcher
            jQuery(\".calendar-date-switcher\", \"#calendar-table-{$id}\").hide() ;
        " ;
    }

    //  Turn off descriptions?

    if ($descriptions == 'off')
    {
        //  Include jQuery script to hide the descriptions

        $js .= "
            //  Eliminate the '*' characters that appear on days
            //  which have events - they are redundant with the
            //  dagger which is added below.
            jQuery(\"span.event\", \".calendar-table\").each(function(){
                var html = jQuery(this).html().replace(/(<br\s*\/*>)\s*\*/g, '$1') ;
                jQuery(this).html(html) ;
            }) ;
           
            //  Clean up the line breaks - only show the first one
            jQuery(\"span.event\", \"#calendar-table-{$id}\").find(\"br\").hide() ;
            jQuery(\"span.event\", \"#calendar-table-{$id}\").find(\"br:first\").show();

            //  Remap the text descriptions to the double dagger.
            jQuery(\"span.calnk > a\", \"#calendar-table-{$id}\").each(function(){
                var html = jQuery(this).html().replace(/^.*<span/i,
                    \"<span class=\\\"event-dagger\\\">&#135;</span><span\");
                jQuery(this).html(html) ;
            });" ;
    }

    //  Terminate the jQuery script and append it to the calendar HTML

    $js .= '
        });
        </script>' ;

    return $calendar . $js ;
}

//  Register the shortcode
add_shortcode('calendar', 'enahnced_calendar_sc_handler');

/**
 * Add print_scripts action
 *
 * This function adds Javascript references
 * required by the Calendar-Plus plugin.
 *
 */
function enahnced_calendar_wp_print_scripts()
{
    //  Load Calendar-Plus Javascript code
    $script = plugins_url(plugin_basename(dirname(__FILE__))) . "/enhanced-calendar.js";
    wp_enqueue_script('enhanced-calendar', $script, array('jquery')) ;
}

//Not being used right now - commented out
//add_action('wp_print_scripts', 'enahnced_calendar_wp_print_scripts') ;

/**
 * Add print_scripts action
 *
 * This function adds Javascript references
 * required by the Calendar-Plus plugin.
 *
 */
function enahnced_calendar_wp_print_styles()
{
    //  Load CSS file

    $css = plugins_url(plugin_basename(dirname(__FILE__))) .
        "/enhanced-calendar.css";
    wp_register_style("enhanced-calendar-style", $css, false) ;
    wp_enqueue_style("enhanced-calendar-style") ;
}

add_action('wp_print_styles', 'enahnced_calendar_wp_print_styles') ;

/**
 * Add admin_init action
 *
 * This function adds Javascript references
 * required by the Calendar-Plus plugin.
 *
 */
function enahnced_calendar_admin_print_scripts()
{
    remove_action('admin_head', 'calendar_add_javascript') ;
    //  Load plugin Javascript
    $script = dirname(plugins_url(plugin_basename(dirname(__FILE__)))) .
       "/" . dirname(CALENDAR_PLUGIN) . "/javascript.js" ;
    wp_enqueue_script('calendar', $script) ;
}

//Not being used right now - commented out
//add_action('admin_print_scripts', 'enahnced_calendar_admin_print_scripts') ;

/**
 * Add plugins_loaded action
 *
 * This function removes an action and filter
 * defined by the Calendar plugin because they
 * are handled differently by Calendar-Plus.
 *
 */
function enahnced_calendar_plugins_loaded()
{
    //  Remove Calendar's filter - remove the non-standard {CALENDAR}
    //  keyword tag.  Why? The keyword tag is better served with a proper
    //  shortcode which this plugin defines.
    remove_filter('the_content', 'calendar_insert') ;

    //  Remove Calendar's Javascript and CSS insertion.  Why?
    //  By ignoring the CSS which is stored in the database a
    //  theme can style the calendar and not have it overloaded
    //  by the default CSS in the database.

    remove_action('wp_head', 'calendar_wp_head') ;
}

add_action('plugins_loaded', 'enahnced_calendar_plugins_loaded') ;

/*
 * Calendar Plus menus
 *
 * Add a menu under Settings for the Calendar options
 * because I always go looking there for the settings.
 *
 * There is some hard coding of names from the original
 * plugin which might have to be changed if the Calendar
 * plugin ever changes them for any reason.
 */
function enahnced_calendar_menus() {
    $page = add_options_page('calendar',
        __('Calendar Options','calendar'), 10,
        'calendar-config', 'edit_calendar_config'); 

    //  Define the appropriate actions so the Javascript
    //  is only loaded on the pages that really need it.
    add_action("admin_print_scripts-$page",
        'enahnced_calendar_admin_print_scripts') ;
    add_action("admin_footer-$page",
        'enahnced_calendar_admin_footer_calendar_config') ;

    //  Hook the Javascript generation for the original Calendar menus

    $pages = array("toplevel_page_calendar",
        "calendar_page_calendar-categories",
        "calendar_page_calendar-config" ) ;

    foreach ($pages as $page)
    {
        add_action("admin_print_scripts-$page",
            'enahnced_calendar_admin_print_scripts') ;
    }

    $page = "toplevel_page_calendar" ;
    add_action("admin_footer-$page",
        'enahnced_calendar_admin_footer_calendar_manage') ;
}

//  Set up the menus
add_action('admin_menu', 'enahnced_calendar_menus');

/*
 * jQuery script to hide the last row of the table
 * on the Calendar options page.  The last row holds
 * the form fields for the CSS which is stored in the
 * database.  With Calendar-Plus this CSS isn't used
 * so it is hidden as not to confuse the user.
 *
 * This jQuery script is only loaded and run on the
 * Calendar Options page.
 */
function enahnced_calendar_admin_footer_calendar_config()
{
?>
<script type="text/javascript">
    jQuery(document).ready(
    function()
    {
        jQuery('div#linkadvanceddiv').find('tr:last').hide();
    });
</script>
<?php
}

/*
 * Javascript to load the Calendar styles.
 *
 * This Javascript is only loaded and run on the
 * Manage Calendar page.
 */
function enahnced_calendar_admin_footer_calendar_manage()
{
?>
<script type="text/javascript">
document.write(getCalendarStyles()) ;
</script>
<?php
}
?>
