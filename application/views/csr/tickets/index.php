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

                <?php $status = $ticket->ticket_status; ?>

                <tr class="<?= $ticket->ticket_status == 'Cancelled' ? 'ticket-cancelled' : '' ?>">

                    <td><?= $ticket->ticket_code ?></td>

                    <td><?= $ticket->client_name ?></td>

                    <td><?= $ticket->requester_name ?></td>

                    <td><?= $ticket->category ?></td>

                    <td><?= $ticket->priority ?></td>

                    <td>

                        <?php if ($status == 'Cancelled'): ?>
                            <span class="badge badge-danger">Cancelled</span>

                        <?php elseif ($status == 'New'): ?>
                            <span class="badge badge-primary">New</span>

                        <?php elseif ($status == 'In Progress'): ?>
                            <span class="badge badge-warning">In Progress</span>

                        <?php elseif ($status == 'Resolved'): ?>
                            <span class="badge badge-success">Resolved</span>

                        <?php else: ?>
                            <span class="badge badge-secondary"><?= $status ?></span>
                        <?php endif; ?>

                    </td>

                    <td><?= date('M d, Y H:i', strtotime($ticket->created_at)) ?></td>

                    <td>
                        <button class="btn-view" onclick="openViewTicketModal(<?= $ticket->id ?>)">
                            View
                        </button>
                        <?php if ($status != 'Cancelled' && $status != 'Resolved') : ?>
                            <a href="<?= site_url('tickets/cancel/' . $ticket->id); ?>"
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



<!-- VIEW TICKET MODAL -->

<div id="viewTicketModal" class="modal-overlay">
    <div class="modal-box ticket-modal">

        <h3>Ticket Details</h3>

        <div id="ticketDetails">

            <p><strong>Ticket Code:</strong> <span id="view_ticket_code"></span></p>
            <p><strong>Client:</strong> <span id="view_client"></span></p>
            <p><strong>Requester:</strong> <span id="view_requester"></span></p>
            <p><strong>Category:</strong> <span id="view_category"></span></p>
            <p><strong>Priority:</strong> <span id="view_priority"></span></p>
            <p><strong>Status:</strong> <span id="view_status"></span></p>
            <p><strong>Created:</strong> <span id="view_created"></span></p>

            <p><strong>Description:</strong></p>
            <div id="view_description"></div>

        </div>

        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeViewTicketModal()">Close</button>
        </div>

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

    function openViewTicketModal(ticket_id) {

        fetch("<?= site_url('tickets/get_ticket/') ?>" + ticket_id)
            .then(response => response.json())
            .then(data => {

                document.getElementById('view_ticket_code').innerText = data.ticket_code;
                document.getElementById('view_client').innerText = data.client_name;
                document.getElementById('view_requester').innerText = data.requester_name;
                document.getElementById('view_category').innerText = data.category;
                document.getElementById('view_priority').innerText = data.priority;
                document.getElementById('view_status').innerText = data.ticket_status;
                document.getElementById('view_created').innerText = data.created_at;
                document.getElementById('view_description').innerText = data.description;

                document.getElementById('viewTicketModal').style.display = 'flex';

            });
    }

    function closeViewTicketModal() {
        document.getElementById('viewTicketModal').style.display = 'none';
    }
</script>

<?php $this->load->view('csr/layout/footer'); ?>