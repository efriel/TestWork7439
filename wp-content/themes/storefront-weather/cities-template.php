<?php
/*
Template Name: Cities Table
*/
get_header();
?>

<div id="before-table">
    <?php do_action('before_cities_table'); ?>
</div>

<div>
    <input type="text" id="city-search" placeholder="Search Cities">
    <button id="search-button">Search</button>
</div>

<div id="cities-table">
    <!-- AJAX -->
</div>

<div id="after-table">
    <?php do_action('after_cities_table'); ?>
</div>

<?php get_footer(); ?>
