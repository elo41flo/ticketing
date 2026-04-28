<?php
/**
 * Plugin Name: Prestalis Ticketing
 * Description: Système de ticketing autonome pour Prestalis
 * Version: 1.0.0
 * Author: Eloise Robert
 */

if (!defined('ABSPATH')) exit; // Sécurité : empêche l'accès direct

// 1. Création de la table lors de l'activation du plugin
register_activation_hook(__FILE__, 'pt_create_ticket_table');

function pt_create_ticket_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // 1. Table des tickets
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

    // 2. Table des messages (maintenant ici, tout est créé au même endroit !)
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

// 2. Ajout du menu dans l'interface admin
add_action('admin_menu', 'pt_register_admin_menu');

function pt_register_admin_menu() {
    add_menu_page(
        'Gestion des Tickets',    // Titre de la page
        'Support Prestalis',      // Titre du menu
        'manage_options',         // Capacité requise
        'prestalis-ticketing',    // Slug
        'pt_render_dashboard',    // Fonction d'affichage
        'dashicons-tickets-alt',  // Icône
        20
    );
}

// 3. Fonction d'affichage du dashboard
function pt_render_dashboard() {
    include plugin_dir_path(__FILE__) . 'includes/admin-template.php';
}

add_action('template_redirect', 'pt_catch_ticket_request');

function pt_catch_ticket_request() {
    if (isset($_GET['token'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'prestalis_tickets';
        $token = sanitize_text_field($_GET['token']);
        $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE access_token = %s", $token));

        if ($ticket) {
            // On charge le dashboard client au lieu d'afficher du texte
            include plugin_dir_path(__FILE__) . 'includes/client-dashboard.php';
            exit; 
        }
    }

    $sql_msgs = "CREATE TABLE " . $wpdb->prefix . "ticket_messages (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    ticket_id mediumint(9) NOT NULL,
    sender varchar(20) NOT NULL, -- 'client' ou 'admin'
    message text NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";

    dbDelta($sql_msgs);
}

function pt_send_notification_email($ticket_id, $sender) {
    global $wpdb;
    $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "prestalis_tickets WHERE id = %d", $ticket_id));
    
    if (!$ticket) return;

    $admin_email = get_option('admin_email');
    $subject = "Mise à jour sur le ticket #" . $ticket_id;
    $message = "";

    if ($sender == 'client') {
        $to = $admin_email;
        $message = "Le client a répondu à son ticket : " . $ticket->title . "\n\nVoir ici : " . home_url('/?token=' . $ticket->access_token);
    } else {
        $to = $ticket->client_email;
        $message = "Le support Prestalis a répondu à votre ticket : " . $ticket->title . "\n\nAccédez à votre suivi ici : " . home_url('/?token=' . $ticket->access_token);
    }

    wp_mail($to, $subject, $message);
}

pt_create_ticket_table();