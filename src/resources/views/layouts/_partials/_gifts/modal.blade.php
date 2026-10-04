<!-- Gift Modal -->
<div class="modal fade" id="giftTicketModal" tabindex="-1" role="dialog" aria-labelledby="giftTicketModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="giftTicketLabel">Are you sure you want to set this ticket as a gift?</h4>
				<button type="button" class="btn-close text-decoration-none" data-bs-dismiss="modal" aria-hidden="true"></button>
			</div>
			{{ Form::open(array('url'=>'/gift/', 'id'=>'giftTicketForm')) }}
				<div class="modal-body">
					<h4></h4>
					<h4>How it works</h4>
					<ol>
						<li>
							Press 'Yes' below
						</li>
						<li>
							Copy the URL Code Generated
						</li>
						<li>
							Paste it to your Friend
						</li>
						<li>
							Tell your friend to visit the URL and claim their ticket
						</li>
					</ol>
				</div>
				<div class="modal-footer">
					<div class="col-lg-9">
						<h5>Are you sure you want to set this ticket as a gift?</h5>
					</div>
					<div class="col-lg-3">
						<button type="submit" class="btn btn-success">Yes</button>
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
					</div>
				</div>
			{{ Form::close() }}
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
	function giftTicket(participant_id)
	{
		jQuery("#giftTicketForm").prop('action', '/gift/' + participant_id);
	}
</script>