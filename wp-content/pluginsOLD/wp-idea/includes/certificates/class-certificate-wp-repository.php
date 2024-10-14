<?php namespace bpmj\wpidea\certificates;

use bpmj\wpidea\data_types\certificate\Certificate_Number;
use bpmj\wpidea\certificates\exceptions\Certificate_Exception;
use bpmj\wpidea\learning\course\Course;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\user\User;
use bpmj\wpidea\user\User_ID;
use Exception;
use WP_Query;
use DateTime;

class Certificate_Wp_Repository implements Interface_Certificate_Repository
{
    private const POST_TYPE = 'certificates';
    private const USER_META_KEY = 'user_id';
    private const COURSE_META_KEY = 'course_id';
    private const CERTIFICATE_NUMBER_META_KEY = 'certificate_number';
    private int $last_query_count;

    /**
     * @throws Exception
     */
    public function find_by_criteria(
        Certificate_Query_Criteria $criteria,
        int $page = 1,
        int $per_page = -1
    ): Certificate_Collection {

        $args = $this->get_meta_query_certificates($page, $per_page);

        $this->apply_course_criteria($criteria, $args);
        $this->apply_name_criteria($criteria, $args);
        $this->apply_email_criteria($criteria, $args);
        $this->apply_certificate_number_criteria($criteria, $args);
        $this->apply_date_range_criteria($criteria, $args);
        $this->apply_sort_by($criteria, $args);

        $query      = new \Wp_Query($args);
        $collection = new Certificate_Collection();
        foreach ($query->posts as $post) {
            $certificate = $this->create_instance_from_wp_post($post);
            $collection->add($certificate);
        }

        $this->last_query_count = $query->found_posts;

        return $collection;
    }

    public function count_by_criteria(Certificate_Query_Criteria $criteria): int
    {
        $this->find_by_criteria($criteria, 1, 1);

        return $this->last_query_count;
    }

    private function get_meta_query_certificates(int $page, int $per_page): array
    {
        return [
            'meta_query' => [
                'relation' => 'AND'
            ],
            'post_type' => self::POST_TYPE,
            'posts_per_page' => $per_page,
            'offset' => ($page - 1) * $per_page
        ];
    }

    /**
     * @throws Exception
     */
    public function find_by_id(Certificate_ID $id): ?Certificate
    {
        $wp_post = get_post($id->to_int());

        return $this->create_instance_from_wp_post($wp_post);
    }

    public function delete(Certificate $certificate): void
    {
        wp_delete_post($certificate->get_id()->to_int(), true);
    }

    public function update_regenerated_date(Certificate $certificate, \DateTime $date_time): Certificate
    {
        update_post_meta(
            $certificate->get_id()->to_int(),
            'date_regenerate',
            date_format($date_time, 'Y-m-d H:i:s')
        );

        return $certificate;
    }


    /**
     * @throws Certificate_Exception
     * @throws Exception
     */
    public function create_certificate(Course $course, User $user, DateTime $date_generated = null, ?string $certificate_number = null): ?Certificate
    {
        if ( ! $this->user_dont_have_certificate_for_course($course, $user)) {
            return null;
        }
        $new_cert_data = [
            'post_author'  => 1,
            'post_content' => '',
            'post_title'   => $course->get_title(),
            'post_status'  => 'publish',
            'post_type'    => 'certificates',
        ];
        if ( ! is_null($date_generated)) {
            $new_cert_data['post_date'] = date_format($date_generated, 'Y-m-d');
        }
        $certificate_id = wp_insert_post($new_cert_data);

        $generated_certificate_number = $this->generate_certificate_number($course->get_id(), $certificate_number);

        update_post_meta($certificate_id, 'course_id', $course->get_id()->to_int());
        update_post_meta($certificate_id, 'user_id', $user->get_id()->to_int());
        update_post_meta($certificate_id, 'certificate_number', $generated_certificate_number);
        update_post_meta($certificate_id, 'date_regenerate', '');

        return $this->find_by_id(new Certificate_ID($certificate_id));
    }

    private function generate_certificate_number(Course_ID $course_id, ?string $certificate_number): ?string
    {
        $enable_certificate_numbering = get_post_meta($course_id->to_int(), 'enable_certificate_numbering', true);

        if(!isset($certificate_number) && $enable_certificate_numbering === 'on'){

            $certificate_numbering_pattern = get_post_meta($course_id->to_int(), 'certificate_numbering_pattern', true);
            $last_certificate_number = (int) get_post_meta($course_id->to_int(), 'certificate_count', true);

            $certificate_number = $last_certificate_number+1;

            update_post_meta($course_id->to_int(), 'certificate_count', $certificate_number);

            $two_digit_year = date('y');
            $four_digit_year = date('Y');

            $number = str_replace('X', $certificate_number, $certificate_numbering_pattern);
            $number = str_replace('YYYY', $four_digit_year, $number);
            $number = str_replace('YY', $two_digit_year, $number);

            return $number;
        }

        return $certificate_number;

    }


    public function update_certificate_pdf_content(Certificate $certificate, string $pdf_content): Certificate
    {
        update_post_meta($certificate->get_id()->to_int(), 'pdf_content', $pdf_content);

        return $certificate;
    }

    public function get_created_at(Certificate $certificate): DateTime
    {
        $certificate_date_created = get_the_date('Y-m-d', $certificate->get_id()->to_int());

        return DateTime::createFromFormat('Y-m-d', $certificate_date_created);
    }

    private function apply_course_criteria(Certificate_Query_Criteria $criteria, array &$args): void
    {
        if ($criteria->get_course_id()) {
            $args['meta_query']['course_clause'] = [
                'key'     => 'course_id',
                'value'   => $criteria->get_course_id()->to_int(),
                'compare' => 'EQUALS'
            ];
        } else {
            $args['meta_query']['course_clause'] = [
                'key' => 'course_id',
            ];
        }
    }

    private function apply_name_criteria(Certificate_Query_Criteria $criteria, array &$args): void
    {
        if ($criteria->get_name_query()) {
            $user_args['meta_query'] = [
                'relation' => 'OR',
            ];

            foreach (explode(' ', $criteria->get_name_query()) as $word) {
                $user_args['meta_query']['first_name_clause'] = [
                    'key'         => 'first_name',
                    'value'       => $word,
                    'operator'    => 'LIKE',
                    'compare'     => 'LIKE',
                    'compare_key' => 'LIKE'
                ];
                $user_args['meta_query']['last_name_clause']  = [
                    'key'         => 'last_name',
                    'value'       => $word,
                    'operator'    => 'LIKE',
                    'compare'     => 'LIKE',
                    'compare_key' => 'LIKE'
                ];
            }

            $students_ids = array_map(fn($u) => $u->ID, (new \WP_User_Query($user_args))->get_results());

            $args['meta_query']['name_clause'] = [
                'key'     => 'user_id',
                'value'   => $students_ids,
                'compare' => 'IN'
            ];
        }
    }

    private function apply_email_criteria(Certificate_Query_Criteria $criteria, array &$args): void
    {
        if ($criteria->get_email_query()) {
            $user_args['search']         = "*" . $criteria->get_email_query() . "*";
            $user_args['search_columns'] = ['user_email'];
            $students_ids                = array_map(fn($u) => $u->ID, (new \WP_User_Query($user_args))->get_results());

            $args['meta_query']['email_clause'] = [
                'key'     => 'user_id',
                'value'   => $students_ids,
                'compare' => 'IN'
            ];
        }
    }

    private function apply_certificate_number_criteria(Certificate_Query_Criteria $criteria, array &$args): void
    {
        if ($criteria->get_certificate_number_query()) {
            $args['meta_query']['certificate_number_clause'] = [
                'key' => 'certificate_number',
                'value' => $criteria->get_certificate_number_query(),
                'compare' => 'LIKE'
            ];
        }
    }

    private function apply_date_range_criteria(Certificate_Query_Criteria $criteria, array &$args): void
    {
        if ($criteria->get_dated_from()) {
            $args['date_query']['after'] = $criteria->get_dated_from()->subDay()->format('Y-m-d');
        }

        if ($criteria->get_dated_to()) {
            $args['date_query']['before'] = $criteria->get_dated_to()->addDay()->format('Y-m-d');
        }
    }

    private function apply_sort_by(Certificate_Query_Criteria $criteria, array &$args)
    {
        if ( ! $criteria->get_sort_by_column()) {
            return;
        }

        $direction = $criteria->get_sort_direction_ascending() ? "ASC" : "DESC";

        switch ($criteria->get_sort_by_column()) {
            case 'id':
                $args['orderby']['ID'] = $direction;
                break;
            case 'created':
                $args['orderby']['post_created'] = $direction;
                break;
            case 'course':
                $args['orderby']['course_clause'] = $direction;
                break;
            default:
                break;
        }
    }

    private function get_meta(Certificate_ID $id, string $key): string
    {
        return get_post_meta($id->to_int(), $key, true);
    }

    private function get_user_id(Certificate_ID $id): User_ID
    {
        $id = $this->get_meta($id, self::USER_META_KEY);

        return new User_ID($id);
    }

    private function get_course_id(Certificate_ID $id): int
    {
        return (int)$this->get_meta($id, self::COURSE_META_KEY);
    }

    private function get_certificate_number(Certificate_ID $id): ?Certificate_Number
    {
        $certificate_number = $this->get_meta($id, self::CERTIFICATE_NUMBER_META_KEY);

        return $certificate_number ? new Certificate_Number($certificate_number) : null;
    }

    /**
     * @throws Exception
     */
    private function create_instance_from_wp_post(\WP_Post $post): Certificate
    {
        $cert_id = new Certificate_ID($post->ID);
        $user_id = $this->get_user_id($cert_id);

        return new Certificate(
            $cert_id,
            $user_id,
            new Course_ID($this->get_course_id($cert_id)),
            new \DateTime($post->post_date),
            $this->get_certificate_number($cert_id)
        );
    }

    private function user_dont_have_certificate_for_course(Course $course, User $user): bool
    {
        $query = new WP_Query(array(
            'post_type'  => self::POST_TYPE,
            'meta_query' => array(
                array(
                    'key'   => 'course_id',
                    'value' => $course->get_id()->to_int(),
                ),
                array(
                    'key'   => 'user_id',
                    'value' => $user->get_id()->to_int(),
                ),
            ),
        ));

        return $query->post_count == 0;
    }

}