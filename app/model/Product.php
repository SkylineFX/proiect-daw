<?php

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all products
    public function getAllProducts() {
        $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name 
                FROM products p 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id
                LEFT JOIN categories c ON s.category_id = c.id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get paginated products
    public function getProductsPaginated($limit = 12, $offset = 0) {
        $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name 
                FROM products p 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id
                LEFT JOIN categories c ON s.category_id = c.id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        // PDO params for limit/offset need to be integers
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get paginated products by category
    public function getProductsByCategoryPaginated($categoryId, $limit = 12, $offset = 0) {
        $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name 
                FROM products p 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id
                LEFT JOIN categories c ON s.category_id = c.id
                WHERE c.id = :category_id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':category_id', $categoryId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get paginated products by subcategory
    public function getProductsBySubcategoryPaginated($subcategoryId, $limit = 12, $offset = 0) {
        $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name 
                FROM products p 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id
                LEFT JOIN categories c ON s.category_id = c.id
                WHERE s.id = :subcategory_id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':subcategory_id', $subcategoryId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get total count of products
    public function getTotalProductCount() {
        $sql = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    // Get total count of products by category
    public function getTotalProductCountByCategory($categoryId) {
        $sql = "SELECT COUNT(*) as total 
                FROM products p 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id
                WHERE s.category_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        $row = $stmt->fetch();
        return $row['total'];
    }

    // Get total count of products by subcategory
    public function getTotalProductCountBySubcategory($subcategoryId) {
        $sql = "SELECT COUNT(*) as total FROM products WHERE subcategory_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$subcategoryId]);
        $row = $stmt->fetch();
        return $row['total'];
    }

    // Get single product
    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Create product
    public function createProduct($data) {
        $sql = "INSERT INTO products (name, description, price, stock, subcategory_id, image_url) 
                VALUES (:name, :description, :price, :stock, :subcategory_id, :image_url)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':subcategory_id' => $data['subcategory_id'],
            ':image_url' => $data['image_url'] ?? null
        ]);
    }

    // Update product
    public function updateProduct($id, $data) {
        $sql = "UPDATE products SET 
                name = :name, 
                description = :description, 
                price = :price, 
                stock = :stock, 
                subcategory_id = :subcategory_id, 
                image_url = :image_url 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':stock' => $data['stock'],
            ':subcategory_id' => $data['subcategory_id'],
            ':image_url' => $data['image_url']
        ];

        return $stmt->execute($params);
    }

    // Delete product
    public function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Get all categories with subcategories
    public function getCategoriesWithSubcategories() {
        $sql = "SELECT c.id as cat_id, c.name as cat_name, s.id as sub_id, s.name as sub_name 
                FROM categories c 
                LEFT JOIN subcategories s ON c.id = s.category_id 
                ORDER BY c.name, s.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $categories = [];
        foreach ($results as $row) {
            $catId = $row['cat_id'];
            if (!isset($categories[$catId])) {
                $categories[$catId] = [
                    'id' => $catId,
                    'name' => $row['cat_name'],
                    'subcategories' => []
                ];
            }
            if ($row['sub_id']) {
                $categories[$catId]['subcategories'][] = [
                    'id' => $row['sub_id'],
                    'name' => $row['sub_name']
                ];
            }
        }
        return $categories;
    }
}
