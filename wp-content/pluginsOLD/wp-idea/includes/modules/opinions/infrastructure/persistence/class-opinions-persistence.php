<?php

namespace bpmj\wpidea\modules\opinions\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Interface_Sql_Helper;
use bpmj\wpidea\infrastructure\database\Sort_By;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;

class Opinions_Persistence implements Interface_Opinions_Persistence
{
	private const QUERY_TYPE_SELECT = 'select';
	private const QUERY_TYPE_COUNT = 'select_count';

    public const TABLE_NAME = 'wpi_opinions';
	private const PRODUCTS_TABLE_NAME = 'posts';
	private const USERMETA_TABLE_NAME = 'usermeta';
	private const USER_TABLE_NAME = 'users';

	private Interface_Database $db;
	private Interface_Sql_Helper $sql_helper;

	public function __construct(
        Interface_Database $db,
	    Interface_Sql_Helper $sql_helper
    )
    {
        $this->db = $db;
	    $this->sql_helper = $sql_helper;
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(
            self::TABLE_NAME,
            [
                'id int UNSIGNED NOT NULL AUTO_INCREMENT',
                'product_id INT UNSIGNED NOT NULL',
                'user_id INT UNSIGNED NOT NULL',
                'opinion_content TEXT NOT NULL',
                'date_of_issue TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
                'status VARCHAR(50) NOT NULL',
                'rating TINYINT NOT NULL',
            ],
            'id',
        );
    }

    public function find_by_criteria(Opinions_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
	    return $this->select_opinions(self::QUERY_TYPE_SELECT, $criteria, $per_page, $page, $sort_by);
    }

	public function count_by_criteria(Opinions_Query_Criteria $criteria): int
	{
		$total_count_result = $this->select_opinions(self::QUERY_TYPE_COUNT, $criteria, 0, 0, null);

		return (int)($total_count_result[0]['total_count'] ?? 0);
	}

	public function select_opinions(string $query_type, Opinions_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
	{
		$select_start = $query_type === self::QUERY_TYPE_COUNT ? 'SELECT COUNT(*) AS total_count from (SELECT' : 'SELECT';
		$select_end = $query_type === self::QUERY_TYPE_COUNT ? ') count_table_alias' : '';


		$limit = ($per_page > 0) ? $per_page : PHP_INT_MAX;
		$skip = !$limit ? 0 : ($per_page * ($page - 1));
        $skip = ($skip < 0) ? 0 : $skip;
		$limit_sql = "LIMIT {$skip}, {$limit}";

		$having = $this->parse_criteria_to_having_clause($criteria);
		$having_sql = $this->sql_helper->process_having_condition_to_sql($having);

		$opinions_table_name = $this->db->prepare_table_name(self::TABLE_NAME);
		$products_table_name = $this->db->prepare_table_name(self::PRODUCTS_TABLE_NAME);
		$usermeta_table_name = $this->db->prepare_table_name(self::USERMETA_TABLE_NAME);
		$user_table_name = $this->db->prepare_table_name(self::USER_TABLE_NAME);

		$order_by_sql = $this->sql_helper->process_order_by_clause(
			$this->fix_column_names_in_sort_by_clause($sort_by)
		);

		return $this->db->execute(
			"${select_start}
                opinions.id,
                opinions.product_id,
                opinions.user_id,
                opinions.opinion_content,
                opinions.date_of_issue,
                opinions.status,
                opinions.rating,
                user.user_email as user_email,
                CONCAT_WS(' ', usermeta_for_first_name.meta_value, usermeta_for_last_name.meta_value) as user_full_name,
                product.post_title as product_name
            FROM ${opinions_table_name} opinions
INNER JOIN ${user_table_name} user ON opinions.user_id = user.id
INNER JOIN ${usermeta_table_name} usermeta_for_first_name ON opinions.user_id = usermeta_for_first_name.user_id AND usermeta_for_first_name.meta_key = 'first_name'
INNER JOIN ${usermeta_table_name} usermeta_for_last_name ON opinions.user_id = usermeta_for_last_name.user_id AND usermeta_for_last_name.meta_key = 'last_name'
INNER JOIN ${products_table_name} product ON opinions.product_id = product.id AND product.post_type = 'download'
GROUP BY opinions.id, 
	usermeta_for_last_name.meta_key,
	usermeta_for_last_name.meta_value,
	usermeta_for_first_name.meta_key,
	usermeta_for_first_name.meta_value
            {$having_sql}
            {$order_by_sql} {$limit_sql}
            {$select_end}
            "
		);
	}

	private function parse_criteria_to_having_clause(Opinions_Query_Criteria $criteria): array
	{
		$having = [];

		if ($criteria->get_opinion_id()){
			$having[] = ['opinions.id', '=', $criteria->get_opinion_id()];
		}

		if ( $criteria->get_user_full_name_like() ) {
			$having[] = [ 'user_full_name', 'LIKE', $criteria->get_user_full_name_like() ];
		}

		if ( $criteria->get_user_email_like() ) {
			$having[] = [ 'user_email', 'LIKE', $criteria->get_user_email_like() ];
		}

		if ( $criteria->get_user_id() ) {
			$having[] = [ 'opinions.user_id', '=', $criteria->get_user_id() ];
		}

		if ( $criteria->get_opinion_content_like() ) {
			$having[] = [ 'opinions.opinion_content', 'LIKE', $criteria->get_opinion_content_like() ];
		}

        if ( $criteria->get_statuses() ) {
			$having[] = [ 'opinions.status', 'IN', $criteria->get_statuses() ];
		}

		if ( $criteria->get_date_of_issue_from() ) {
			$having[] = [ 'opinions.date_of_issue', '>=', $criteria->get_date_of_issue_from() ];
		}

		if ( $criteria->get_date_of_issue_to() ) {
			$having[] = [ 'opinions.date_of_issue', '<=', $criteria->get_date_of_issue_to() ];
		}

		if ($criteria->get_product_id_in()) {
			$having[] = ['opinions.product_id', 'IN', $criteria->get_product_id_in()];
		}

		if ($criteria->get_opinion_rating_in()) {
			$having[] = ['opinions.rating', 'IN', $criteria->get_opinion_rating_in()];
		}

		return $having;
	}

	private function fix_column_names_in_sort_by_clause(?Sort_By_Clause $sort_by): ?Sort_By_Clause
	{
		if ( ! $sort_by ) {
			return null;
		}

		$processed_sort_by = new Sort_By_Clause();

		foreach ( $sort_by->get_all() as $item ) {
			/** @var Sort_By $item */
			$property = $item->property;

			switch ( $property ) {
				case 'product_name':
					$processed_sort_by->sort_by( 'product_name', $item->desc );
					break;

				case 'user_name':
					$processed_sort_by->sort_by( 'user_full_name', $item->desc );
					break;

				case 'user_email':
					$processed_sort_by->sort_by( 'user_email', $item->desc );
					break;

				case 'opinion_content':
					$processed_sort_by->sort_by( 'opinions.opinion_content', $item->desc );
					break;

				case 'date_of_issue':
					$processed_sort_by->sort_by( 'opinions.date_of_issue', $item->desc );
					break;

				case 'created_at': // handle default sorting provided by admin table module
					$processed_sort_by->sort_by( 'opinions.date_of_issue', $item->desc );
					break;

				case 'status':
					$processed_sort_by->sort_by( 'opinions.status', $item->desc );
					break;

				case 'opinion_rating':
					$processed_sort_by->sort_by( 'opinions.rating', $item->desc );
					break;
			}
		}

		return $processed_sort_by;
	}

    public function update(Opinion $opinion): void
    {
        $this->db->update_rows(self::TABLE_NAME, [
            [
                'status', $opinion->get_status()->get_value()
            ],
        ], [['id', '=', $opinion->get_id()->to_int()]]);
    }

    public function insert(Opinion $opinion): void
    {
        $this->db->insert(self::TABLE_NAME, [
            'product_id' => $opinion->get_product_id()->to_int(),
            'user_id' => $opinion->get_user_id()->to_int(),
            'opinion_content' => $opinion->get_opinion_content()->get_value(),
            'status' => $opinion->get_status()->get_value(),
            'rating' => $opinion->get_rating()->get_value(),
        ]);
    }
}