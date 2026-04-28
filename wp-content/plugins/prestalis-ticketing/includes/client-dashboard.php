<?php
$current_ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : $ticket->id;

// 1. Traitement avec vérification du Nonce
if (isset($_POST['submit_message'])) {
    // Vérification de sécurité : si le nonce est invalide, on arrête tout
    check_admin_referer('pt_client_reply', 'pt_nonce_field');

    $inserted = $wpdb->insert($wpdb->prefix . 'ticket_messages', [
        'ticket_id' => $current_ticket_id,
        'sender'    => 'client',
        'message'   => sanitize_textarea_field($_POST['client_message'])
    ]);

    if ($inserted && function_exists('pt_send_notification_email')) {
        pt_send_notification_email($current_ticket_id, 'client');
        echo "<p style='color:green;'>Message envoyé !</p>";
    }
}
?>

<div class="pt-container">
    <h1>Mon suivi de ticket : <?php echo esc_html($ticket->title); ?></h1>
    
    <div class="pt-status">
        <strong>Statut actuel :</strong> <?php echo esc_html($ticket->status); ?>
    </div>

    <h3>Historique des échanges</h3>
    <?php
    $messages = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ticket_messages WHERE ticket_id = %d ORDER BY created_at ASC", $current_ticket_id));
    foreach ($messages as $msg) {
        $class = ($msg->sender == 'client') ? 'pt-msg-client' : 'pt-msg-admin';
        echo "<div class='pt-msg $class'><strong>" . ucfirst($msg->sender) . " :</strong> " . esc_html($msg->message) . "</div>";
    }
    ?>

    <form method="post" style="margin-top: 30px;">
        <?php wp_nonce_field('pt_client_reply', 'pt_nonce_field'); ?>
        
        <textarea name="client_message" required style="width: 100%; height: 100px;"></textarea>
        <input type="hidden" name="ticket_id" value="<?php echo $current_ticket_id; ?>">
        <input type="submit" name="submit_message" value="Envoyer" class="button">
    </form>
</div>