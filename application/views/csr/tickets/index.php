<h2 class="page-title">My Tickets</h2>

<button class="btn-primary" onclick="openCreateTicketModal()">Create Ticket</button>

<br><br>

<div class="table-responsive">
    <table class="admin-table tickets-table">

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

                        <td>
                            <span class="badge badge-<?= strtolower($ticket->priority) ?>">
                                <?= $ticket->priority ?>
                            </span>
                        </td>

                        <td>
                            <?php $statusClass = strtolower(str_replace(' ', '', $ticket->ticket_status)); ?>
                            <span class="badge badge-<?= $statusClass ?>">
                                <?= $ticket->ticket_status ?>
                            </span>
                        </td>

                        <td><?= date('M d, Y h:i A', strtotime($ticket->created_at)) ?></td>

                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-icon-view" title="View" onclick="openViewTicketModal(<?= $ticket->id ?>)">
                                    👁
                                </button>
                                <?php if (in_array($status, ['New', 'Endorsed'])) : ?>
                                    <button class="btn-icon btn-icon-endorse" title="Endorse" onclick="openEndorseModal(<?= $ticket->id ?>, <?= $ticket->assigned_team ?? 'null' ?>)">
                                        📤
                                    </button>
                                    <a href="<?= site_url('tickets/cancel/' . $ticket->id); ?>"
                                        class="btn-icon btn-icon-cancel"
                                        title="Cancel"
                                        onclick="return confirm('Are you sure you want to cancel this ticket?')">
                                        ✕
                                    </a>
                                <?php endif; ?>
                            </div>
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
</div>

<div class="pagination-wrapper">
    <?= $pagination ?>
</div>


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
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="POS">POS</option>
                        <option value="Platform">Platform</option>
                        <option value="Server">Server</option>
                        <option value="Billing">Billing</option>
                        <option value="Others">Others</option>
                    </select>
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


<div id="viewTicketModal" class="modal-overlay">
    <div class="modal-box ticket-modal">

        <div class="ticket-header">
            <div class="ticket-header-left">
                <span class="ticket-code-label">Ticket Code</span>
                <h3 id="view_ticket_code"></h3>
            </div>
            <div class="ticket-header-right">
                <span id="view_priority_badge" class="badge"></span>
                <span id="view_status_badge" class="badge"></span>
            </div>
        </div>

        <div class="ticket-details-grid">
            <div class="ticket-detail-item">
                <span class="detail-label">Client</span>
                <span class="detail-value" id="view_client"></span>
            </div>
            <div class="ticket-detail-item">
                <span class="detail-label">Requester</span>
                <span class="detail-value" id="view_requester"></span>
            </div>
            <div class="ticket-detail-item">
                <span class="detail-label">Category</span>
                <span class="detail-value" id="view_category"></span>
            </div>
            <div class="ticket-detail-item">
                <span class="detail-label">Created</span>
                <span class="detail-value" id="view_created"></span>
            </div>
        </div>

        <div class="ticket-description-box">
            <span class="detail-label">Description</span>
            <div id="view_description" class="description-content"></div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeViewTicketModal()">Close</button>
        </div>

    </div>
</div>

<div id="endorseModal" class="modal-overlay">
    <div class="modal-box" style="width: 400px;">
        <h3>Endorse Ticket</h3>
        <p style="font-size: 13px; color: #6c757d; margin-top: -10px;">
            Select the team to endorse this ticket to.
        </p>

        <input type="hidden" id="endorse_ticket_id">

        <div class="form-group">
            <label>Assign to Team</label>
            <select id="endorse_team_id">
                <?php foreach ($teams as $team): ?>
                    <option value="<?= $team->id ?>">
                        <?= $team->team_name ?> (<?= $team->role ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeEndorseModal()">Cancel</button>
            <button type="button" class="btn-primary" onclick="submitEndorse()">Endorse</button>
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
            modal.style.display = 'none';
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
                document.getElementById('view_description').innerText = data.description;

                const date = new Date(data.created_at);
                document.getElementById('view_created').innerText = date.toLocaleString('en-PH', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                const priorityBadge = document.getElementById('view_priority_badge');
                priorityBadge.innerText = data.priority;
                priorityBadge.className = 'badge badge-' + data.priority.toLowerCase();

                const statusBadge = document.getElementById('view_status_badge');
                statusBadge.innerText = data.ticket_status;
                statusBadge.className = 'badge badge-' + data.ticket_status.toLowerCase().replace(' ', '');

                document.getElementById('viewTicketModal').style.display = 'flex';
            });
    }

    function closeViewTicketModal() {
        document.getElementById('viewTicketModal').style.display = 'none';
    }

    function openEndorseModal(ticket_id, current_team_id) {
        document.getElementById('endorse_ticket_id').value = ticket_id;
        if (current_team_id) {
            document.getElementById('endorse_team_id').value = current_team_id;
        }
        document.getElementById('endorseModal').style.display = 'flex';
    }

    function closeEndorseModal() {
        document.getElementById('endorseModal').style.display = 'none';
    }

    function submitEndorse() {
        const ticket_id = document.getElementById('endorse_ticket_id').value;
        const team_id = document.getElementById('endorse_team_id').value;

        fetch("<?= site_url('tickets/endorse') ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `ticket_id=${ticket_id}&team_id=${team_id}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    closeEndorseModal();
                    alert('Ticket endorsed successfully!');
                    location.reload();
                } else {
                    alert('Something went wrong. Please try again.');
                }
            });
    }
</script>