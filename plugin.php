<?php

namespace Change_Administration_Email;

if (!defined('ABSPATH')) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

class Plugin {

    /** @var string $plugin_file absolute path to the plugin file */
    protected string $plugin_file;

    /** @var array $cache Cached results of method calls */
    protected array $cache = [];

    /**
     * @param $file string absolute path to the plugin file
     */
    public function __construct( $file ) {
        $this->plugin_file = $file;
    }

    /**
     * Plugin data retrieved from get_plugin_data()
     * @return array Array of plugin data from WordPress
     */
    public function get_plugin_data():array {
        if ( !isset( $this->cache[ __METHOD__ ] ) ) {
            include_once ABSPATH . '/wp-admin/includes/plugin.php';
            $this->cache[ __METHOD__ ] = get_plugin_data( $this->plugin_file );
        }

        return $this->cache[ __METHOD__ ];
    }

    /**
     * Get the name of the plugin
     * @return string The name of the plugin
     */
    public function get_name() {
        return $this->cache[ __METHOD__ ] ??= match( true ) {
            array_key_exists( 'Name', $data = $this->get_plugin_data() ) => $data['Name'],
            default => null,
        };
    }

    /**
     * The relative path to the plugin file from WP_PLUGIN_DIR
     * @return string Relative path to plugin file from WP_PLUGIN_DIR
     */
    public function get_basename(): string {
        return $this->cache[ __METHOD__ ] ??= plugin_basename( $this->plugin_file );
    }
}
