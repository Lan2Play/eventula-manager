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
        {{ Form::label('ticket-group-name','Ticket group name') }}
        {{
            Form::text(
                'ticket-group-name',
                isset($ticketGroup) ? $ticketGroup->name : null,
                [
                    'id'=>'ticket-group-name',
                    'class'=>'form-control'
                ]
            )
        }}
    </div>
</div>

<div class="row">
    <div class="mb-3 col-12">
        {{ Form::label('ticket-group-tickets','Ticket group name') }}
        {{
            Form::number(
                'ticket-group-tickets',
                isset($ticketGroup) ? $ticketGroup->tickets_per_user : 0,
                [
                    'id'=>'ticket-group-tickets',
                    'class'=>'form-control',
                    'min' => 0
                ]
            )
        }}
    </div>

</div>
<button type="submit" class="btn btn-secondary btn-success btn-block">Submit</button>

