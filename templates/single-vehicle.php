<?php
/**
 * The template for displaying single vehicle posts
 *
 * This template can be overridden by copying it to yourtheme/single-vehicle.php
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div class="leasing-vehicle-single">
    <div class="leasing-vehicle-container">
        <div class="leasing-vehicle-content">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>
                    
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                    
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
    .leasing-vehicle-container {
        display: flex;
        flex-wrap: wrap;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .leasing-vehicle-content {
        flex: 1 0 100%;
    }
    
    .leasing-vehicle-image {
        flex: 1 0 100%;
        margin-bottom: 20px;
    }
    
    .leasing-vehicle-form {
        flex: 1 0 100%;
    }
    
    @media (min-width: 768px) {
        .leasing-vehicle-image {
            flex: 0 0 45%;
            margin-right: 5%;
            margin-bottom: 0;
        }
        
        .leasing-vehicle-form {
            flex: 0 0 50%;
        }
    }
    
    .leasing-vehicle-image img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 8px;
    }
    
    .no-image-placeholder {
        background-color: #f0f0f0;
        color: #888;
        text-align: center;
        padding: 100px 20px;
        border-radius: 8px;
    }
</style>

<?php get_footer(); ?> 