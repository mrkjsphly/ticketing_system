<h2>Ticket Details</h2>

<a href="<?php echo site_url('tickets'); ?>">← Back to Tickets</a>

<br><br>

<table border="1" cellpadding="8">

    <tr>
        <th>Ticket Code</th>
        <td><?php echo $ticket->ticket_code; ?></td>
    </tr>

    <tr>
        <th>Client</th>
        <td><?php echo $ticket->client_name; ?></td>
    </tr>

    <tr>
        <th>Requester</th>
        <td><?php echo $ticket->requester_name; ?></td>
    </tr>

    <tr>
        <th>Contact Info</th>
        <td><?php echo $ticket->contact_info; ?></td>
    </tr>

    <tr>
        <th>Category</th>
        <td><?php echo $ticket->category; ?></td>
    </tr>

    <tr>
        <th>Priority</th>
        <td><?php echo $ticket->priority; ?></td>
    </tr>

    <tr>
        <th>Channel</th>
        <td><?php echo $ticket->channel; ?></td>
    </tr>

    <tr>
        <th>Subject</th>
        <td><?php echo $ticket->subject; ?></td>
    </tr>

    <tr>
        <th>Description</th>
        <td><?php echo $ticket->description; ?></td>
    </tr>

    <tr>
        <th>Status</th>
        <td><?php echo $ticket->ticket_status; ?></td>
    </tr>

    <tr>
        <th>Created At</th>
        <td><?php echo $ticket->created_at; ?></td>
    </tr>

</table>

<br>

<?php $role = $this->session->userdata('role'); ?>

<?php if ($role == 'TECH' || $role == 'ACCOUNTING' || $role == 'SUPERADMIN'): ?>

    <h3>Update Status</h3>

    <form method="post" action="<?php echo site_url('tickets/update_status/' . $ticket->id); ?>">

        <select name="ticket_status">

            <option value="New" <?php if ($ticket->ticket_status == 'New') echo 'selected'; ?>>New</option>

            <option value="In Progress" <?php if ($ticket->ticket_status == 'In Progress') echo 'selected'; ?>>In Progress</option>

            <option value="Resolved" <?php if ($ticket->ticket_status == 'Resolved') echo 'selected'; ?>>Resolved</option>

            <option value="For Closure" <?php if ($ticket->ticket_status == 'For Closure') echo 'selected'; ?>>For Closure</option>

            <option value="Closed" <?php if ($ticket->ticket_status == 'Closed') echo 'selected'; ?>>Closed</option>

        </select>

        <button type="submit">Update Status</button>

    </form>

<?php endif; ?>