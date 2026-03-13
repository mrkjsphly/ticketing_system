<h2 class="page-title">Assigned Tickets</h2>

<div class="table-controls">
    <form method="get" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0;">
        <input type="text" name="search" placeholder="Search ticket code or requester..."
            value="<?= htmlspecialchars($this->input->get('search') ?? '') ?>" style="width: 260px;">

        <select name="status">
            <option value="">All Status</option>
            <?php foreach (['Endorsed', 'In Progress', 'Resolved', 'For Closure', 'Closed', 'Cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= $this->input->get('status') == $s ? 'selected' : '' ?>>
                    <?= $s ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="priority">
            <option value="">All Priority</option>
            <?php foreach (['Low', 'Medium', 'High', 'Critical'] as $p): ?>
                <option value="<?= $p ?>" <?= $this->input->get('priority') == $p ? 'selected' : '' ?>>
                    <?= $p ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary">Search</button>
        <a href="<?= site_url('accounting/tickets') ?>" class="btn-secondary">Reset</a>
    </form>
</div>

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
            <?php if (!empty($tickets)): ?>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
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
                                <button class="btn-icon btn-icon-view" title="View"
                                    onclick="openViewModal(<?= $ticket->id ?>)">
                                    👁
                                </button>
                                <?php if (in_array($ticket->ticket_status, ['Endorsed', 'In Progress'])): ?>
                                    <button class="btn-icon btn-icon-status" title="Update Status"
                                        onclick="openStatusModal(<?= $ticket->id ?>, '<?= $ticket->ticket_status ?>')">
                                        ✏️
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center; color:#6c757d; padding:30px;">
                        No tickets found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">
    <?= $pagination ?>
</div>


<!-- VIEW TICKET MODAL -->
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

        <div class="ticket-description-box" id="view_resolution_box" style="display:none;">
            <span class="detail-label">Resolution Notes</span>
            <div id="view_resolution_notes" class="description-content"></div>
            <div style="display:flex; gap:20px; margin-top:10px;">
                <div>
                    <span class="detail-label">Resolved By</span>
                    <div class="detail-value" id="view_resolved_by"></div>
                </div>
                <div>
                    <span class="detail-label">Resolved At</span>
                    <div class="detail-value" id="view_resolved_at"></div>
                </div>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeViewModal()">Close</button>
        </div>

    </div>
</div>


<!-- UPDATE STATUS MODAL -->
<div id="statusModal" class="modal-overlay">
    <div class="modal-box" style="width: 380px;">
        <h3>Update Ticket Status</h3>
        <p style="font-size: 13px; color: #6c757d; margin-top: -10px;">
            Select the new status for this ticket.
        </p>

        <form id="statusForm" method="post">
            <input type="hidden" name="ticket_id" id="status_ticket_id">

            <div class="form-group">
                <label>Status</label>
                <select name="ticket_status" id="status_select" onchange="toggleResolutionNotes()">
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                </select>
            </div>

            <div class="form-group" id="resolution_notes_group" style="display: none;">
                <label>Resolution Notes <span style="color: #d93025;">*</span></label>
                <textarea name="resolution_notes" id="resolution_notes"
                    style="width:100%; height:90px; resize:none; padding:8px; border:1px solid #e3e6f0; border-radius:4px; font-size:13px; font-family:inherit; box-sizing:border-box;"></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeStatusModal()">Cancel</button>
                <button type="submit" class="btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>


<script>
    function openViewModal(ticket_id) {
        fetch("<?= site_url('accounting/get_ticket/') ?>" + ticket_id)
            .then(r => r.json())
            .then(data => {
                document.getElementById('view_ticket_code').innerText = data.ticket_code;
                document.getElementById('view_client').innerText      = data.client_name;
                document.getElementById('view_requester').innerText   = data.requester_name;
                document.getElementById('view_category').innerText    = data.category;
                document.getElementById('view_description').innerText = data.description;

                const date = new Date(data.created_at);
                document.getElementById('view_created').innerText = date.toLocaleString('en-PH', {
                    month: 'short', day: 'numeric', year: 'numeric',
                    hour: 'numeric', minute: '2-digit', hour12: true
                });

                const pb = document.getElementById('view_priority_badge');
                pb.innerText   = data.priority;
                pb.className   = 'badge badge-' + data.priority.toLowerCase();

                const sb = document.getElementById('view_status_badge');
                sb.innerText = data.ticket_status;
                sb.className = 'badge badge-' + data.ticket_status.toLowerCase().replace(/ /g, '');

                const resolutionBox = document.getElementById('view_resolution_box');
                if (data.resolution_details) {
                    document.getElementById('view_resolution_notes').innerText = data.resolution_details;
                    document.getElementById('view_resolved_by').innerText = data.resolved_by_name ?? 'N/A';
                    document.getElementById('view_resolved_at').innerText = data.resolved_at
                        ? new Date(data.resolved_at).toLocaleString('en-PH', {
                            month: 'short', day: 'numeric', year: 'numeric',
                            hour: 'numeric', minute: '2-digit', hour12: true
                          })
                        : 'N/A';
                    resolutionBox.style.display = 'block';
                } else {
                    resolutionBox.style.display = 'none';
                }

                document.getElementById('viewTicketModal').style.display = 'flex';
            });
    }

    function closeViewModal() {
        document.getElementById('viewTicketModal').style.display = 'none';
    }

    function openStatusModal(ticket_id, current_status) {
        document.getElementById('status_ticket_id').value = ticket_id;
        document.getElementById('statusForm').action = "<?= site_url('accounting/update_status/') ?>" + ticket_id;

        const select = document.getElementById('status_select');
        select.value = current_status === 'In Progress' ? 'Resolved' : 'In Progress';

        toggleResolutionNotes();
        document.getElementById('statusModal').style.display = 'flex';
    }

    function closeStatusModal() {
        document.getElementById('statusModal').style.display = 'none';
        document.getElementById('resolution_notes').value = '';
        document.getElementById('resolution_notes_group').style.display = 'none';
    }

    function toggleResolutionNotes() {
        const status = document.getElementById('status_select').value;
        const group  = document.getElementById('resolution_notes_group');
        group.style.display = status === 'Resolved' ? 'block' : 'none';
        document.getElementById('resolution_notes').required = status === 'Resolved';
    }
</script>