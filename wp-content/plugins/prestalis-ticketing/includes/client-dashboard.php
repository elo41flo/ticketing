<?php
// On s'assure d'avoir l'ID du ticket, même après un POST
$current_ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : $ticket->id;

// 1. Traitement du formulaire (Placé AVANT tout affichage)
if (isset($_POST['submit_message'])) {
    $inserted = $wpdb->insert($wpdb->prefix . 'ticket_messages', [
        'ticket_id' => $current_ticket_id,
        'sender'    => 'client',
        'message'   => sanitize_textarea_field($_POST['client_message'])
    ]);

    if ($inserted) {
        // Appeler la fonction email ici
        if (function_exists('pt_send_notification_email')) {
            pt_send_notification_email($current_ticket_id, 'client');
        }
        echo "<div style='color:green; padding:10px;'>Message envoyé avec succès !</div>";
    } else {
        echo "<div style='color:red;'>Erreur : " . $wpdb->last_error . "</div>";
    }
}

function pt_send_notification_email($ticket_id, $sender) {
    global $wpdb;
    $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "prestalis_tickets WHERE id = %d", $ticket_id));
    
    if (!$ticket) return;

    $subject = "Notification Ticket #" . $ticket_id;
    $message = "Test d'envoi pour le ticket " . $ticket->title;
    $to = 'test@example.com'; 

    // --- LE TEST ---
    $result = wp_mail($to, $subject, $message);

    // On écrit dans le fichier debug.log de WordPress
    if ($result) {
        error_log("EMAIL PRESTALIS : Email envoyé avec succès pour le ticket $ticket_id");
    } else {
        error_log("EMAIL PRESTALIS : Échec de l'envoi pour le ticket $ticket_id");
    }
}

?>

<div class="client-container" style="max-width: 800px; margin: 40px auto; padding: 20px; border: 1px solid #ddd;">
    <h1>Mon suivi de ticket : <?php echo esc_html($ticket->title); ?></h1>
    
    <div class="status-box" style="background: #f4f4f4; padding: 10px; margin: 20px 0;">
        <strong>Statut actuel :</strong> <?php echo esc_html($ticket->status); ?>
    </div>

    <h3>Historique des échanges</h3>
    <?php
    $messages = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ticket_messages WHERE ticket_id = %d ORDER BY created_at ASC", $current_ticket_id));
    
    foreach ($messages as $msg) {
        $bg = ($msg->sender == 'client') ? '#eef' : '#fee';
        echo "<div style='background: $bg; padding: 10px; margin: 5px 0;'>
                <strong>" . ucfirst($msg->sender) . " :</strong> " . esc_html($msg->message) . "
              </div>";
    }
    ?>

    <form method="post" style="margin-top: 30px;">
        <textarea name="client_message" required placeholder="Votre message..." style="width: 100%; height: 100px;"></textarea>
        <input type="hidden" name="ticket_id" value="<?php echo $current_ticket_id; ?>">
        <input type="submit" name="submit_message" value="Envoyer" class="button button-primary">
    </form>
</div>