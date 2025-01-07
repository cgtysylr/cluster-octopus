<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alarm Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .alarm-card {
            border-left: 5px solid;
            margin-bottom: 1rem;
        }
        .critical {
            border-color: #dc3545;
        }
        .warning {
            border-color: #ffc107;
        }
        .info {
            border-color: #17a2b8;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Alarm Management</h1>

    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control" id="searchInput" placeholder="Search alarms...">
        </div>
        <div class="col-md-4">
            <select class="form-select" id="filterSelect">
                <option value="">Filter by Severity</option>
                <option value="critical">Critical</option>
                <option value="warning">Warning</option>
                <option value="info">Info</option>
            </select>
        </div>
    </div>

    <div id="alarmList">
        <!-- Sample Alarm Cards -->
        <div class="card alarm-card critical">
            <div class="card-body">
                <h5 class="card-title">Critical Alarm</h5>
                <p class="card-text">Server 01 is down.</p>
                <button class="btn btn-danger btn-sm">Resolve</button>
            </div>
        </div>

        <div class="card alarm-card warning">
            <div class="card-body">
                <h5 class="card-title">Warning Alarm</h5>
                <p class="card-text">High memory usage detected.</p>
                <button class="btn btn-warning btn-sm">Investigate</button>
            </div>
        </div>

        <div class="card alarm-card info">
            <div class="card-body">
                <h5 class="card-title">Info Alarm</h5>
                <p class="card-text">Backup completed successfully.</p>
                <button class="btn btn-info btn-sm">Acknowledge</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#alarmList .card').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $('#filterSelect').on('change', function() {
            const value = $(this).val();
            $('#alarmList .card').show();
            if (value) {
                $('#alarmList .card').not('.' + value).hide();
            }
        });
    });
</script>

</body>
</html>
