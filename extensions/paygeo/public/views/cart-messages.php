<div id="paygeo_msgs" style="display: none;">
    <?php
    foreach ($messages as $message) {
        ?>
    <div class="woocommerce-info paygeo-unavailable"><?php echo wp_kses_post($message); ?></div>
        <?php
    }
    ?>
</div>