<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cluster Octopus</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .blinking {
            animation: blinkingText 1.5s infinite;
        }

        @keyframes blinkingText {
            0% { color: red; }
            50% { color: transparent; }
            100% { color: red; }
        }

        .group-header {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .group-header:hover {
            background-color: #f8f9fa; /* Bootstrap light-gray */
        }

        .app-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .app-title img {
            width: 120px;
            height: 120px;
        }

        .app-title h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
            color: #333333; /* Bootstrap primary color */
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <!-- Application Title with Icon -->
    <div class="app-title">
        <img src="/images/octopus.png" alt="Cluster Octopus Logo">
        <h1>Cluster Octopus</h1>
    </div>

    <!-- Summary Section -->
    <div id="summary" class="alert text-center fw-bold" role="alert">

    </div>

    <!-- Grouped Accessibility List -->
    <div id="grouped-accessibility-list" class="accordion" id="accessibilityAccordion">
        <!-- Dynamic content will be inserted here -->
    </div>
</div>

<script>
    // Grup durumlarını izlemek için global bir değişken
    let groupState = {}; // Bu değişken burada tanımlanmalı

    function fetchAndUpdateData() {
        $.ajax({
            url: '/get-errors', // Laravel endpoint
            method: 'GET',
            success: function (data) {
                updateUI(data);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    function updateUI(data) {
        const groupedData = data.reduce((acc, item) => {
            acc[item.description] = acc[item.description] || [];
            acc[item.description].push(item);
            return acc;
        }, {});

        const container = $('#grouped-accessibility-list');
        container.empty();

        const totalIssues = data.length;

        // Summary bölümünü dinamik olarak güncelle
        const summaryElement = $('#summary');
        if (totalIssues > 0) {
            summaryElement.removeClass('alert-success').addClass('alert-danger');
            summaryElement.text(`Total Issues: ${totalIssues}`);
        } else {
            summaryElement.removeClass('alert-danger').addClass('alert-success');
            summaryElement.text('EVERYTHING IS OK');
        }

        let index = 0;
        for (const [group, items] of Object.entries(groupedData)) {
            const isOpen = groupState[group] || false;
            const groupId = `group-${index}`;
            const collapseId = `collapse-${index}`;

            const groupHeader = $(`
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="${groupId}">
                            <button class="accordion-button ${isOpen ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${isOpen}" aria-controls="${collapseId}">
                                ${group} (${items.length})
                            </button>
                        </h2>
                        <div id="${collapseId}" class="accordion-collapse collapse ${isOpen ? 'show' : ''}" aria-labelledby="${groupId}" data-bs-parent="#accessibilityAccordion">
                            <div class="accordion-body">
                                <ul class="list-group">
                                </ul>
                            </div>
                        </div>
                    </div>
                `);

            const groupItems = groupHeader.find('.list-group');
            items.forEach(item => {
                const listItem = `
                        <li class="list-group-item">
                            <strong>Source:</strong> ${item.source} <span class="text-primary">&#8594;</span> <strong>Destination:</strong> ${item.destination} <br>
                            <strong>Port:</strong> ${item.port} <br>
                            <strong>Status:</strong> <span class="blinking">${item.status}</span>
                        </li>
                    `;
                groupItems.append(listItem);
            });

            container.append(groupHeader);

            groupHeader.find('.accordion-button').on('click', function () {
                groupState[group] = !groupState[group];
            });

            index++;
        }
    }

    $(document).ready(function () {
        fetchAndUpdateData(); // İlk veri çekme
        setInterval(fetchAndUpdateData, 5000); // 5 saniyede bir yenile
    });
</script>


</body>
</html>
