=== Enhanced-Calendar ===
Contributors: mpwalsh8
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ESP5FSYD3GXRJ
Tags: calendar
Requires at least: 2.9
Tested up to: 3.0.3
Stable tag: 0.0.4

Enhancements to Kieran O'Shea's Calendar plugin to allow multiple uses, theme styling, and more.

== Description ==

<p>
Enhanced-Calendar (<a href="http://michaelwalsh.org/blog/2010/12/08/need-to-rename-calendar-plus/">previously known as Calendar-Plus</a>) enhances <a href="http://wordpress.org/extend/plugins/calendar/">
Kieran O'Shea's Calendar plugin</a> by removing several limitations and adding the ability to instantiate
the calendar in a sidebar widget.
</p>
<p>
Enhanced-Calendar requires the original
<a href="http://wordpress.org/extend/plugins/calendar/">Calendar plugin</a> to be installed and activated.
Enhanced-Calendar introduces a proper shortcode which can also be used to display the calendar in a sidebar
by using the <a href="http://wordpress.org/extend/plugins/sidebar-shortcodes/">Sidebar Shortcode plugin</a>.
</p>

== Installation ==

<p>
To install Enhanced-Calendar, download it, unzip it, upload it to your
WordPress installation and activate like a standard plugin.
</p>

1. Upload `enhanced-calendar` to the `/wp-content/plugins/` directory
1. Activate Enhanced-Calendar through the 'Plugins' menu in WordPress

== Usage ==

Usage of Enhanced-Calendar is primarily driven by shortcodes.  Enhanced-Calendar does not change the way
in which Calendar adds and manages calendar events.  The [calendar] shortcode has several arguments.  You
could create a post that contains the following content to see how the various options affect the Calendar
output:
<pre style="padding-left: 30px;">[calendar]

[calendar weekday=1]

[calendar weekday=3 descriptions=&#39;off&#39;]

[calendar dateswitcher=&#39;off&#39;]

[calendar weekday=3 dateswitcher=&#39;off&#39; descriptions=&#39;off&#39;]
</pre>

== Styling ==

Enhanced-Calendar moves the CSS styling from a plugin setting which is stored in the database to a separate CSS
file which makes it easy to style Calendar with your own WordPress theme.  The default CSS file shipped with
Enhanced-Calendar is the same as the default settings from the Calendar plugin.

You can see Enhanced-Calendar in action on the <a href="http://www.caslshocks.org">CASL Shocks web site</a>.

== Frequently Asked Questions ==

= Does this plugin replace the Calendar plugin? =

No it does not.  It "enhances" the Calendar plugin hence the name.  The Enhanced-Calendar plugin requires the
original Calendar plugin to be installed and be active.  Management of calendar events continues to be done
using the Calendar plugin.  Enhanced-Calendar provides a way to present the calendar differently and more control
over styling it as part of a theme.

= How did you achieve the styling on the CASL Shocks web site? =

By default Enhanced-Calendar uses the same CSS as calendar but it is loaded via a style sheet included with the
plugin instead of from the database plugin option setting which is the standard plugin operation.

By using style sheets, the CSS can be styled as part of the theme which is exactly what I have done for styling
the Calendar on the <a href="http://www.caslshocksorg">CASL Shocks</a> web site.  The calendar is styled by adding
the following CSS to my theme CSS file:

<code>
/*
* Calendar styling
*
*/
.calendar-heading {
background-color: #EFEFEF !important;
}</code>

<code>.current-day {
background-color: #EFEFEF !important;
}</code>

<code>.normal-day-heading, .weekend-heading, .calendar-date-switcher {
background-color: #EFEFEF !important;
}

.current-day, .day-with-date, .day-without-date {
/*
width: 30px !important;
height: 50px !important;
*/
overflow: hidden;
}

.events {
background: url(../images/soccer/SoccerBallSidebar.png) no-repeat -10px 115%;
}

.event-dagger {
display: inline !important;
font-weight: bold;
font-size: 1.2em;
}

.kjo-link {
display: none !important;
}

.widget_calendar_upcoming, .widget_calendar_today {
border-top: 0px;
padding-top: 10px;
}

.widget_calendar_upcoming ul li, .widget_calendar_today ul li {
border-width: 0px;
padding-bottom: 5px;
padding-left: 5px;
}

</code>

<code>.widget_calendar_upcoming ul li a, .widget_calendar_today ul li a {
padding: 0.3em 0em;
}
</code>

== Changelog ==

= 0.0.1 =
* First release as "Calendar-Plus".

= 0.0.2 =
* Minor jQuery fixes to addresses widget display issues.

= 0.0.3 =
* Renamed Enhanced-Calendar
* Added to WordPress plugin repository

= 0.0.4 =
* ReadMe.txt content and cleanup.

= 0.0.5 =
* More ReadMe.txt content and cleanup.

== Upgrade Notice ==

No specical upgrade instructions at this time.
