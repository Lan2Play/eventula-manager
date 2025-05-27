@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row">
    <div class="mb-3 col-12">
        {!! Html::label('Ticket group name', 'ticket-group-name') !!}
        {!! Html::text('ticket-group-name', isset($ticketGroup) ? $ticketGroup->name : null)->id('ticket-group-name')->class('form-control') !!}
    </div>
</div>

<div class="row">
    <div class="mb-3 col-12">
        {!! Html::label('No. tickets per user', 'ticket-group-tickets') !!}
        {!! Html::number('ticket-group-tickets', isset($ticketGroup) ? $ticketGroup->tickets_per_user : 0)->id('ticket-group-tickets')->class('form-control')->attribute('min', '0') !!}
    </div>

</div>
<button type="submit" class="btn btn-secondary btn-success btn-block">Submit</button>

