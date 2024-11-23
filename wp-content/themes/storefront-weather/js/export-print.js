jQuery(document).ready(function ($) {
    // Export to CSV
    $('#export-csv').on('click', function () {
        const table = $('#cities-table table');
        if (!table.length) {
            alert('No data to export!');
            return;
        }

        let csv = [];
        table.find('tr').each(function () {
            const row = [];
            $(this).find('th, td').each(function () {
                row.push($(this).text().trim());
            });
            csv.push(row.join(','));
        });

        const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
        const link = document.createElement('a');
        link.setAttribute('href', encodeURI(csvContent));
        link.setAttribute('download', 'cities_table.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Print Table
    $('#print-table').on('click', function () {
        const tableContent = $('#cities-table').html();
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print Table</title></head><body>');
        printWindow.document.write(tableContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
});
