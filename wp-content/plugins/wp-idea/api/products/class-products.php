<?php

namespace bpmj\wpidea\api\products;

class Products
{
    protected $repository;

    public function __construct() {
        $this->repository = WPI()->repo_locator->getProductRepository();
    }
    /**
     * @return Products_List
     */
    public function all($per_page = null, $page = null, $order_by = null, $order = 'ASC')
    {
        $per_page   = (int)$per_page;
        $per_page   = is_integer($per_page) && $per_page > 0 ? $per_page : null;
        $page       = is_integer($page) && $page > 0 ? $page : 1;
        $skip 		= $page - 1 > 0 ? ($page - 1) * $per_page : 0;

        $products = $this->repository->findAll($per_page, $skip, $order_by, $order);

        return new Products_List($products);
    }

    public function get_all_products_and_count_by_user_id($id, $per_page = null, $page = null, $order_by = null, $order = 'ASC')
    {
        $user_accessible_products = [];

        foreach ($this->all(null, null, $order_by, $order) as $product) {
            if ($product->userHasAccess($id))
                $user_accessible_products[] = $product;
        }

        if(!$per_page || !$page){
            $products = new Products_List($user_accessible_products);
        } else {
            $offset = $per_page * ($page - 1);
            $length = $per_page;
            $products = new Products_List(array_slice($user_accessible_products, $offset, $length));
        }

        return ['products' => $products, 'count' => count($user_accessible_products)];
    }

    public function get_products_by_category_name(string $archive_name, ?int $user_id, ?int $per_page = null, ?int $page = null, ?string $order_by = null, ?string $order = 'ASC'): array
    {
        $archive_products = [];

        foreach ($this->all(null, null, $order_by, $order) as $product) {
    
            foreach($product->getCategories() as $category){
                if($category->getName() == $archive_name){
                    $archive_products[] = $product;
                }
             }
 
        }

        $archive_products_filtred = $archive_products;

        if($user_id){
            $archive_products_filtred = $this->get_products_by_user_id($user_id, $archive_products);
        }

        if(!$per_page || !$page){
            $products = new Products_List($archive_products_filtred);
        } else {
            $offset = $per_page * ($page - 1);
            $length = $per_page;
            $products = new Products_List(array_slice($archive_products_filtred, $offset, $length));
        }

        return ['products' => $products, 'count' => count($archive_products_filtred)];
    }

    public function get_products_by_tag_name(string $archive_name, ?int $user_id, ?int $per_page = null, ?int $page = null, ?string $order_by = null, ?string $order = 'ASC'): array
    {
        $archive_products = [];
    
        foreach ($this->all(null, null, $order_by, $order) as $product) {
            
            foreach($product->getTags() as $tag){
                if($tag->getName() == $archive_name){
                    $archive_products[] = $product;
                }
             }

        }

        $archive_products_filtred = $archive_products;

        if($user_id){
            $archive_products_filtred = $this->get_products_by_user_id($user_id, $archive_products);
        }

        if(!$per_page || !$page){
            $products = new Products_List($archive_products_filtred);
        } else {
            $offset = $per_page * ($page - 1);
            $length = $per_page;
            $products = new Products_List(array_slice($archive_products_filtred, $offset, $length));
        }

        return ['products' => $products, 'count' => count($archive_products_filtred)];
    }

    public function get_products_by_user_id(int $user_id, array $products): array
    {
        $user_accessible_products = [];

        foreach($products as $product){
            if($product->userHasAccess($user_id)){
                $user_accessible_products[] = $product;
            }
        }

        return $user_accessible_products;
    }

    public function count_all()
    {
        return $this->repository->countAll();
    }
}
