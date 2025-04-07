<?php
/**
 * The template for displaying vehicle archives
 *
 * This template can be overridden by copying it to yourtheme/archive-vehicle.php
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div class="leasing-vehicles-archive">
    <div class="leasing-vehicles-container">
        <header class="page-header">
            <h1 class="page-title">
                <?php 
                if (is_tax('vehicle_category')) {
                    single_term_title('Category: ');
                } else {
                    _e('Available Vehicles for Lease', 'leasing-form');
                }
                ?>
            </h1>
            
            <?php
            // Display an optional term description
            if (is_tax('vehicle_category')) {
                $term_description = term_description();
                if (!empty($term_description)) {
                    echo '<div class="taxonomy-description">' . $term_description . '</div>';
                }
            }
            ?>
        </header>
        
        <div class="leasing-vehicles-list">
            <?php if (have_posts()) : ?>
                
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('leasing-vehicle-item'); ?>>
                        <div class="vehicle-thumbnail">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" class="no-image">
                                    <div class="no-image-placeholder"><?php _e('No Image', 'leasing-form'); ?></div>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="vehicle-details">
                            <h2 class="vehicle-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="vehicle-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <?php
                            // Get base price
                            $base_price = get_post_meta(get_the_ID(), '_base_price', true);
                            if (!empty($base_price)) :
                            ?>
                                <div class="vehicle-price">
                                    <?php printf(__('From $%s /month', 'leasing-form'), number_format($base_price, 2)); ?>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="view-details-btn">
                                <?php _e('View Details', 'leasing-form'); ?>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
                
                <?php the_posts_pagination(); ?>
                
            <?php else : ?>
                <div class="no-vehicles-found">
                    <p><?php _e('No vehicles found.', 'leasing-form'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .leasing-vehicles-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .page-header {
        margin-bottom: 30px;
        text-align: center;
    }
    
    .taxonomy-description {
        margin-top: 10px;
        color: #666;
        font-style: italic;
    }
    
    .leasing-vehicles-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    @media (min-width: 768px) {
        .leasing-vehicles-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 992px) {
        .leasing-vehicles-list {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    .leasing-vehicle-item {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        background: white;
    }
    
    .leasing-vehicle-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .vehicle-thumbnail {
        height: 200px;
        overflow: hidden;
    }
    
    .vehicle-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .leasing-vehicle-item:hover .vehicle-thumbnail img {
        transform: scale(1.05);
    }
    
    .no-image-placeholder {
        background-color: #f0f0f0;
        color: #888;
        text-align: center;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .vehicle-details {
        padding: 20px;
    }
    
    .vehicle-title {
        margin: 0 0 15px;
        font-size: 20px;
    }
    
    .vehicle-title a {
        color: #333;
        text-decoration: none;
    }
    
    .vehicle-excerpt {
        color: #666;
        margin-bottom: 15px;
        font-size: 14px;
    }
    
    .vehicle-price {
        font-weight: bold;
        font-size: 18px;
        color: #01c257;
        margin-bottom: 15px;
    }
    
    .view-details-btn {
        display: inline-block;
        background-color: #01c257;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }
    
    .view-details-btn:hover {
        background-color: #00a048;
        color: white;
    }
    
    .no-vehicles-found {
        text-align: center;
        padding: 50px 20px;
        background: #f9f9f9;
        border-radius: 8px;
        color: #666;
    }
    
    .navigation.pagination {
        margin-top: 30px;
        text-align: center;
    }
    
    .nav-links {
        display: inline-flex;
    }
    
    .page-numbers {
        padding: 8px 12px;
        margin: 0 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #333;
        text-decoration: none;
    }
    
    .page-numbers.current {
        background-color: #01c257;
        color: white;
        border-color: #01c257;
    }
</style>

<?php get_footer(); ?> 