<div id="partialcod_msgs" style="display: none;">
    <?php
    
    $allowed_html = WOOPCD_PartialCOD_Main::get_allow_html();
    
    foreach ($messages as $message) {
        ?>
    <div class="woocommerce-info partialcod-unavailable"><?php echo wp_kses($message, $allowed_html); ?></div>
        <?php
    }
    ?>
</div>