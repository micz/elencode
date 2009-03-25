=== SI CAPTCHA for Wordpress ===
Contributors: Mike Challis
Donate link: http://www.642weather.com/weather/scripts.php
Tags: captcha, comments, spam
Requires at least: 2.3
Tested up to: 2.7.1
Stable tag: trunk

Adds CAPTCHA anti-spam methods to WordPress on the comment form, registration form, or both.

== Description ==

Adds CAPTCHA anti-spam methods to WordPress on the comment form, registration form, or both.
In order to post comments, users will have to type in the phrase shown on the image.
This can help prevent spam from automated bots.

[Plugin URI]: (http://www.642weather.com/weather/scripts-wordpress-captcha.php)


Requirements/Restrictions:
-------------------------

- Works with Wordpress 2.x.
- PHP 4.0.6 or above with GD2 library support.
- Your theme must have a `<?php do_action('comment_form', $post->ID); ?>` tag inside your comments.php form. Most themes do.
  The best place to locate the tag is before the comment textarea, you may want to move it if it is below the comment textarea.

Captcha Image Support:
---------------------
 * Captcha Image by www.phpcaptcha.org is included
 * Open-source free PHP CAPTCHA script
 * Abstract background with multi colored, angled, and transparent text
 * Arched lines through text
 * Generates audible CAPTCHA files in wav format
 * Refresh button to reload captcha if you cannot read it

Features:
--------
 * Configure from Admin panel
 * JavaScript is not required
 * Allows Trackbacks and Pingbacks
 * Setting to hide the CAPTCHA from logged in users and or admins
 * Setting to show the CAPTCHA on the comment form, registration form, or both
 * I18n language translation support (see FAQ)


== Installation ==

1. Upload the `si-captcha-for-wordpress` folder to the `/wp-content/plugins/` directory

2. Activate the plugin through the `Plugins` menu in WordPress

3. Updates are automatic. Click on "Upgrade Automatically" if prompted from the admin menu. If you ever have to manually upgrade, simply deactivate, uninstall, and repeat the installation steps with the new version. 


= Troubleshooting if the CAPTCHA form fields and image is not being shown: =

Do this as a test:
Activate the SI CAPTCHA plugin and temporarily change your theme to the "Wordpress Default" theme.
Does the captcha image show now?
If it does then the theme you are using is the cause.

Your theme must have a `<?php do_action('comment_form', $post->ID); ?>` tag inside your comments.php form. Most themes do.
  The best place to locate the tag is before the comment textarea, you may want to move it if it is below the comment textarea.
This tag is exactly where the captcha image and captcha code entry will display on the form, so
move the line to before the comment textarea, uncheck the 'Comment Form Rearrange' box on the 'Captcha options' page,
and the problem should be fixed.


= Troubleshooting if the CAPTCHA image itself is not being shown: =

This can happen if a server has too low a default permission level on new folders.
Check and make sure the permission on all the captcha-secureimage folders are set to permission: 755

all these folders need to be 755:
- si-captcha-for-wordpress
  - captcha-secureimage
     - audio
     - gdfonts
     - images


== Screenshots ==

1. screenshot-1.jpg is the captcha on the comment form.

2. screenshot-2.jpg is the captcha on the registration form.

3. screenshot-3.jpg is the `Captcha options` tab on the `Admin Plugins` page.


== Configuration ==

After the plugin is activated, you can configure it by selecting the `Captcha options` tab on the `Admin Plugins` page.
Here is a list of the options:

1. CAPTCHA on Register Form: - Enable CAPTCHA on the register form.

2. CAPTCHA on Comment Form:  - Enable CAPTCHA on the comment form.

3. CAPTCHA on Comment Form:  - Hide CAPTCHA for registered users (select permission level)

4. CAPTCHA on Comment Form:  - CSS class name for CAPTCHA input field on the comment form: 
(Enter a CSS class name only if your theme uses one for comment text inputs. Default is blank for none.)

5. Comment Form Rearrange: - Changes the display order of the catpcha input field on the comment form


== Usage ==

Once activated, a captcha image and captcha code entry is added to the comment and register forms.


== Frequently Asked Questions ==

= Sometimes the captcha image and captcha input field are displayed AFTER the submit button on the comment form. =

Your theme must have a `<?php do_action('comment_form', $post->ID); ?>` tag inside your comments.php form. Most themes do.
  The best place to locate the tag is before the comment textarea, you may want to move it if it is below the comment textarea.
This tag is exactly where the captcha image and captcha code entry will display on the form, so
move the line to before the comment textarea, uncheck the 'Comment Form Rearrange' box on the 'Captcha options' page,
and the problem should be fixed.

= Alternate Fix for the captcha image display order =

You can just check the 'Comment Form Rearrange' box on the admin plugins 'Captcha options' page and javascript will attempt to rearrange it for you. Editing the comments.php, moving the tag, and uncheck the 'Comment Form Rearrange' box on the 'Captcha options' page is the best solution.

= Why is it better to uncheck the 'Comment Form Rearrange' box and move the tag? =
Because the XHTML will no longer validate if it is checked.


= Is this plugin available in other languages? =

Yes. To use a translated version, you need to obtain or make the language file for it. 
At this point it would be useful to read [Installing WordPress in Your Language](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") from the Codex. You will need an .mo file for SI CAPTCHA that corresponds with the "WPLANG" setting in your wp-config.php file. Translations are listed below -- if a translation for your language is available, all you need to do is place it in the `/wp-content/plugins/si-captcha-for-wordpress` directory of your WordPress installation. If one is not available, and you also speak good English, please consider doing a translation yourself (see the next question).


The following translations are included in the download zip file:

* Danish (da_DK) - Translated by [Parry](http://www.detheltnyestore.dk/)
* German (de_DE) - Translated by [Sebastian Kreideweiﬂ](http://www.cps-it.de/)
* Italian (it_IT) - Translated by [Gianni Diurno](http://gidibao.net/ "Gianni Diurno")
* French (fr_FR) - Translated by [Pierre Sudarovich](http://pierre.sudarovich.free.fr/)
* Norwegian (nb_NO) - Translated by [Roger Sylte](http://www.taekwon-do.org/)
* Portuguese brazil (pt_BR) - Translated by [Newton Dan Faoro]
* Turkish (tr_TR) - Translated by [Volkan](http://www.kirpininyeri.com/)


= Are the CAPTCHA audio files available in other languages? =

Portuguese brazil (pt_BR) audio files are available. Wait until after you install the plugin. Download the audio files:
[Portuguese brazil (pt_BR) audio files download](http://www.642weather.com/weather/scripts/captcha-secureimage-pt_BR.zip) and follow instructions in the Readme.txt inside the zip file.

= Can I provide a translation? =

Of course! It will be very gratefully received. Please read [Translating WordPress](http://codex.wordpress.org/Translating_WordPress "Translating WordPress") first for background information on translating. Then obtain the latest [.pot file](http://svn.wp-plugins.org/si-captcha-for-wordpress/trunk/si-captcha.pot ".pot file") and translate it. 
* There are some strings with a space in front or end -- please make sure you remember the space!
* When you have a translation ready, please send the .po and .mo files to wp-translation at 642weather dot com. 
* If you have any questions, feel free to email me also. Thanks!

== Version History ==

rel 1.6 (23 Mar 2009)
-------
- Added new option on configuration page: You can set a CSS class name for CAPTCHA input field on the comment form: 
(Enter a CSS class name only if your theme uses one for comment text inputs. Default is blank for none.)

rel 1.5.4 (19 Mar 2009)
-------
- Updated Danish Language (da_DK) - Translated by [Parry](http://www.detheltnyestore.dk/)

rel 1.5.3 (12 Mar 2009)
-------
- Added German Language (de_DE) - Translated by [Sebastian Kreideweiﬂ](http://www.cps-it.de/)
- Updated Danish Language (da_DK) - Translated by [Parry](http://www.detheltnyestore.dk/)

rel 1.5.2 (24 Feb 2009)
-------
- Added Danish Language (da_DK) - Translated by [Parry](http://www.detheltnyestore.dk/)

rel 1.5.1 (11 Feb 2009)
-------
- Added Portuguese_brazil Language (pt_BR) - Translated by [Newton Dan Faoro]

rel 1.5 (22 Jan 2009)
-------
- Added fix for compatibility with WP Wall plugin. This does NOT add CAPTCHA to WP Wall plugin, it just prevents the "Error: You did not enter a Captcha phrase." when submitting a WP Wall comment.
- Added Norwegian language (nb_NO) - Translated by [Roger Sylte](http://www.taekwon-do.org/)

rel 1.4 (04 Jan 2009)
-------
- Added Turkish language (tr_TR) - Translated by [Volkan](http://www.kirpininyeri.com/)

rel 1.3.3 (02 Jan 2009)
-------
-  Fixed a missing "Refresh Image" language variable

rel 1.3.2 (19 Dec 2008)
-------
-  Added WAI ARIA property aria-required to captcha input form for more accessibility

rel 1.3.1 (17 Dec 2008)
-------
- Changed screenshots to WP 2.7
- Better detection of GD and a few misc. adjustments

rel 1.3 (14 Dec 2008)
-------
- Added language translation to the permissions drop down select on the options admin page, thanks Pierre
- Added French language (fr_FR) - Translated by [Pierre Sudarovich](http://pierre.sudarovich.free.fr/)

rel 1.2.1 (23 Nov 2008)
-------
- Fixed compatibility with custom `WP_PLUGIN_DIR` installations

rel 1.2 (23 Nov 2008)
-------
- Fixed install path from `si-captcha` to `si-captcha-for-wordpress` so automatic update works correctly

rel 1.1.1 (22 Nov 2008)
-------
- Added Italian language (it_IT) - Translated by [Gianni Diurno](http://gidibao.net/ "Gianni Diurno")

rel 1.1 (21 Nov 2008)
-------
- Added I18n language translation feature

rel 1.0 (21 Aug 2008)
-------
- Initial Release



