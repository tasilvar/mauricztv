<?php
/**
 *
 * The class responsible for users list page
 *
 */

// Exit if accessed directly
namespace bpmj\wpidea\admin;

use WP_Screen;

if (!defined('ABSPATH')) {
    exit;
}

class User_List
{

    const URL_PARAM = 'course_participants_of';

    /**
     * Course ID
     * @var int
     */
    private $course_id;

    /**
     *
     */
    function __construct()
    {
        $this->init();
    }

    /**
     *
     */
    public function init()
    {
        add_filter('admin_init', array($this, 'hook_admin_init'));
    }

    /**
     * Add additional hooks and filters if we are on a supported page
     *
     * @global string $pagenow
     */
    public function hook_admin_init()
    {
        global $pagenow;
        if ('users.php' !== $pagenow) {
            return;
        }
        if (empty($_GET[static::URL_PARAM]) && (empty($_GET['action']) || 'delete' !== $_GET['action'])) {
            /*
             * Inject our param and redirect if the param is in the referer
             * and the requested page IS NOT main users.php (without parameters)
             */
            if ('/wp-admin/users.php' !== $_SERVER['REQUEST_URI'] && 1 === preg_match('/' . preg_quote(static::URL_PARAM, '/') . '=(\d+)/', wp_get_referer(), $matches)) {
                wp_redirect(
                    add_query_arg(
                        static::URL_PARAM, $matches[1], remove_query_arg(array(
                            '_wp_http_referer',
                            '_wpnonce'
                        ), wp_unslash($_SERVER['REQUEST_URI']))
                    )
                );
            }

            return;
        }
        $this->course_id = !empty($_GET[static::URL_PARAM]) ? $_GET[static::URL_PARAM] : false;
        if (!$this->course_id) {
            return;
        }
        add_filter('users_list_table_query_args', array($this, 'filter_users_list_for_course'));
        add_filter('views_users', array($this, 'filter_users_views_for_course'));

        add_action('admin_print_scripts', array($this, 'output_scripts'), 100);
    }

    /**
     * Filter users list params
     *
     * @param array $args
     *
     * @return array
     */
    public function filter_users_list_for_course($args)
    {
        $user_ids = WPI()->courses->get_users_that_had_access_to_the_course($this->course_id);

        // This is to prevent passing an empty list
        $user_ids[] = 0;
        $args['include'] = $user_ids;

        return $args;
    }

    /**
     * @param array $views
     *
     * @return array
     */
    public function filter_users_views_for_course($views)
    {
        global $role;

        $roles = array_diff(array_keys($views), array('all'));
        if (empty($roles)) {
            return $views;
        }

        $wp_roles = wp_roles();
        $url = 'users.php';
        $course_role_stats = WPI()->courses->get_course_user_role_stats($this->course_id, $roles);
        $total_users = $course_role_stats['total_users'];
        $avail_roles =& $course_role_stats['avail_roles'];
        unset($course_role_stats);

        $class = empty($role) ? ' class="current"' : '';
        $role_links = array();
        $role_links['all'] = "<a href='$url'$class>" . sprintf(_nx('All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_users, 'users'), number_format_i18n($total_users)) . '</a>';
        foreach ($wp_roles->get_names() as $this_role => $name) {
            if (!isset($avail_roles[$this_role])) {
                continue;
            }

            $class = '';

            if ($this_role === $role) {
                $class = ' class="current"';
            }

            $name = translate_user_role($name);
            /* translators: User role name with count */
            $name = sprintf(__('%1$s <span class="count">(%2$s)</span>'), $name, number_format_i18n($avail_roles[$this_role]));
            $role_links[$this_role] = "<a href='" . esc_url(add_query_arg('role', $this_role, $url)) . "'$class>$name</a>";
        }

        if (!empty($avail_roles['none'])) {

            $class = '';

            if ('none' === $role) {
                $class = ' class="current"';
            }

            $name = __('No role');
            /* translators: User role name with count */
            $name = sprintf(__('%1$s <span class="count">(%2$s)</span>'), $name, number_format_i18n($avail_roles['none']));
            $role_links['none'] = "<a href='" . esc_url(add_query_arg('role', 'none', $url)) . "'$class>$name</a>";

        }

        return $role_links;
    }

    /**
     * Output scripts on supported "users.php" page
     */
    public function output_scripts()
    {
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                // Modify page title
                $('h1').html('<?php printf(esc_html__('Participants of "%s"', BPMJ_EDDCM_DOMAIN), get_the_title($this->course_id)); ?>');

                var $users_anchor = $('.subsubsub > .all > a');
                if ('users.php' === $users_anchor.attr('href')) {
                    $users_anchor.attr('href', 'users.php?<?php echo static::URL_PARAM; ?>=<?php echo $this->course_id ?>');
                }
            });
        </script>
        <?php
    }
}
