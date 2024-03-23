<?php

namespace Change_Administration_Email\Action;

use Change_Administration_Email\Plugin;

if (!defined('ABSPATH')) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

class Deactivate_Notice {

    /** @var Plugin $plugin Plugin data object */
    protected Plugin $plugin;

    /**
     * @param Plugin $plugin Plugin data object
     */
    public function __construct( Plugin $plugin ) {
        $this->plugin = $plugin;
    }

    /**
     * Output admin notice
     *
     * @return void
     */
    public function output_notice():void {

        $plugin_name = $this->plugin->get_name();
        $plugin_basename = $this->plugin->get_basename();

        // Based on https://core.trac.wordpress.org/browser/tags/6.4/src/wp-admin/includes/class-wp-plugins-list-table.php#L798
        if (current_user_can('manage_network_plugins')) {
            $deactivate_link_label = __( 'Network Deactivate', 'change-administration-email' );
            /* translators: %s: Plugin name. */
            $deactivate_link_description = __( 'Network Deactivate %s', 'change-administration-email' );
        // Based on https://core.trac.wordpress.org/browser/tags/6.4/src/wp-admin/includes/class-wp-plugins-list-table.php#L848
        } else if (current_user_can('deactivate_plugin', $plugin_basename)) {
            $deactivate_link_label = __( 'Deactivate', 'change-administration-email' );
            /* translators: %s: Plugin name. */
            $deactivate_link_description = __( 'Deactivate %s', 'change-administration-email' );
        } else {
            return;
        }

        $deactivate_link = sprintf(
            ' <a href="%s" aria-label="%s">%s</a>',
            esc_url( wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin_basename ), 'deactivate-plugin_' . $plugin_basename ) ),
            /* translators: %s: Plugin name. */
            esc_attr( $deactivate_link_description ),
            esc_attr( $deactivate_link_label )
        );

        $deactivate_message = sprintf(
            /* translators: %s: Plugin name. */
            __( 'You should deactivate then delete the "%s" plugin as soon as you are finished using it.', 'change-administration-email' ),
            $plugin_name
        );

        // @TODO: add fallback for WP < 6.4.0 that does not support wp_admin_notice
        wp_admin_notice(
            $deactivate_message . $deactivate_link,
            [
                'type' => 'error',
            ]
        );
    }
}
