<h2 class="page-title">All Tickets</h2>

<?php $success = $this->session->flashdata('success'); ?>
<?php if ($success): ?>
    <p style="color: #1f8f4e; margin-bottom: 15px;"><?= $success ?></p>
<?php endif; ?>

<div class="table-controls">
    <form method="get" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0;">
        <input type="text" name="search" placeholder="Search ticket code or requester..."
            value="<?= htmlspecialchars($this->input->get('search') ?? '') ?>" style="width: 240px;">

        <select name="status">
            <option value="">All Status</option>
            <?php foreach (['New', 'Endorsed', 'In Progress', 'Resolved', 'For Closure', 'Closed', 'Cancelled'] as $s): ?>
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

        <select name="team">
            <option value="">All Teams</option>
            <?php foreach ($teams as $team): ?>
                <option value="<?= $team->id ?>" <?= $this->input->get('team') == $team->id ? 'selected' : '' ?>>
                    <?= $team->team_name ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary">Search</button>
        <a href="<?= site_url('tl/tickets') ?>" class="btn-secondary">Reset</a>
    </form>
</div>

<div class="table-responsive">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Ticket Code</th>
                <th>Client</th>
                <th>Requester</th>
                <th>Category</th>
                <th>Team</th>
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
                        <td><?= $ticket->team_name ?? 'Unassigned' ?></td>
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
                                <?php if ($ticket->ticket_status === 'For Closure'): ?>
                                    <a href="<?= site_url('tl/close_ticket/' . $ticket->id) ?>"
                                        class="btn-icon btn-icon-closure" title="Close Ticket"
                                        onclick="return confirm('Are you sure you want to close this ticket?')">
                                        ✔
                                    </a>
                                <?php endif; ?>
                                <?php if (!in_array($ticket->ticket_status, ['Cancelled', 'Closed'])): ?>
                                    <button class="btn-icon btn-icon-status" title="Reassign"
                                        onclick="openReassignModal(<?= $ticket->id ?>, '<?= htmlspecialchars($ticket->team_name ?? '') ?>')">
                                        🔀
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align:center; color:#6c757d; padding:30px;">
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
                <span class="detail-label">Assigned Team</span>
                <span class="detail-value" id="view_team"></span>
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

        <div class="ticket-description-box" id="view_closure_box" style="display:none;">
            <div style="display:flex; gap:20px;">
                <div>
                    <span class="detail-label">Closed By</span>
                    <div class="detail-value" id="view_closed_by"></div>
                </div>
                <div>
                    <span class="detail-label">Closed At</span>
                    <div class="detail-value" id="view_closed_at"></div>
                </div>
            </div>
        </div>

        <!-- TICKET ACTIVITY TIMELINE -->
        <div class="ticket-description-box">
            <span class="detail-label">Activity Timeline</span>
            <div id="view_activities" style="margin-top: 8px;"></div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeViewModal()">Close</button>
        </div>

    </div>
</div>


<!-- REASSIGN MODAL -->
<div id="reassignModal" class="modal-overlay">
    <div class="modal-box" style="width: 380px;">
        <h3>Reassign Ticket</h3>
        <p style="font-size: 13px; color: #6c757d; margin-top: -10px;">
            Select a team to reassign this ticket to.
        </p>

        <form id="reassignForm" method="post">
            <div class="form-group">
                <label>Current Team</label>
                <input type="text" id="reassign_current_team" disabled
                    style="background: #f8f9fa; color: #6c757d;">
            </div>

            <div class="form-group">
                <label>Assign To</label>
                <select name="assigned_team" id="reassign_team" required>
                    <option value="">Select Team</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team->id ?>"><?= $team->team_name ?> (<?= $team->role ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeReassignModal()">Cancel</button>
                <button type="submit" class="btn-primary">Reassign</button>
            </div>
        </form>
    </div>
</div>


<script>
    function openViewModal(ticket_id) {
        fetch("<?= site_url('tl/get_ticket/') ?>" + ticket_id)
            .then(r => r.json())
            .then(data => {
                document.getElementById('view_ticket_code').innerText = data.ticket_code;
                document.getElementById('view_client').innerText = data.client_name;
                document.getElementById('view_requester').innerText = data.requester_name;
                document.getElementById('view_category').innerText = data.category;
                document.getElementById('view_team').innerText = data.team_name ?? 'Unassigned';
                document.getElementById('view_description').innerText = data.description;

                document.getElementById('view_created').innerText = new Date(data.created_at).toLocaleString('en-PH', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                const pb = document.getElementById('view_priority_badge');
                pb.innerText = data.priority;
                pb.className = 'badge badge-' + data.priority.toLowerCase();

                const sb = document.getElementById('view_status_badge');
                sb.innerText = data.ticket_status;
                sb.className = 'badge badge-' + data.ticket_status.toLowerCase().replace(/ /g, '');

                // Resolution box
                const resolutionBox = document.getElementById('view_resolution_box');
                if (data.resolution_details) {
                    document.getElementById('view_resolution_notes').innerText = data.resolution_details;
                    document.getElementById('view_resolved_by').innerText = data.resolved_by_name ?? 'N/A';
                    document.getElementById('view_resolved_at').innerText = data.resolved_at ?
                        new Date(data.resolved_at).toLocaleString('en-PH', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        }) :
                        'N/A';
                    resolutionBox.style.display = 'block';
                } else {
                    resolutionBox.style.display = 'none';
                }

                // Closure box
                const closureBox = document.getElementById('view_closure_box');
                if (data.closed_by_name) {
                    document.getElementById('view_closed_by').innerText = data.closed_by_name;
                    document.getElementById('view_closed_at').innerText = data.closed_at ?
                        new Date(data.closed_at).toLocaleString('en-PH', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        }) :
                        'N/A';
                    closureBox.style.display = 'block';
                } else {
                    closureBox.style.display = 'none';
                }

                // Activity timeline
                const activityDiv = document.getElementById('view_activities');
                if (data.activities && data.activities.length > 0) {
                    activityDiv.innerHTML = data.activities.map(a => `
                        <div style="display:flex; gap:10px; padding: 6px 0; border-bottom: 1px solid #f0f0f0; font-size:13px;">
                            <span style="color:#6c757d; white-space:nowrap;">
                                ${new Date(a.created_at).toLocaleString('en-PH', {
                                    month: 'short', day: 'numeric', year: 'numeric',
                                    hour: 'numeric', minute: '2-digit', hour12: true
                                })}
                            </span>
                            <span style="color:#333;">${a.activity}</span>
                            <span style="color:#2b5fd9; margin-left:auto; white-space:nowrap;">${a.full_name ?? 'System'}</span>
                        </div>
                    `).join('');
                } else {
                    activityDiv.innerHTML = '<p style="color:#6c757d; font-size:13px;">No activity recorded.</p>';
                }

                document.getElementById('viewTicketModal').style.display = 'flex';
            });
    }

    function closeViewModal() {
        document.getElementById('viewTicketModal').style.display = 'none';
    }

    function openReassignModal(ticket_id, current_team) {
        document.getElementById('reassign_current_team').value = current_team || 'Unassigned';
        document.getElementById('reassignForm').action = "<?= site_url('tl/reassign/') ?>" + ticket_id;
        document.getElementById('reassignModal').style.display = 'flex';
    }

    function closeReassignModal() {
        document.getElementById('reassignModal').style.display = 'none';
    }
</script>