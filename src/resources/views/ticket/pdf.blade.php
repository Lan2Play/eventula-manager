<html>
<head>
    <style>
        @page {
            margin: 20mm 0;
        }

        #header {
            position: fixed;
            top: -10mm;
            left: 0;
            right: 0;
        }

        #header .site-name {
            text-align: center;
        }

        .center {
            text-align: center;
        }

        #content {
            margin: 0 5mm;
        }

        table th {
            text-align: left;
            padding-right: 5mm;
        }

        .qr-code-container p {
            margin-top: 10mm;
            margin-bottom: 10mm;
        }

        .qr-code {
            width: 50mm;
        }

        #footer {
            position: fixed;
            bottom: -10mm;
            left: 5mm;
            right: 5mm;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
<div id="header">
    <div class="site-name">{{ Settings::getOrgTagline() }} | {{ Settings::getOrgName() }}</div>
</div>
<div id="footer">
    <p>@lang('tickets.generated_at', ['date' => $data->date, 'time' => $data->time])</p>
</div>
<div id="content">
    <h1 class="center">@lang('tickets.pdf_header', ['name' => $data->event_name] )</h1>
    <table>
        <tr>
            <th>@lang('tickets.username')</th>
            <td>{{ $data->username }}</td>
        </tr>
        <tr>
            <th>@lang('tickets.realname')</th>
            <td>@lang('tickets.realname_format', ['firstname' => $data->firstname, 'lastname' => $data->surname])</td>
        </tr>
        <tr>
            <th>@lang('tickets.ticket_name')</th>
            <td>{{ $data->ticket_name }}</td>
        </tr>
        <tr>
            <th>@lang('tickets.seat')</th>
            <td>{{ $data->seat }}</td>
        </tr>
        <tr>
            <th>@lang('tickets.seat_in')</th>
            <td>{{ $data->seating_plan }}</td>
        </tr>
    </table>
    <div class="qr-code-container">
        <p>@lang('tickets.present_qr_code')</p>
        <div class="center">
            <img class="qr-code" src="{{ $data->qr_image }}"/>
        </div>
    </div>
</div>
</body>
</html>