<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

?><div class="notice notice-info woopcd-partialcod-notice is-dismissible" data-woopcd_partialcod_id="lite">
    <p><?php echo wp_kses( __( 'Hey, I noticed you having been using <strong>PCOD – Partial COD, Payment Gateway Restrictions & Fees | for WooCommerce</strong>, that’s awesome! Could you please do me a favor and give it a 5-star rating to help us spread the word?', 'partial-cod-payment-gateway-restrictions-fees' ), array( 'strong' => array() ) ); ?></p>
    <p>
        <a href="https://wordpress.org/support/plugin/partial-cod-payment-gateway-restrictions-fees/reviews/#new-post" class="woopcd-partialcod-btn" target="_blank"><span class="dashicons dashicons-external"></span><?php echo esc_html__( 'Yes! You deserve it', 'partial-cod-payment-gateway-restrictions-fees' ); ?></a>
        <a href="#" class="woopcd-partialcod-btn woopcd-partialcod-btn-secondary" data-woopcd_partialcod_remind="yes"><span class="dashicons dashicons-calendar"></span><?php echo esc_html__( 'Nah, maybe later', 'partial-cod-payment-gateway-restrictions-fees' ); ?></a>
        <a href="#" class="woopcd-partialcod-btn woopcd-partialcod-btn-secondary"><span class="dashicons dashicons-smiley"></span><?php echo esc_html__( 'I already did', 'partial-cod-payment-gateway-restrictions-fees' ); ?></a>
    </p>
</div>