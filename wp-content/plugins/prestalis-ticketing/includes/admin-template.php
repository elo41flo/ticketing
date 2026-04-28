<?php
global $wpdb;
$table_tickets = $wpdb->prefix . 'prestalis_tickets';
$table_msgs = $wpdb->prefix . 'ticket_messages';

// --- LOGIQUE D'INSERTION (Nouveau ticket) ---
if (isset($_POST['pt_submit'])) {
    $wpdb->insert($table_tickets, [
        'title' => sanitize_text_field($_POST['pt_title']),
        'description' => sanitize_textarea_field($_POST['pt_desc']),
        'client_email' => sanitize_email($_POST['pt_email']),
        'access_token' => bin2hex(random_bytes(16)),
        'status' => 'open'
    ]);
}

// --- LOGIQUE DE RÉPONSE ADMIN ---
if (isset($_POST['admin_reply'])) {
    $wpdb->insert($table_msgs, [
        'ticket_id' => intval($_POST['ticket_id']),
        'sender'    => 'admin',
        'message'   => sanitize_textarea_field($_POST['admin_message'])
    ]);
}

// --- AFFICHAGE ---
echo '<div class="wrap">';

// VUE DÉTAILLÉE (Gestion d'un ticket)
if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
    $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_tickets WHERE id = %d", intval($_GET['id'])));
    $messages = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_msgs WHERE ticket_id = %d ORDER BY created_at ASC", $ticket->id));
    ?>
    <a href="?page=prestalis-ticketing">← Retour à la liste</a>
    <h1>Ticket #<?php echo $ticket->id; ?> : <?php echo esc_html($ticket->title); ?></h1>
    
    <div style="background: #fff; padding: 20px; border: 1px solid #ccc;">
        <h3>Historique :</h3>
        <?php foreach ($messages as $msg) : ?>
            <div style="margin-bottom: 10px; padding: 10px; background: <?php echo ($msg->sender == 'admin' ? '#d4edda' : '#e2e3e5'); ?>">
                <strong><?php echo ucfirst($msg->sender); ?> :</strong> <?php echo esc_html($msg->message); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post" style="margin-top:20px;">
        <textarea name="admin_message" required style="width:100%; height:80px;" placeholder="Répondre au client..."></textarea>
        <input type="hidden" name="ticket_id" value="<?php echo $ticket->id; ?>">
        <input type="submit" name="admin_reply" value="Envoyer la réponse" class="button button-primary">
    </form>
    <?php
} 
// VUE LISTE (Tableau de bord)
else {
    $tickets = $wpdb->get_results("SELECT * FROM $table_tickets ORDER BY created_at DESC");
    ?>
    <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; margin-bottom: 20px;">
        <h3>Ajouter un ticket de test</h3>
        <form method="post">
            <input type="text" name="pt_title" placeholder="Sujet" required>
            <input type="email" name="pt_email" placeholder="Email" required>
            <textarea name="pt_desc" placeholder="Description"></textarea>
            <input type="submit" name="pt_submit" value="Créer" class="button button-primary">
        </form>
    </div>

    <h1>Support Prestalis - Liste des Tickets</h1>
    <table class="wp-list-table widefat striped">
        <thead><tr><th>ID</th><th>Sujet</th><th>Statut</th><th>Lien Client</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($tickets as $t) : ?>
            <tr>
                <td><?php echo $t->id; ?></td>
                <td><?php echo esc_html($t->title); ?></td>
                <td><?php echo esc_html($t->status); ?></td>
                <td><a href="<?php echo esc_url(home_url('/?token=' . $t->access_token)); ?>" target="_blank">Suivi Client</a></td>
                <td><a href="?page=prestalis-ticketing&action=view&id=<?php echo $t->id; ?>" class="button">Gérer</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}
echo '</div>';
?>