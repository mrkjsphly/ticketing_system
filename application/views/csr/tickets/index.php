<?php $this->load->view('csr/layout/header'); ?>
<?php $this->load->view('csr/layout/sidebar'); ?>

<h2>My Tickets</h2>

<button class="btn-primary" onclick="openCreateTicketModal()">Create Ticket</button>

<br><br>

<table class="admin-table">

    <thead>
        <tr>
            <th>Ticket Code</th>
            <th>Client</th>
            <th>Requester</th>
            <th>Category</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Created</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

        <?php if (!empty($tickets)) : ?>

            <?php foreach ($tickets as $ticket) : ?>

                <tr>

                    <td><?= $ticket->ticket_code ?></td>

                    <td><?= $ticket->client_name ?></td>

                    <td><?= $ticket->requester_name ?></td>

                    <td><?= $ticket->category ?></td>

                    <td><?= $ticket->priority ?></td>

                    <td><?= $ticket->ticket_status ?></td>

                    <td><?= date('M d, Y H:i', strtotime($ticket->created_at)) ?></td>

                    <td>
                        <a href="<?php echo site_url('tickets/view/' . $ticket->id); ?>" class="btn-view">
                            View
                        </a>

                        <?php if ($ticket->ticket_status != 'Cancelled' && $ticket->ticket_status != 'Resolved') : ?>
                            <a href="<?php echo site_url('tickets/cancel/' . $ticket->id); ?>"
                                class="btn-cancel"
                                onclick="return confirm('Are you sure you want to cancel this ticket?')">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else : ?>

            <tr>
                <td colspan="8">No tickets found</td>
            </tr>

        <?php endif; ?>

    </tbody>

</table>



<!-- CREATE TICKET MODAL -->

<div id="createTicketModal" class="modal-overlay">
    <div class="modal-box ticket-modal">

        <h3>Create Ticket</h3>

        <form method="post" action="<?= site_url('tickets/store') ?>">

            <div class="ticket-grid">

                <div class="form-group">
                    <label>Requester</label>
                    <input type="text" name="requester_name" required>
                </div>

                <div class="form-group">
                    <label>Contact Info</label>
                    <input type="text" name="contact_info">
                </div>

                <div class="form-group">
                    <label>Client</label>

                    <select name="client_id" required>

                        <option value="">Select Client</option>

                        <?php foreach ($clients as $client): ?>

                            <option value="<?= $client->id ?>">
                                <?= $client->client_name ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category">
                </div>

                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority">
                        <option>Low</option>
                        <option>Medium</option>
                        <option>High</option>
                        <option>Critical</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="ticket_type">
                        <option>Issue</option>
                        <option>Request</option>
                        <option>Account Creation</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Channel</label>
                    <select name="channel">
                        <option>Viber</option>
                        <option>Messenger</option>
                        <option>Email</option>
                        <option>Call</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject">
                </div>

            </div>

            <div class="form-group description-group">
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeCreateTicketModal()">Cancel</button>
                <button type="submit" class="btn-primary">Create Ticket</button>
            </div>

        </form>

    </div>
</div>



<script>
    function openCreateTicketModal() {
        document.getElementById('createTicketModal').style.display = 'flex';
    }

    function closeCreateTicketModal() {
        document.getElementById('createTicketModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('createTicketModal');

        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php $this->load->view('csr/layout/footer'); ?>