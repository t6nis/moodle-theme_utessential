<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The UT Essential theme is built upon the Bootstrapbase theme.
 *
 * @package    theme
 * @subpackage UT Essential
 * @author     Julian (@moodleman) Ridden
 * @author     Based on code originally written by G J Bernard, Mary Evans, Bas Brands, Stuart Lamour and David Scotson.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$haslogo = (!empty($PAGE->theme->settings->logo));
$hasboringlayout = (empty($PAGE->theme->settings->layout)) ? false : $PAGE->theme->settings->layout;
$hasanalytics = (empty($PAGE->theme->settings->useanalytics)) ? false : $PAGE->theme->settings->useanalytics;

theme_utessential_check_colours_switch();
theme_utessential_initialise_colourswitcher($PAGE);

$bodyclasses = array();
$bodyclasses[] = 'utessential-colours-' . theme_utessential_get_colours();
 
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google web fonts -->
    <?php require_once(dirname(__FILE__).'/includes/fonts.php'); ?>
    <!-- iOS Homescreen Icons -->
    <?php require_once(dirname(__FILE__).'/includes/iosicons.php'); ?>
</head>

<body <?php echo $OUTPUT->body_attributes($bodyclasses); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

    <div id="page" class="container-fluid">
	<!-- Start Main Regions -->
    <div id="page-content" class="row-fluid">
        <section id="region-main" class="span12">
        	<div id="page-navbar" class="clearfix">
        		<nav class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></nav>
            	<div class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></div>
        	</div>
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
    </div>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>

<!-- Start Google Analytics -->
<?php if ($hasanalytics) { ?>
	<?php require_once(dirname(__FILE__).'/includes/analytics.php'); ?>
<?php } ?>
<!-- End Google Analytics -->

</body>
</html>
