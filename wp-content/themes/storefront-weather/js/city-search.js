jQuery(document).ready(function ($) {
    function loadCities(searchValue = '') {
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'search_cities',
                search: searchValue
            },
            success: function (response) {
                $('#cities-table').html(response);
            }
        });
    }
    loadCities();

    $('#search-button').on('click', function () {
        const searchValue = $('#city-search').val();
        loadCities(searchValue);
    });
    
});