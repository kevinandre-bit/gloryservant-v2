{{-- Admin Send Alert Modal --}}
<div class="modal fade" id="sendAlertModal" tabindex="-1" aria-labelledby="sendAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendAlertModalLabel">Send Alert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendAlertForm">
                    <div class="mb-3">
                        <label for="alertTitle" class="form-label">Alert Title</label>
                        <input type="text" class="form-control" id="alertTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="alertMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="alertMessage" name="message" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="alertType" class="form-label">Alert Type</label>
                        <select class="form-select" id="alertType" name="type">
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                            <option value="success">Success</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendAlert()">Send Alert</button>
            </div>
        </div>
    </div>
</div>

<script>
function sendAlert() {
    // Add your alert sending logic here
    console.log('Sending alert...');
    // Close modal after sending
    var modal = bootstrap.Modal.getInstance(document.getElementById('sendAlertModal'));
    modal.hide();
}
</script>