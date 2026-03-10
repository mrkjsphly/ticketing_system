<h2 class="page-title">Ticket Timeline</h2>

<!-- Filter Bar -->
<form method="get" action="<?= site_url('tickets/timeline') ?>" class="table-controls" style="margin-bottom: 20px;">
    <input type="text" name="code" id="filter_ticket"
        placeholder="Search ticket code..."
        value="<?= $filter_code ?>">
    <select name="action" id="filter_action">
        <option value="">All Actions</option>
        <option value="created" <?= $filter_action == 'created'    ? 'selected' : '' ?>>Created</option>
        <option value="cancelled" <?= $filter_action == 'cancelled'  ? 'selected' : '' ?>>Cancelled</option>
        <option value="resolved" <?= $filter_action == 'resolved'   ? 'selected' : '' ?>>Resolved</option>
        <option value="closed" <?= $filter_action == 'closed'     ? 'selected' : '' ?>>Closed</option>
        <option value="progress" <?= $filter_action == 'progress'   ? 'selected' : '' ?>>In Progress</option>
    </select>
    <input type="date" name="date_from" value="<?= $date_from ?>">
    <input type="date" name="date_to" value="<?= $date_to ?>">
    <button type="submit" class="btn-primary">Filter</button>
    <a href="<?= site_url('tickets/timeline') ?>" class="btn-secondary">Reset</a>
</form>

<div class="timeline-container" id="timeline-list">

    <?php if (empty($activities)): ?>
        <div class="timeline-empty">
            <p>🎉 No activity yet. Create your first ticket to get started!</p>
        </div>
    <?php else: ?>

        <?php
        $current_date = null;
        foreach ($activities as $activity):
            $activity_lower = strtolower($activity->activity);
            if (strpos($activity_lower, 'created') !== false) {
                $action_type = 'created';
                $badge_label = 'CREATED';
            } elseif (strpos($activity_lower, 'cancelled') !== false) {
                $action_type = 'cancelled';
                $badge_label = 'CANCELLED';
            } elseif (strpos($activity_lower, 'resolved') !== false) {
                $action_type = 'resolved';
                $badge_label = 'RESOLVED';
            } elseif (strpos($activity_lower, 'closed') !== false) {
                $action_type = 'closed';
                $badge_label = 'CLOSED';
            } elseif (strpos($activity_lower, 'progress') !== false) {
                $action_type = 'inprogress';
                $badge_label = 'IN PROGRESS';
            } else {
                $action_type = 'updated';
                $badge_label = 'UPDATED';
            }

            $entry_date = date('F d, Y', strtotime($activity->created_at));
            if ($entry_date !== $current_date):
                $current_date = $entry_date;
        ?>
                <div class="timeline-date-group"><?= $entry_date ?></div>
            <?php endif; ?>

            <div class="timeline-item"
                data-action="<?= $action_type ?>"
                data-code="<?= strtolower($activity->ticket_code) ?>"
                data-date="<?= date('Y-m-d', strtotime($activity->created_at)) ?>">

                <div class="timeline-dot dot-<?= $action_type ?>"></div>
                <div class="timeline-content">

                    <div class="timeline-header">
                        <a href="javascript:void(0);"
                            class="timeline-ticket-code"
                            onclick="openViewTicketModal(<?= $activity->ticket_id ?>)">
                            <?= $activity->ticket_code ?>
                        </a>
                        <span class="timeline-badge badge-<?= $action_type ?>"><?= $badge_label ?></span>
                        <?php if (!empty($activity->priority)): ?>
                            <span class="badge badge-<?= strtolower($activity->priority) ?>"><?= $activity->priority ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($activity->subject)): ?>
                        <div class="timeline-subject"><?= htmlspecialchars($activity->subject) ?></div>
                    <?php endif; ?>

                    <div class="timeline-meta">
                        <?php if (!empty($activity->client_name)): ?>
                            <span>🏢 <?= htmlspecialchars($activity->client_name) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($activity->performed_by_name)): ?>
                            <span>👤 <?= htmlspecialchars($activity->performed_by_name) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="timeline-text"><?= $activity->activity ?></div>

                    <div class="timeline-date">
                        <?= date('h:i A', strtotime($activity->created_at)) ?>
                    </div>

                </div>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<!-- Load More -->
<?php if ($has_more): ?>
    <div id="load-more-wrapper" style="text-align: center; margin: 20px 0;">
        <button class="btn-secondary" id="load-more-btn" onclick="loadMore()">
            Load More
        </button>
    </div>
<?php endif; ?>

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
        <div class="modal-actions">
            <button type="button" class="btn-secondary" onclick="closeViewTicketModal()">Close</button>
        </div>
    </div>
</div>

<script>
    // ===== LOAD MORE =====
    let currentOffset = <?= $next_offset ?>;
    const baseUrl = "<?= site_url('tickets/timeline') ?>";

    function loadMore() {
        const btn = document.getElementById('load-more-btn');
        btn.innerText = 'Loading...';
        btn.disabled = true;

        const params = new URLSearchParams(window.location.search);
        params.set('offset', currentOffset);

        fetch(baseUrl + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.activities.length > 0) {
                    appendActivities(data.activities);
                    currentOffset = data.next_offset;
                }
                if (!data.has_more) {
                    document.getElementById('load-more-wrapper').style.display = 'none';
                } else {
                    btn.innerText = 'Load More';
                    btn.disabled = false;
                }
            });
    }

    function appendActivities(activities) {
        const container = document.getElementById('timeline-list');
        let lastDate = container.querySelector('.timeline-date-group:last-of-type')?.innerText || '';

        activities.forEach(a => {
            const date = new Date(a.created_at).toLocaleDateString('en-PH', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });

            if (date !== lastDate) {
                lastDate = date;
                const dg = document.createElement('div');
                dg.className = 'timeline-date-group';
                dg.innerText = date;
                container.appendChild(dg);
            }

            const actLower = a.activity.toLowerCase();
            let actionType, badgeLabel;
            if (actLower.includes('created')) {
                actionType = 'created';
                badgeLabel = 'CREATED';
            } else if (actLower.includes('cancelled')) {
                actionType = 'cancelled';
                badgeLabel = 'CANCELLED';
            } else if (actLower.includes('resolved')) {
                actionType = 'resolved';
                badgeLabel = 'RESOLVED';
            } else if (actLower.includes('closed')) {
                actionType = 'closed';
                badgeLabel = 'CLOSED';
            } else if (actLower.includes('progress')) {
                actionType = 'inprogress';
                badgeLabel = 'IN PROGRESS';
            } else {
                actionType = 'updated';
                badgeLabel = 'UPDATED';
            }

            const item = document.createElement('div');
            item.className = `timeline-item`;
            item.dataset.action = actionType;
            item.dataset.code = a.ticket_code.toLowerCase();
            item.dataset.date = a.created_at.substring(0, 10);
            item.innerHTML = `
                <div class="timeline-dot dot-${actionType}"></div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <a href="javascript:void(0);" class="timeline-ticket-code"
                           onclick="openViewTicketModal(${a.ticket_id})">${a.ticket_code}</a>
                        <span class="timeline-badge badge-${actionType}">${badgeLabel}</span>
                        ${a.priority ? `<span class="badge badge-${a.priority.toLowerCase()}">${a.priority}</span>` : ''}
                    </div>
                    ${a.subject ? `<div class="timeline-subject">${a.subject}</div>` : ''}
                    <div class="timeline-meta">
                        ${a.client_name ? `<span>🏢 ${a.client_name}</span>` : ''}
                        ${a.performed_by_name ? `<span>👤 ${a.performed_by_name}</span>` : ''}
                    </div>
                    <div class="timeline-text">${a.activity}</div>
                    <div class="timeline-date">${new Date(a.created_at).toLocaleTimeString('en-PH', {hour:'numeric', minute:'2-digit', hour12:true})}</div>
                </div>`;
            container.appendChild(item);
        });
    }

    // ===== VIEW MODAL =====
    function openViewTicketModal(ticket_id) {
        fetch("<?= site_url('tickets/get_ticket/') ?>" + ticket_id)
            .then(r => r.json())
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

                const pb = document.getElementById('view_priority_badge');
                pb.innerText = data.priority;
                pb.className = 'badge badge-' + data.priority.toLowerCase();

                const sb = document.getElementById('view_status_badge');
                sb.innerText = data.ticket_status;
                sb.className = 'badge badge-' + data.ticket_status.toLowerCase().replace(' ', '');

                document.getElementById('viewTicketModal').style.display = 'flex';
            });
    }

    function closeViewTicketModal() {
        document.getElementById('viewTicketModal').style.display = 'none';
    }
</script>

<?php $this->load->view('csr/layout/footer'); ?>