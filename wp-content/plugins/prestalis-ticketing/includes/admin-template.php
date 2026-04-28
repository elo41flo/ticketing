<?php
global $wpdb;
$table_tickets = $wpdb->prefix . 'prestalis_tickets';
$table_msgs = $wpdb->prefix . 'ticket_messages';

// --- LOGIQUE D'INSERTION ET TRAITEMENT (Avec Nonce) ---
if (isset($_POST['admin_reply'])) {
    check_admin_referer('pt_admin_reply', 'pt_nonce_admin');
    $wpdb->insert($table_msgs, [
        'ticket_id' => intval($_POST['ticket_id']),
        'sender'    => 'admin',
        'message'   => sanitize_textarea_field($_POST['admin_message'])
    ]);
    if (function_exists('pt_send_notification_email')) {
        pt_send_notification_email(intval($_POST['ticket_id']), 'admin');
    }
}

if (isset($_POST['update_status'])) {
    check_admin_referer('pt_status_update', 'pt_nonce_status');
    $wpdb->update($table_tickets, 
        ['status' => sanitize_text_field($_POST['new_status'])], 
        ['id' => intval($_POST['ticket_id'])]
    );
    echo "<div class='updated'><p>Statut mis à jour !</p></div>";
}
?>

<div class="wrap pt-wrapper">
    <?php if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) : 
        $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_tickets WHERE id = %d", intval($_GET['id'])));
        $messages = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_msgs WHERE ticket_id = %d ORDER BY created_at ASC", $ticket->id));
    ?>
        <a href="?page=prestalis-ticketing">← Retour à la liste</a>
        <h1>Ticket #<?php echo $ticket->id; ?> : <?php echo esc_html($ticket->title); ?></h1>
        
        <div class="pt-status">
            <strong>Statut actuel : <?php echo esc_html($ticket->status); ?></strong>
            <form method="post" style="display:inline-block; margin-left:10px;">
                <?php wp_nonce_field('pt_status_update', 'pt_nonce_status'); ?>
                <select name="new_status">
                    <option value="open">Ouvert</option>
                    <option value="pending">En attente</option>
                    <option value="resolved">Résolu</option>
                </select>
                <input type="hidden" name="ticket_id" value="<?php echo $ticket->id; ?>">
                <input type="submit" name="update_status" value="Mettre à jour" class="button">
            </form>
        </div>

        <div class="pt-container">
            <h3>Historique :</h3>
            <?php foreach ($messages as $msg) : 
                $class = ($msg->sender == 'admin' ? 'pt-msg-admin' : 'pt-msg-client'); ?>
                <div class="pt-message <?php echo $class; ?>">
                    <strong><?php echo ucfirst($msg->sender); ?> :</strong> <?php echo esc_html($msg->message); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="post" style="margin-top:20px;">
            <?php wp_nonce_field('pt_admin_reply', 'pt_nonce_admin'); ?>
            <textarea name="admin_message" required style="width:100%; height:80px;" placeholder="Répondre au client..."></textarea>
            <input type="hidden" name="ticket_id" value="<?php echo $ticket->id; ?>">
            <input type="submit" name="admin_reply" value="Envoyer la réponse" class="button button-primary">
        </form>
    <?php else : 
        $tickets = $wpdb->get_results("SELECT * FROM $table_tickets ORDER BY created_at DESC");
    ?>
        <h1>Support Prestalis - Liste des Tickets</h1>
        <table class="wp-list-table widefat striped">
            <thead><tr><th>ID</th><th>Sujet</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($tickets as $t) : ?>
                <tr>
                    <td><?php echo $t->id; ?></td>
                    <td><?php echo esc_html($t->title); ?></td>
                    <td><?php echo esc_html($t->status); ?></td>
                    <td><a href="?page=prestalis-ticketing&action=view&id=<?php echo $t->id; ?>" class="button">Gérer</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>