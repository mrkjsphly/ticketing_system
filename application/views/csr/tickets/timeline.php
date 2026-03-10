<?php $this->load->view('csr/layout/header'); ?>
<?php $this->load->view('csr/layout/sidebar'); ?>

<h2 class="page-title">Ticket Timeline</h2>

<div class="timeline-container">

    <?php foreach ($activities as $activity): ?>

        <div class="timeline-item">

            <div class="timeline-dot"></div>

            <div class="timeline-content">

                <div class="timeline-title">
                    Ticket <?= $activity->ticket_code ?>
                </div>

                <div class="timeline-text">
                    <?= $activity->activity ?>
                </div>

                <div class="timeline-date">
                    <?= date('M d, Y h:i A', strtotime($activity->created_at)) ?>
                </div>

            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php $this->load->view('csr/layout/footer'); ?>