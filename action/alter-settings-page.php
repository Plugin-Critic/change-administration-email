<?php

namespace PluginCritic_Change_Administration_Email\Action;

if (!defined('ABSPATH')) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

class Alter_Settings_Page {

    /** @var array $adminhash adminhash WordPress option */
    protected array $adminhash;

    /**
     * Get the adminhash option
     *
     * @return array The adminhash from WordPress options
     */
    protected function get_adminhash():array {
        return $this->adminhash ??= get_option( 'adminhash' );
    }

    /**
     * Check if we should display the admin email confirm link
     *
     * @return bool true if the page should be altered, false if not
     */
    protected function should_page_be_altered():bool {
        if (!current_user_can('manage_options')) {
            return false;
        }

        // only check for updates when on plugins or updates pages
        global $pagenow;
        if (!in_array($pagenow, ['options-general.php'])) {
            return false;
        }

        // Check that there is a new admin email awaiting confirmation
        // Based on https://core.trac.wordpress.org/browser/tags/6.4/src/wp-admin/options-general.php#L141
        $new_admin_email = get_option( 'new_admin_email' );
        if ( !$new_admin_email || get_option( 'admin_email' ) == $new_admin_email ) {
            return false;
        }

        $adminhash = $this->get_adminhash();
        if ( empty( $adminhash ) ) {
            return false;
        }

        return true;
    }

    /**
     * Output jQuery code to add a second notice to the General Settings page
     *
     * @return void
     */
    public function output_confirm_link_script():void {

        if ( false == $this->should_page_be_altered() ) {
            return;
        }

        $adminhash = $this->get_adminhash();

        // Based on https://core.trac.wordpress.org/browser/tags/6.4/src/wp-admin/includes/misc.php#L1523
        $confirm_url = esc_url( self_admin_url( 'options.php?adminhash=' . $adminhash['hash'] ) );

        $pending_admin_email_message = sprintf(
        /* translators: %s: New admin email. */
            __( 'Confirm pending change of the admin email to %s?', 'plugin-critic' ),
            '<code>' . esc_html( $adminhash['newemail'] ) . '</code>'
        );

        $pending_admin_email_message .= sprintf(
            ' <a id="plugincritic-confirm-admin-email" href="%1$s">%2$s</a>',
            $confirm_url,
            __( 'Confirm Immediately', 'plugin-critic' )
        );

        // @TODO: add fallback for WP < 6.4.0 that does not support wp_get_admin_notice
        $notice = wp_kses_post( wp_get_admin_notice( $pending_admin_email_message,
            [
                'type' => 'warning',
                'additional_classes' => array( 'inline', 'updated' )
            ]
        ) );
        ?>
        <script>
            jQuery().ready(function() {
                let confirm_link = jQuery('<?php echo $notice; ?>');
                jQuery("#new-admin-email-description").parent().append( confirm_link );
                jQuery('#plugincritic-confirm-admin-email').on( 'click', function () {
                    return confirm( '<?php echo __( 'Are you sure you want to immediately confirm the admin email "' . esc_attr( $adminhash['newemail'] ) . '"?' ); ?>' );
                });
            });
        </script>
        <?php
    }
}
