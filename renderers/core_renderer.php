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
 * UT Essential theme with the underlying Bootstrap theme.
 *
 * @package    theme
 * @subpackage UT Essential
 * @author     Julian (@moodleman) Ridden
 * @author     Based on code originally written by G J Barnard, Mary Evans, Bas Brands, Stuart Lamour and David Scotson.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 include_once($CFG->libdir . '/coursecatlib.php');
 
 class theme_utessential_core_renderer extends theme_bootstrapbase_core_renderer {
 	
 	/*
     * This renders a notification message.
     * Uses bootstrap compatible html.
     */
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);
        $type = '';

        if ($classes == 'notifyproblem') {
            $type = 'alert alert-error';
        }
        if ($classes == 'notifysuccess') {
            $type = 'alert alert-success';
        }
        if ($classes == 'notifymessage') {
            $type = 'alert alert-info';
        }
        if ($classes == 'redirectmessage') {
            $type = 'alert alert-block alert-info';
        }
        return "<div class=\"$type\">$message</div>";
    } 
    
    /*
     * This renders the navbar.
     * Uses bootstrap compatible html.
     */
    public function navbar() {
        global $COURSE;
        $items = $this->page->navbar->get_items();
        $breadcrumbs = array();
        //22.10.2012 - changing logic of navbar
        $titles_arr = array('Osalemiskontroll', 'General', 'Kursuse üldosa');
        $keys_arr = array('mycourses', 'courses');
        foreach ($items as $item) {

            if ($item->action === null) {
                continue;
            }            
            if ($item->title === '') {
                continue;
            }
            if (in_array($item->title, $titles_arr)) {
                continue;
            }
            if (in_array((string)$item->key, $keys_arr)) {
                continue;
            }
            $item->hideicon = true;
            $breadcrumbs[] = $this->render($item);
        }
        $divider = '<span class="divider">'.get_separator().'</span>';
        $list_items = '<li>'.join(" $divider</li><li>", $breadcrumbs).'</li>';
        $title = '<span class="accesshide">'.get_string('pagepath').'</span>';
        return $title . "<ul class=\"breadcrumb\">$list_items</ul>";
    }
    
    /**
     * Outputs the page's footer
     * @return string HTML fragment
     */
    public function footer() {
        global $CFG, $DB, $USER;

        $output = $this->container_end_all(true);

        $footer = $this->opencontainers->pop('header/footer');

        if (debugging() and $DB and $DB->is_transaction_started()) {
            // TODO: MDL-20625 print warning - transaction will be rolled back
        }

        // Provide some performance info if required
        $performanceinfo = '';
        if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
            $perf = get_performance_info();
            if (defined('MDL_PERFTOLOG') && !function_exists('register_shutdown_function')) {
                error_log("PERF: " . $perf['txt']);
            }
            if (defined('MDL_PERFTOFOOT') || debugging() || $CFG->perfdebug > 7) {
                if ($USER->id == 2) {                    
                    $performanceinfo = utessential_performance_output($perf);
                    print_r($perf);
                }
            }
        }

        $footer = str_replace($this->unique_performance_info_token, $performanceinfo, $footer);

        $footer = str_replace($this->unique_end_html_token, $this->page->requires->get_end_code(), $footer);

        $this->page->set_state(moodle_page::STATE_DONE);

        if(!empty($this->page->theme->settings->persistentedit) && property_exists($USER, 'editing') && $USER->editing && !$this->really_editing) {
            $USER->editing = false;
        }

        return $output . $footer;
    }

    
/**
 * Get a list of essential user navigation items.
 *
 * @param stdclass $user user object.
 * @param moodle_page $page page object.
 * @param array $options associative array.
 *     options are:
 *     - avatarsize=35 (size of avatar image)
 * @return stdClass $returnobj navigation information object, where:
 *
 *      $returnobj->navitems    array    array of links where each link is a
 *                                       stdClass with fields url, title, and
 *                                       pix
 *      $returnobj->metadata    array    array of useful user metadata to be
 *                                       used when constructing navigation;
 *                                       fields include:
 *
 *          ROLE FIELDS
 *          asotherrole    bool    whether viewing as another role
 *          rolename       string  name of the role
 *
 *          USER FIELDS
 *          These fields are for the currently-logged in user, or for
 *          the user that the real user is currently logged in as.
 *
 *          userid         int        the id of the user in question
 *          userfullname   string     the user's full name
 *          userprofileurl moodle_url the url of the user's profile
 *          useravatar     string     a HTML fragment - the rendered
 *                                    user_picture for this user
 *          userloginfail  string     an error string denoting the number
 *                                    of login failures since last login
 *
 *          "REAL USER" FIELDS
 *          These fields are for when asotheruser is true, and
 *          correspond to the underlying "real user".
 *
 *          asotheruser        bool    whether viewing as another user
 *          realuserid         int        the id of the user in question
 *          realuserfullname   string     the user's full name
 *          realuserprofileurl moodle_url the url of the user's profile
 *          realuseravatar     string     a HTML fragment - the rendered
 *                                        user_picture for this user
 *
 *          MNET PROVIDER FIELDS
 *          asmnetuser            bool   whether viewing as a user from an
 *                                       MNet provider
 *          mnetidprovidername    string name of the MNet provider
 *          mnetidproviderwwwroot string URL of the MNet provider
 */

    
     /**
      * Return the standard string that says whether you are logged in (and switched
      * roles/logged in as another user).
      * @param bool $withlinks if false, then don't include any links in the HTML produced.
      * If not set, the default is the nologinlinks option from the theme config.php file,
      * and if that is not set, then links are included.
      * @return string HTML fragment.
      */
     public function login_info($withlinks = null) {
        global $USER, $CFG, $DB, $SESSION;
        $user = $USER;
        require_once($CFG->dirroot . '/user/lib.php');

        if (is_null($user)) {
            $user = $USER;
        }
        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = 'usermenu';
        if (!$withlinks) {
            $usermenuclasses .= ' withoutlinks';
        }
        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            $returnstr = get_string('loggedinnot', 'moodle');
            if (!$loginpage) {
                $returnstr .= " <a href=\"$loginurl\" class=\"login-button\">" . get_string('login') . '</a>';
            }
            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'logininfo'
                ),
                $usermenuclasses
            );

        }

        // If logged in as a guest user, show a string to that effect.
        if (isguestuser()) {
            $returnstr = get_string('loggedinasguest');
            if (!$loginpage && $withlinks) {
                $returnstr .= " <a href=\"$loginurl\">".get_string('login').'</a>';
            }

            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'logininfo'
                ),
                $usermenuclasses
            );
        }

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page);
        $usertextcontents = $opts->metadata['userfullname'];

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $usertextcontents = $opts->metadata['realuserfullname'];
            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                        $opts->metadata['userfullname'],
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['rolename'],
                'meta role role-' . $role
            );
        }

        // User login failures.
        /* if (!empty($opts->metadata['userloginfail'])) {
            $usertextcontents .= html_writer::span(
                $opts->metadata['userloginfail'],
                'meta loginfailures'
            );
        }*/

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        $arr = '';
        foreach ($opts->navitems as $key => $value) {
            switch ($value->itemtype) {
                case 'divider':
                    // If the nav item is a divider, add one and skip link processing.
                    //$am->add($divider);
                    break;
                case 'invalid':
                    // Silently skip invalid entries (should we post a notification?).
                    break;
                case 'link':
                    // Process this as a link item.
                    $pix = null;
                    if (isset($value->pix) && !empty($value->pix)) {
                        $pix = new pix_icon($value->pix, $value->title, null, array('class' => 'iconsmall'));
                    } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                        $value->title = html_writer::img(
                            $value->imgsrc,
                            $value->title,
                            array('class' => 'iconsmall')
                        ) . $value->title;
                    }
                    $attrs = array();
                    $attrs['class'] = 'icon';
                    if (!empty($value->titleidentifier)) {
                        $attrs['data-title'] = $value->titleidentifier;
                    }
                    $arr .= '<li>'.html_writer::link(
                        $value->url,
                        $this->render($pix).$value->title,
                        $attrs
                    ).'</li>';
                    break;
            }
        }
        
        return html_writer::link(new moodle_url('index.php'), $opts->metadata['userfullname'].'<b class="caret"></b>', 
            array(
                'class' => 'toggle-display dropdown-toggle',
                'data-toggle' => 'dropdown',
                'title' => $opts->metadata['userfullname']
            )
        ).html_writer::tag('ul', $arr, array('class' => 'dropdown-menu'));
     }

    /**
     * THIS SHOULDNT BE HERE BUT FOR NOW ITS ALRGIHT!!
     * 
     * Returns list of courses current $USER is enrolled in and can access
     *
     * - $fields is an array of field names to ADD
     *   so name the fields you really need, which will
     *   be added and uniq'd
     *
     * @param string|array $fields
     * @param string $sort
     * @param int $limit max number of courses
     * @return array
     */
    function utessential_enrol_get_my_courses($fields = NULL, $sort = 'visible DESC,sortorder ASC', $limit = 0) {
        global $DB, $USER;

        // Guest account does not have any courses
        if (isguestuser() or !isloggedin()) {
            return(array());
        }

        $basefields = array('id', 'category', 'sortorder',
                            'shortname', 'fullname', 'idnumber',
                            'startdate', 'visible',
                            'groupmode', 'groupmodeforce', 'cacherev');

        if (empty($fields)) {
            $fields = $basefields;
        } else if (is_string($fields)) {
            // turn the fields from a string to an array
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
            $fields = array_unique(array_merge($basefields, $fields));
        } else if (is_array($fields)) {
            $fields = array_unique(array_merge($basefields, $fields));
        } else {
            throw new coding_exception('Invalid $fileds parameter in enrol_get_my_courses()');
        }
        if (in_array('*', $fields)) {
            $fields = array('*');
        }

        $orderby = "";
        $sort    = trim($sort);
        if (!empty($sort)) {
            $rawsorts = explode(',', $sort);
            $sorts = array();
            foreach ($rawsorts as $rawsort) {
                $rawsort = trim($rawsort);
                if (strpos($rawsort, 'c.') === 0) {
                    $rawsort = substr($rawsort, 2);
                }
                $sorts[] = trim($rawsort);
            }
            $sort = 'c.'.implode(',c.', $sorts);
            $orderby = "ORDER BY ua.timeaccess DESC, $sort";
        }

        $wheres = array("c.id <> :siteid");
        $params = array('siteid'=>SITEID);

        if (isset($USER->loginascontext) and $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
            // list _only_ this course - anything else is asking for trouble...
            $wheres[] = "courseid = :loginas";
            $params['loginas'] = $USER->loginascontext->instanceid;
        }

        $coursefields = 'c.' .join(',c.', $fields);
        $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
        $params['contextlevel'] = CONTEXT_COURSE;
        $wheres = implode(" AND ", $wheres);

        //note: we can not use DISTINCT + text fields due to Oracle and MS limitations, that is why we have the subselect there
        $sql = "SELECT $coursefields $ccselect
                  FROM {course} c
                  JOIN (SELECT DISTINCT e.courseid
                          FROM {enrol} e
                          JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)                          
                         WHERE ue.status = :active AND e.status = :enabled AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)
                       ) en ON (en.courseid = c.id)
                $ccjoin
                JOIN {user_lastaccess} ua ON (ua.courseid = c.id AND ua.userid = :userid1)
                WHERE $wheres
              $orderby";
        $params['userid']  = $USER->id;
        $params['userid1']  = $USER->id;
        $params['active']  = ENROL_USER_ACTIVE;
        $params['enabled'] = ENROL_INSTANCE_ENABLED;
        $params['now1']    = round(time(), -2); // improves db caching
        $params['now2']    = $params['now1'];
        $courses = $DB->get_records_sql($sql, $params, 0, $limit);

        // preload contexts and check visibility
        foreach ($courses as $id=>$course) {
            context_helper::preload_from_record($course);
            if (!$course->visible) {
                if (!$context = context_course::instance($id, IGNORE_MISSING)) {
                    unset($courses[$id]);
                    continue;
                }
                if (!has_capability('moodle/course:viewhiddencourses', $context)) {
                    unset($courses[$id]);
                    continue;
                }
            }
            $courses[$id] = $course;
        }

        //wow! Is that really all? :-D

        return $courses;
    }

    protected function render_custom_menu(custom_menu $menu) {
        global $USER, $CFG, $PAGE, $OUTPUT;
    	/*
    	* This code replaces adds the current enrolled
    	* courses to the custommenu.
    	*/
    
    	$hasdisplaymycourses = (empty($this->page->theme->settings->displaymycourses)) ? false : $this->page->theme->settings->displaymycourses;
        if (isloggedin() && !isguestuser() && $hasdisplaymycourses) {
        	$mycoursetitle = $this->page->theme->settings->mycoursetitle;
            if ($mycoursetitle == 'module') {
				$branchtitle = get_string('mymodules', 'theme_utessential');
			} else if ($mycoursetitle == 'unit') {
				$branchtitle = get_string('myunits', 'theme_utessential');
			} else if ($mycoursetitle == 'class') {
				$branchtitle = get_string('myclasses', 'theme_utessential');
			} else {
				$branchtitle = get_string('mycourses', 'theme_utessential');
			}
			$branchlabel = '<i class="fa fa-briefcase"></i>'.$branchtitle;
            $branchurl   = new moodle_url('/my/index.php');
            $branchsort  = 10000;
 
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
                if ($courses = $this->utessential_enrol_get_my_courses(NULL, 'fullname ASC', 21)) {
                    foreach ($courses as $course) {
                        if ($course->visible){
                            $branch->add(format_string($course->fullname), new moodle_url('/course/view.php?id='.$course->id), format_string($course->shortname));
                        }
                    }
                    $branch->add('<em>'.ucfirst(get_string('more')).'..</em>', new moodle_url('/user/profile.php?id='.$USER->id.'&showallcourses=1'));
                } else {
                    $noenrolments = get_string('noenrolments', 'theme_utessential');
                    $branch->add('<em>'.$noenrolments.'</em>', new moodle_url('/'), $noenrolments);
                }
        }
        
        /*
    	* This code replaces adds the My Dashboard
    	* functionality to the custommenu.
    	*/
        //$hasdisplaymydashboard = (empty($this->page->theme->settings->displaymydashboard)) ? false : $this->page->theme->settings->displaymydashboard;
        /*if (isloggedin() && !isguestuser() && $hasdisplaymydashboard) {
            $branchlabel = '<i class="fa fa-dashboard"></i>'.get_string('mydashboard', 'theme_utessential');
            $branchurl   = new moodle_url('/my/index.php');
            $branchtitle = get_string('mydashboard', 'theme_utessential');
            $branchsort  = 10000;
 
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
 			$branch->add('<em><i class="fa fa-user"></i>'.get_string('profile').'</em>',new moodle_url('/user/profile.php?id='.$USER->id),get_string('profile'));
                        //$branch->add('<em><i class="fa fa-wrench"></i>'.get_string('preferences').'</em>',new moodle_url('/user/preferences.php'),get_string('preferences'));
                        //$branch->add('<em><i class="fa fa-certificate"></i>'.get_string('pluginname', 'gradereport_overview').'</em>',new moodle_url('/grade/report/overview/index.php'),get_string('pluginname', 'gradereport_overview'));
 			$branch->add('<em><i class="fa fa-calendar"></i>'.get_string('pluginname', 'block_calendar_month').'</em>',new moodle_url('/calendar/view.php'),get_string('pluginname', 'block_calendar_month'));
 			$branch->add('<em><i class="fa fa-envelope"></i>'.get_string('pluginname', 'block_messages').'</em>',new moodle_url('/message/index.php'),get_string('pluginname', 'block_messages'));
 			$branch->add('<em><i class="fa fa-asterisk"></i>'.get_string('badges').'</em>',new moodle_url('/badges/mybadges.php'),get_string('badges'));
 			$branch->add('<em><i class="fa fa-file"></i>'.get_string('privatefiles', 'block_private_files').'</em>',new moodle_url('/user/files.php'),get_string('privatefiles', 'block_private_files'));
 			$branch->add('<em><i class="fa fa-sign-out"></i>'.get_string('logout').'</em>',new moodle_url('/login/logout.php'),get_string('logout'));    
        }*/
        
        /*
         * This code adds the Theme colors selector to the custommenu.
         */
        if (isloggedin() && !isguestuser()) {
            $alternativethemes = array();
            foreach (range(1, 3) as $alternativethemenumber) {
                if (!empty($this->page->theme->settings->{'enablealternativethemecolors' . $alternativethemenumber})) {
                    $alternativethemes[] = $alternativethemenumber;
                }
            }
            if (!empty($alternativethemes)) {
                $branchtitle = get_string('themecolors', 'theme_utessential');
                $branchlabel = '<i class="fa fa-th-large"></i>' . $branchtitle;
                $branchurl   = new moodle_url('/my/index.php');
                $branchsort  = 11000;
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
                
                $defaultthemecolorslabel = get_string('defaultcolors', 'theme_utessential');
                $branch->add('<i class="fa fa-square colours-default"></i>' . $defaultthemecolorslabel,
                        new moodle_url($this->page->url, array('utessentialcolours' => 'default')), $defaultthemecolorslabel);
                foreach ($alternativethemes as $alternativethemenumber) {
                    if (!empty($this->page->theme->settings->{'alternativethemename' . $alternativethemenumber})) {
                        $alternativethemeslabel = $this->page->theme->settings->{'alternativethemename' . $alternativethemenumber};
                    } else {
                        $alternativethemeslabel = get_string('alternativecolors', 'theme_utessential', $alternativethemenumber);
                    }
                    $branch->add('<i class="fa fa-square colours-alternative' .  $alternativethemenumber . '"></i>' . $alternativethemeslabel,
                            new moodle_url($this->page->url, array('utessentialcolours' => 'alternative' . $alternativethemenumber)), $alternativethemeslabel);
                }
            }
        }
 
         // TODO: eliminate this duplicated logic, it belongs in core, not
        // here. See MDL-39565.
        $addlangmenu = true;
        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2
            or empty($CFG->langmenu)
            or ($this->page->course != SITEID and !empty($this->page->course->lang))) {
            $addlangmenu = false;
        }

        if (!$menu->has_children() && $addlangmenu === false) {
            return '';
        }

        if ($addlangmenu) {
            $strlang =  get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $currentlang = '<i class="fa fa-flag"></i>'.$currentlang;
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
            
        }
        
        //20.06.2014 - Categories dropdown
        if (!isloggedin() || isguestuser()) {
            //Category list
            $categories = coursecat::get(0)->get_children();  // Parent = 0   ie top-level categories only
            $icon = '<img src="' . $OUTPUT->pix_url('i/course') . '" class="icon" alt="" />';
            if ($categories) {   //Check we have categories
                if (count($categories) > 1 || (count($categories) == 1)) {     // Just print top level category links
                    $this->categories = $menu->add(get_string('categories'));
                    foreach ($categories as $category) {
                        $categoryname = $category->get_formatted_name();
                        $linkcss = $category->visible ? "" : " class=\"dimmed\" ";
                        $this->categories->add($icon.$categoryname, new moodle_url('/course/index.php', array('categoryid' => $category->id)));
                        //"<a $linkcss href=\"$CFG->wwwroot/course/index.php?categoryid=$category->id\">".$icon . $categoryname . "</a>";
                    }
                }
            }
        }
        
        $content = '<ul class="nav">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }
        
        //20.06.2014 - Navbar course search
        if (!isloggedin() || isguestuser()) {
            //Course search
            $sform = html_writer::start_tag('form', array('id' => 'navbarcoursesearch', 'action' => new moodle_url('/course/search.php'), 'method' => 'get'));
            $sform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'navsearchbox',
                'size' => 20, 'name' => 'search', 'value' => '', 'placeholder' => get_string('searchcourses')));
            $sform .= '<button class="fa fa-search"></button>';
            $sform .= html_writer::end_tag('form');
            $content .= '<li>'.$sform.'</li>';
        }
            
        return $content.'</ul>';
    }
    
 	/*
    * This code replaces the icons in the Admin block with
    * FontAwesome variants where available.
    */
    
	protected function render_pix_icon(pix_icon $icon) {
		if (self::replace_moodle_icon($icon->pix) !== false && $icon->attributes['alt'] === '') {
			return self::replace_moodle_icon($icon->pix);
		} else {
			return parent::render_pix_icon($icon);
		}
	}
     
    private static function replace_moodle_icon($name) {
        $icons = array(
            'add' => 'plus',
            'book' => 'book',
            'chapter' => 'file',
            'docs' => 'question-sign',
            'generate' => 'gift',
            'i/backup' => 'cloud-download',
            'i/checkpermissions' => 'user',
            'i/edit' => 'pencil',
            'i/filter' => 'filter',
            'i/grades' => 'table',
            'i/group' => 'group',
            'i/hide' => 'eye',
            'i/import' => 'upload',
            'i/move_2d' => 'arrows',
            'i/navigationitem' => 'circle',
            'i/outcomes' => 'magic',
            'i/publish' => 'globe',
            'i/reload' => 'refresh',
            'i/report' => 'list-alt',
            'i/restore' => 'cloud-upload',
            'i/return' => 'repeat',
            'i/roles' => 'user',
            'i/settings' => 'cogs',
            'i/show' => 'eye-slash',
            'i/switchrole' => 'random',
            'i/user' => 'user',
            'i/users' => 'user',
            't/right' => 'arrow-right',
            't/left' => 'arrow-left',
        );
        if (isset($icons[$name])) {
            return "<i class=\"fa fa-$icons[$name]\" id=\"icon\"></i>";
        } else {
            return false;
        }
    }
    
    /**
    * Get the HTML for blocks in the given region.
    *
    * @since 2.5.1 2.6
    * @param string $region The region to get HTML for.
    * @return string HTML.
    * Written by G J Barnard
    */
    
    public function utessentialblocks($region, $classes = array(), $tag = 'aside') {
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = array(
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $region),
            'class' => join(' ', $classes),
            'data-blockregion' => $region,
            'data-droptarget' => '1'
        );
        return html_writer::tag($tag, $this->blocks_for_region($region), $attributes);
    }
    
    /**
    * Returns HTML to display a "Turn editing on/off" button in a form.
    *
    * @param moodle_url $url The URL + params to send through when clicking the button
    * @return string HTML the button
    * Written by G J Barnard
    */
    
    public function edit_button(moodle_url $url) {
        $url->param('sesskey', sesskey());    
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $btn = 'btn-danger';
            $title = get_string('turneditingoff');
            $icon = 'fa-power-off';
        } else {
            $url->param('edit', 'on');
            $btn = 'btn-success';
            $title = get_string('turneditingon');
            $icon = 'fa-edit';
        }
        return html_writer::tag('a', html_writer::start_tag('i', array('class' => $icon.' fa fa-fw')).
               html_writer::end_tag('i'), array('href' => $url, 'class' => 'btn '.$btn, 'title' => $title));
    }

    /**
     * Internal implementation of user image rendering.
     *
     * @param user_picture $userpicture
     * @return string
     */
    protected function render_user_picture(user_picture $userpicture) {
        global $CFG, $DB, $OUTPUT;

        $user = $userpicture->user;

        if ($userpicture->alttext) {
            if (!empty($user->imagealt)) {
                $alt = $user->imagealt;
            } else {
                $alt = get_string('pictureof', '', fullname($user));
            }
        } else {
            $alt = '';
        }

        if (empty($userpicture->size)) {
            $size = 35;
        } else if ($userpicture->size === true or $userpicture->size == 1) {
            $size = 100;
        } else {
            $size = $userpicture->size;
        }

        $class = $userpicture->class;

        if ($user->picture == 0) {
            $class .= ' defaultuserpic';
        }

        $src = $userpicture->get_url($this->page, $this);
        //dont show images in development
        if ($CFG->wwwroot == 'https://moodle2.ut.ee') {
            $src = $OUTPUT->pix_url('f1', 'theme');            
        } else {
            $src = $userpicture->get_url($this->page, $this);
        }
        $attributes = array('src'=>$src, 'alt'=>$alt, 'title'=>$alt, 'class'=>$class, 'width'=>$size, 'height'=>$size);

        // get the image html output fisrt
        $output = html_writer::empty_tag('img', $attributes);

        // then wrap it in link if needed
        if (!$userpicture->link) {
            return $output;
        }

        if (empty($userpicture->courseid)) {
            $courseid = $this->page->course->id;
        } else {
            $courseid = $userpicture->courseid;
        }

        if ($courseid == SITEID) {
            $url = new moodle_url('/user/profile.php', array('id' => $user->id));
        } else {
            $url = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $courseid));
        }

        $attributes = array('href'=>$url);

        if ($userpicture->popup) {
            $id = html_writer::random_id('userpicture');
            $attributes['id'] = $id;
            $this->add_action_handler(new popup_action('click', $url), $id);
        }

        return html_writer::tag('a', $output, $attributes);
    }
}

/* Topics renderer */
include_once($CFG->dirroot . "/course/format/topics/renderer.php");
 
class theme_utessential_format_topics_renderer extends format_topics_renderer {
    
    protected function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $previousarrow= '<i class="fa fa-chevron-circle-left"></i>';
        $nextarrow= '<i class="fa fa-chevron-circle-right"></i>';
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > 0 and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array('id' => 'previous_section');
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::start_tag('div', array('class' => 'nav_icon'));
                $previouslink .= $previousarrow;
                $previouslink .= html_writer::end_tag('div');
                $previouslink .= html_writer::start_tag('span', array('class' => 'text'));
                $previouslink .= html_writer::start_tag('span', array('class' => 'nav_guide'));
                $previouslink .= get_string('previoussection', 'theme_utessential');
                $previouslink .= html_writer::end_tag('span');
                $previouslink .= html_writer::empty_tag('br');
                $previouslink .= get_section_name($course, $sections[$back]);
                $previouslink .= html_writer::end_tag('span');
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        while ($forward <= $course->numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array('id' => 'next_section');
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = html_writer::start_tag('div', array('class' => 'nav_icon'));
                $nextlink .= $nextarrow;
                $nextlink .= html_writer::end_tag('div');
                $nextlink .= html_writer::start_tag('span', array('class' => 'text'));
                $nextlink .= html_writer::start_tag('span', array('class' => 'nav_guide'));
                $nextlink .= get_string('nextsection', 'theme_utessential');
                $nextlink .= html_writer::end_tag('span');
                $nextlink .= html_writer::empty_tag('br');
                $nextlink .= get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::end_tag('span');
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }
    
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();
        }

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
        // Title attributes
        $titleattr = 'mdl-align title';
        if (!$thissection->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= html_writer::tag('div', get_section_name($course, $displaysection), array('class' => $titleattr));
        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('nav', array('id' => 'section_footer'));
        $sectionbottomnav .= $sectionnavlinks['previous']; 
        $sectionbottomnav .= $sectionnavlinks['next']; 
        // $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection), array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::empty_tag('br', array('style'=>'clear:both'));
        $sectionbottomnav .= html_writer::end_tag('nav');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }
}

//Require course renderer
require_once($CFG->dirroot . '/course/renderer.php');

class theme_utessential_core_course_renderer extends core_course_renderer {
    
    //Check if module exists in course_modules
    private function check_course_module_exists($cmid) {
        global $DB;

        if ($DB->record_exists('course_modules', array('id' => $cmid))) {
            return true;
        } else {            
            return false;
        }
    }
    
    /**
     * Renders HTML to display a list of course modules in a course section
     * Also displays "move here" controls in Javascript-disabled mode
     *
     * This function calls {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param int $displayoptions
     * @return void
     */
    public function course_section_cm_list($course, $section, $sectionreturn = null, $displayoptions = array()) {
        global $USER, $DB;

        $output = '';
        $modinfo = get_fast_modinfo($course);
        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // check if we are currently in the process of moving a module with JavaScript disabled
        $ismoving = $this->page->user_is_editing() && ismoving($course->id);
        if ($ismoving) {
            $movingpix = new pix_icon('movehere', get_string('movehere'), 'moodle', array('class' => 'movetarget'));
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one)
        $moduleshtml = array();
        // Itemlist hack...
        //* 13.03.2012 Custom code start *//  
        $sectionmods = explode(',', $section->sequence);
        $cmarr = $sectionmods; // Course modules array
        $temp_vals = array(); // Temporary array for manipulation
        $listmap = array(); // Mapping purpose
        
        if (isset($modinfo->instances['itemslist'])) {
            foreach ($modinfo->instances['itemslist'] as $key => $value) {
                $cm = get_coursemodule_from_instance('itemslist', $key, $course->id);
                $modsection = $DB->get_record('course_sections', array('id' => $cm->section));
                $listmap[$cm->id] = $key;                
                if ($section->section == $modsection->section) {
                    $all_items = $DB->get_record('itemslist', array('id' => $key), 'id, items');
                    if (!empty($all_items->items)) {
                        $items = explode(',', $all_items->items);
                        /* 28.10.2016 - Remove notice. For missing/deleted items.*/
                        foreach($items as $key3 => $value3) {
                            if (!$this->check_course_module_exists($value3)) {
                                unset($items[$key3]);
                            }
                        }
                        // 28.10.2016 - Keep the list always update.
                        $itemobject = new stdClass();
                        $itemobject->id = $key;
                        $itemobject->course = $course->id;
                        $itemobject->listsection = $section->section;
                        $itemobject->items = implode(',', $items);
                        $DB->update_record('itemslist', $itemobject);
                        //
                        $all_lists[$modsection->section][$all_items->id] = $items;           
                        foreach ($items as $key1 => $value1) {                            
                            if ($this->check_course_module_exists($value1)) {
                                $key2 = array_search($value1, $cmarr);
                                $temp_vals[$cm->id][$key2] = $value1;
                                unset($cmarr[$key2]);
                            }
                        }
                    }
                }                
            }

            $order = '';
            $xi1 = 0;
            $maxxi1 = count($cmarr); 
            $farr = array();

            foreach ($cmarr as $key1 => $value1) {
                $xi1++;
                if (array_key_exists($value1, $temp_vals)) {
                    
                    $order .= $value1.($xi1 < $maxxi1 ? ',' : '');
                    $farr[] = $value1;
                    $xi2 = 0;
                    $maxxi2 = count($temp_vals[$value1]);
                    ksort($temp_vals[$value1]);
                    $listorder = '';
                    
                    foreach ($temp_vals[$value1] as $key2 => $value2) {
                        $xi2++;
                        $order .= $value2.(($xi2 < $maxxi2) || ($xi1 < $maxxi1) ? ',' : '');
                        $listorder .= $value2.($xi2 < $maxxi2 ? ',' : '');
                        $farr[] = $value2;
                    }
        
                    $itemobject = new stdClass();
                    $itemobject->id = $listmap[$value1];
                    $itemobject->course = $course->id;
                    $itemobject->listsection = $section->section;
                    $itemobject->items = $listorder;
                    $DB->update_record('itemslist', $itemobject);                    
                } else {                    
                    $order .= $value1.($xi1 < $maxxi1 ? ',' : '');
                    $farr[] = $value1;
                }
            }
            $cmarr = $farr;
            $reorder = implode(',', $cmarr);
            $section->sequence = $reorder;
            $sectionmods = $cmarr;
        }
        //* Custom code end */
        
        // Custom vars
        $critems = array();
        $itemslistmod = '';
        
        if (!empty($modinfo->sections[$section->section])) {
            
            foreach ($sectionmods as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // do not display moving mod
                    continue;
                }
                
                $modcontext = context_module::instance($mod->id);
                $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $modcontext);
                
                if (!$canviewhidden && !empty($itemslistmod) && $mod->modname == 'itemslist') {
                    // 25.03.2013 if itemslist is totally hidden skip continuing processes.
                    if (!$itemslistmod->visible) {
                        continue;
                    }
                }
                
                //21.11.2012 - itemslist improvement
                if ($mod->modname == 'itemslist') {
                    $itemslistmod = get_coursemodule_from_id('itemslist', $mod->id, $course->id);
                    if (!empty($all_lists) && $itemslistmod->visible && in_array($itemslistmod->instance, $all_lists[$section->section])) {
                        $critems = $all_lists[$section->section][$itemslistmod->instance];
                    }
                }
                
                //Code end

                if ($modulehtml = $this->course_section_cm($course,
                        $completioninfo, $mod, $sectionreturn, $displayoptions, $modcontext, $canviewhidden, $itemslistmod, $critems)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        if (!empty($moduleshtml) || $ismoving) {

            $output .= html_writer::start_tag('ul', array('class' => 'section img-text'));

            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $output .= html_writer::tag('li', html_writer::link($movingurl, $this->output->render($movingpix)),
                            array('class' => 'movehere', 'title' => $strmovefull));
                }

                $mod = $modinfo->cms[$modnumber];
                
                //Code start
                $modcontext = context_module::instance($mod->id);
                $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $modcontext);
                // modified 09.12.2016
                if (!$canviewhidden) {
                    if (in_array($mod->id, $critems)) {
                        $output .= html_writer::end_tag('li');
                        continue;
                    }
                }
                //Code end
                
                $modclasses = 'activity '. $mod->modname. ' modtype_'.$mod->modname. ' '. $mod->extraclasses;
                $output .= html_writer::start_tag('li', array('class' => $modclasses, 'id' => 'module-'. $mod->id));
                $output .= $modulehtml;
                
                //* Code Start 13.03.2012 *//           
                if ($mod->modname == 'itemslist') {
                    $cm = get_coursemodule_from_id('itemslist', $mod->id);
                    $asd = $DB->get_record('itemslist', array('id' => $cm->instance));
                    if (!empty($asd->items)) {
                        $items = explode(',', $asd->items);
                    } else {
                        continue;
                    }
                }
                
                if ((!$ismoving && !empty($items) && end($items) == $mod->id)) {
                    $output .= html_writer::end_tag('ul');
                }

                if (!$ismoving && !empty($items) && $mod->modname == 'itemslist') {
                    $output .= html_writer::start_tag('ul', array('class' => 'itemlistcount', 'id' => 'itemlist-'.$mod->id));
                    continue;
                }
                //* Code End *//
                $output .= html_writer::end_tag('li');
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $output .= html_writer::tag('li', html_writer::link($movingurl, $this->output->render($movingpix)),
                        array('class' => 'movehere', 'title' => $strmovefull));
            }

            $output .= html_writer::end_tag('ul'); // .section
        }

        return $output;
    }

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link cm_info::get_after_link()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array(), $modcontext = '', $canviewhidden = '', $cmod = '', $critems = '') {
        $output = '';
        // We return empty string (because course module will not be displayed at all)
        // if:
        // 1) The activity is not visible to users
        // and
        // 2a) The 'showavailability' option is not set (if that is set,
        //     we need to display the activity so we can show
        //     availability info)
        // or
        // 2b) The 'availableinfo' is empty, i.e. the activity was
        //     hidden in a way that leaves no info, such as using the
        //     eye icon.

        if (!$mod->uservisible && (empty($mod->availableinfo))) {
            //21.11.2012 - custom code
            if (!$canviewhidden && !empty($cmod)) {  
                // modified 09.12.2016 
                /*if (isset($critems) && in_array($mod->id, $critems) && end($critems) == $mod->id) {
                    $output = html_writer::end_tag('li');
                }*/
                $output = html_writer::end_tag('li');
            }
            //code end
            return $output;
        }

        $indentclasses = 'mod-indent';
        if (!empty($mod->indent)) {
            $indentclasses .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $indentclasses .= ' mod-indent-huge';
            }
        }
        $output .= html_writer::start_tag('div');

        if ($this->page->user_is_editing()) {
            $output .= course_get_cm_move($mod, $sectionreturn);
        }

        $output .= html_writer::start_tag('div', array('class' => 'mod-indent-outer'));

        // This div is used to indent the content.
        $output .= html_writer::div('', $indentclasses);

        // Start a wrapper for the actual content to keep the indentation consistent
        $output .= html_writer::start_tag('div');

        // Display the link to the module (or do nothing if module has no url)
        $cmname = $this->course_section_cm_name($mod, $displayoptions);

        if (!empty($cmname)) {
            // Start the div for the activity title, excluding the edit icons.
            $output .= html_writer::start_tag('div', array('class' => 'activityinstance'));
            $output .= $cmname;


            /*if ($this->page->user_is_editing()) {
                $output .= ' ' . course_get_cm_rename_action($mod, $sectionreturn);
            }*/

            // Module can put text after the link (e.g. forum unread)
            $output .= $mod->afterlink;

            // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
            $output .= html_writer::end_tag('div'); // .activityinstance
        }

        // If there is content but NO link (eg label), then display the
        // content here (BEFORE any icons). In this case cons must be
        // displayed after the content so that it makes more sense visually
        // and for accessibility reasons, e.g. if you have a one-line label
        // it should work similarly (at least in terms of ordering) to an
        // activity.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);
        $url = $mod->url;
        if (empty($url)) {
            $output .= $contentpart;
        }

        $modicons = '';
        if ($this->page->user_is_editing()) {
            $editactions = course_get_cm_edit_actions($mod, $mod->indent, $sectionreturn);
            $modicons .= ' '. $this->course_section_cm_edit_actions($editactions, $mod, $displayoptions);
            $modicons .= $mod->afterediticons;
        }

        $modicons .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);

        if (!empty($modicons)) {
            $output .= html_writer::span($modicons, 'actions');
        }

        // If there is content AND a link, then display the content here
        // (AFTER any icons). Otherwise it was displayed before
        if (!empty($url)) {
            $output .= $contentpart;
        }

        // show availability info (if module is not available)
        $output .= $this->course_section_cm_availability($mod, $displayoptions);

        $output .= html_writer::end_tag('div'); // $indentclasses

        // End of indentation div.
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }
    
    /**
     * Renders HTML to show course module availability information (for someone who isn't allowed
     * to see the activity itself, or for staff)
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_availability(cm_info $mod, $displayoptions = array()) {
        global $CFG;
        if (!$mod->uservisible) {
            // this is a student who is not allowed to see the module but might be allowed
            // to see availability info (i.e. "Available from ...")
            if (!empty($mod->availableinfo)) {
                $formattedinfo = \core_availability\info::format_info(
                        $mod->availableinfo, $mod->get_course());
                $output = html_writer::tag('div', $formattedinfo, array('class' => 'availabilityinfo'));
            }
            return $output;
        }
        // this is a teacher who is allowed to see module but still should see the
        // information that module is not available to all/some students
        $modcontext = context_module::instance($mod->id);
        $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $modcontext);
        if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Don't add availability information if user is not editing and activity is hidden.
            if ($mod->visible || $this->page->user_is_editing()) {
                $hidinfoclass = '';
                if (!$mod->visible) {
                    $hidinfoclass = 'hide';
                }
                $ci = new \core_availability\info_module($mod);
                $fullinfo = $ci->get_full_information();
                if ($fullinfo) {
                    $formattedinfo = \core_availability\info::format_info(
                            $fullinfo, $mod->get_course());
                    return html_writer::div($formattedinfo, 'availabilityinfo ' . $hidinfoclass);
                }
            }
        }
        // 26.08.2015 - If theres is an until option available then show it.
        if ($mod->visible && !empty($CFG->enableavailability)) {
            $ci = new \core_availability\info_module($mod);
            $availability = json_decode($mod->availability);
            if (isset($availability->c) && count($availability->c) > 1) {
                foreach ($availability->c as $key => $value) {
                    if ($value->type == 'date' && $value->d == '<') {
                        $time = strftime('%d %B %Y, %H:%M', $value->t);
                        $formattedinfo = get_string('availableuntil', 'theme_utessential').' <strong>'.$time.'</strong>';
                        return html_writer::div($formattedinfo, 'availabilityinfo ');
                        
                    }
                }
            }
        }
        return '';
    }
    
    /**
     * Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name(cm_info $mod, $displayoptions = array()) {
        global $CFG;
        $output = '';
        if (!$mod->uservisible && empty($mod->availableinfo)) {
            // nothing to be displayed to the user
            return $output;
        }
        $url = $mod->url;
        if (!$url) {
            return $output;
        }

        //Accessibility: for files get description via icon, this is very ugly hack!
        $instancename = $mod->get_formatted_name();
        $altname = $mod->modfullname;
        // Avoid unnecessary duplication: if e.g. a forum name already
        // includes the word forum (or Forum, etc) then it is unhelpful
        // to include that in the accessible description that is added.
        if (false !== strpos(core_text::strtolower($instancename),
                core_text::strtolower($altname))) {
            $altname = '';
        }
        // File type after name, for alphabetic lists (screen reader).
        if ($altname) {
            $altname = get_accesshide(' '.$altname);
        }

        // For items which are hidden but available to current user
        // ($mod->uservisible), we show those as dimmed only if the user has
        // viewhiddenactivities, so that teachers see 'items which might not
        // be available to some students' dimmed but students do not see 'item
        // which is actually available to current student' dimmed.
        $linkclasses = '';
        $accesstext = '';
        $textclasses = '';
        if ($mod->uservisible) {
            $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
            $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
                has_capability('moodle/course:viewhiddenactivities',
                        context_course::instance($mod->course));
            if ($accessiblebutdim) {
                $linkclasses .= ' dimmed';
                $textclasses .= ' dimmed_text';
                if ($conditionalhidden) {
                    $linkclasses .= ' conditionalhidden';
                    $textclasses .= ' conditionalhidden';
                }
                // Show accessibility note only if user can access the module himself.
                $accesstext = get_accesshide(get_string('hiddenfromstudents').':'. $mod->modfullname);
            }
        } else {
            $linkclasses .= ' dimmed';
            $textclasses .= ' dimmed_text';
        }

        // Get on-click attribute value if specified and decode the onclick - it
        // has already been encoded for display (puke).
        $onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);

        $groupinglabel = '';
        if (!empty($mod->groupingid) && has_capability('moodle/course:managegroups', context_course::instance($mod->course))) {
            $groupings = groups_get_all_groupings($mod->course);
            $groupinglabel = html_writer::tag('span', '('.format_string($groupings[$mod->groupingid]->name).')',
                    array('class' => 'groupinglabel '.$textclasses));
        }

        // Display link itself.
        $activitylink = html_writer::empty_tag('img', array('src' => $mod->get_icon_url(),
                'class' => 'icon activityicon', 'alt' => ' ', 'role' => 'presentation')) . $accesstext .
                html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));
        if ($mod->uservisible) {
            //17.06.2014 - Itemslist
            if ($mod->modname == 'itemslist') {
        // Display link itself.
                $activitylink = html_writer::empty_tag('img', array('src' => $mod->get_icon_url(),
                        'class' => 'icon activityicon', 'id' => 'image-itemlist-'.$mod->id, 'alt' => ' ', 'role' => 'presentation')) . $accesstext .
                        html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));
                $output .= html_writer::link('javascript:void(0);', $activitylink, array('id' => $mod->id, 'class' => 'itemslistaction '.$linkclasses, 'onclick' => $onclick)) .
                        $groupinglabel;
            } else {
                $output .= html_writer::link($url, $activitylink, array('class' => $linkclasses, 'onclick' => $onclick)) .
                        $groupinglabel;
            }
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->uservisible)
            $output .= html_writer::tag('div', $activitylink, array('class' => $textclasses)) .
                    $groupinglabel;
        }
        return $output;
    }
}
