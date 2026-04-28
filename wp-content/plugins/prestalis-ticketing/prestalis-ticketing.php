<?php
/**
 * Plugin Name: Prestalis Ticketing
 * Description: Système de ticketing autonome pour Prestalis
 * Version: 1.0.0
 * Author: Eloise Robert
 */

if (!defined('ABSPATH')) exit; // Sécurité

// 1. Enregistrement des styles (avec time() pour forcer le chargement)
add_action('wp_enqueue_scripts', 'pt_enqueue_styles');
add_action('admin_enqueue_scripts', 'pt_enqueue_styles');

function pt_enqueue_styles() {
    wp_enqueue_style(
        'pt-style', 
        plugin_dir_url(__FILE__) . 'assets/style.css', 
        [], 
        time() 
    );
}

// 2. Création des tables lors de l'activation
register_activation_hook(__FILE__, 'pt_create_ticket_table');

function pt_create_ticket_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_tickets = $wpdb->prefix . 'prestalis_tickets';
    $sql_tickets = "CREATE TABLE $table_tickets (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        description text NOT NULL,
        status varchar(20) DEFAULT 'open' NOT NULL,
        client_email varchar(100) NOT NULL,
        access_token varchar(64) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql_tickets);

    $table_msgs = $wpdb->prefix . 'ticket_messages';
    $sql_msgs = "CREATE TABLE $table_msgs (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ticket_id mediumint(9) NOT NULL,
        sender varchar(20) NOT NULL,
        message text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql_msgs);
}

// 3. Admin Menu
add_action('admin_menu', 'pt_register_admin_menu');
function pt_register_admin_menu() {
    add_menu_page('Gestion des Tickets', 'Support Prestalis', 'manage_options', 'prestalis-ticketing', 'pt_render_dashboard', 'dashicons-tickets-alt', 20);
}

function pt_render_dashboard() {
    include plugin_dir_path(__FILE__) . 'includes/admin-template.php';
}

// 4. Gestion de la page client
add_action('template_redirect', 'pt_catch_ticket_request');
function pt_catch_ticket_request() {
    if (isset($_GET['token'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'prestalis_tickets';
        $token = sanitize_text_field($_GET['token']);
        $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE access_token = %s", $token));

        if ($ticket) {
            include plugin_dir_path(__FILE__) . 'includes/client-dashboard.php';
            exit; 
        }
    }
}

// 5. Fonction email (Gérée ici, au centre)
function pt_send_notification_email($ticket_id, $sender) {
    global $wpdb;
    $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "prestalis_tickets WHERE id = %d", $ticket_id));
    
    if (!$ticket) return;

    $admin_email = get_option('admin_email');
    $subject = "Mise à jour sur le ticket #" . $ticket_id;
    $message = ($sender == 'client') ? 
        "Le client a répondu : " . home_url('/?token=' . $ticket->access_token) : 
        "Le support a répondu : " . home_url('/?token=' . $ticket->access_token);
    
    $to = ($sender == 'client') ? $admin_email : $ticket->client_email;
    wp_mail($to, $subject, $message);
}