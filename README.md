Tenth Template
==============

The Tenth Template is a WordPress template maintained by Tenth Prebyterian Church for the church's website, 
[tenth.org](https://www.tenth.org).

In general, the intent is to keep PHP, WordPress, and plugin versions current, and we will not deliberately maintain 
backwards compatibility.

While you are welcome to adapt this for your own use, note that it is **very** customized for our purposes at Tenth.  
In theory, this template does not require any of the many plugins we use.  However, it does interact with them 
extensively.

The structure of this template is substantially borrowed from 
[TwentyTwenty by Wordpress.org](https://wordpress.org/themes/twentytwenty/), which is itself derived from the Chaplin 
Theme, by Anders Norén.  Chaplin Theme and TwentyTwenty are both distributed under the terms of the GNU GPL version 2.0.

## Noteworthy Features

- The default Admin Bar that comes with WordPress is replaced by links in the user menu of the header nav.  Most links 
 from the admin bar will appear there.  
- Custom header nav layout.  Nearly all of the layout is handled in CSS, including the expansion of expanding menus. 
- Most template files are written in [Twig](https://twig.symfony.com/).

### Possible Gotchas
If you aren't expecting them, there are a few features that might be a surprise:
- The WordPress admin bar will disappear from public-facing pages, and its functionality is moved to the user menu of 
the header.  As a result, a page that does not have a nav also does not have these functions. 
- You will need Timber to parse [Twig](https://twig.symfony.com/).  If you're using Composer, 
[Timber](https://packagist.org/packages/timber/timber) will be installed as a requirement.  If you aren't using 
composer, you will need to install [the Timber Wordpress Plugin](https://wordpress.org/plugins/timber-library/). 

## Copyright & Licensing

The template is licensed under the [AGPL 3.0](LICENSE.md), which is a strong "copyleft" license.  If you modify this 
template and distribute it (including making it available for use over the internet or any other network), you *must* 
make the source code for the modified template available.

While you may see this as a major restriction, we see it as a freedom because other users of the template (like us) 
are able to benefit from your improvements.

Please note that logos and other branding content belonging to Tenth Presbyterian Church are not themselves included in 
this template, and are not available under the same license.  The brand identity of Tenth remains exclusively the 
proprietary property of Tenth.
