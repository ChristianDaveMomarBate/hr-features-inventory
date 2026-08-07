    <script src="{{ asset('js/boostrapcdn.min.js') }}"></script>
    <script src="{{ asset('js/ajax.min.js') }}"></script>
<style>
    .alert-danger-alt {
        border-color: #B63E5A;
        background: #E26868;
        color: #fff;
    }

    .alert-success-alt {
        border-color: #19B99A;
        background: #20A286;
        color: #fff;
    }
</style>
<script type="text/javascript">
    ///////CATEGORY
    $(document).ready(function() {
        $(".btn-group .btn").click(function() {
            var inputValue = $(this).find("input").val();
            if (inputValue != 'all') {
                var target = $('table tr[data-status="' + inputValue + '"]');
                $("table tbody tr").not(target).hide();
                target.fadeIn();
            } else {
                $("table tbody tr").fadeIn();
            }
        });
        var bs = $.fn.tooltip.Constructor.VERSION;
        var str = bs.split(".");
        if (str[0] == 4) {
            $(".label").each(function() {
                var classStr = $(this).attr("class");
                var newClassStr = classStr.replace(/label/g, "badge");
                $(this).removeAttr("class").addClass(newClassStr);
            });
        }
    });
</script>
<script>
    $(document).on('click', '.saveCategory', function(e) {
        var data = {
            'category': $('.category').val(),
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "POST",
            url: "/product",
            data: data,
            dataType: "json",
            success: function(response) {
                if (response.status == 400) {
                    $('#save_msgList').addClass('alert alert-danger-alt');
                    $.each(response.errors, function(key, err_value) {
                        $('#save_msgList').append('<li>' + err_value + '</li>');
                    });
                    $('.saveCategory').text('Save');
                } else {
                    $('#success_message').addClass(
                        'alert-success-alt');
                    $('#success_message').text(response.message);
                    $("#success_message").fadeTo(2000, 500).slideUp(500, function() {
                        $("#success_message").slideUp(500);
                    });
                    $('.saveCategory').text('Save');
                }
            }
        });
    });
    ///////PRODUCT SAVE
    $(document).on('click', '.addProduct', function(e) {
        var data = {
            'pcategory': $('.pcategoryadd').val(),
            'pname': $('.pnameadd').val(),
            'pprice': $('.ppriceadd').val(),
            'pstock': $('.pstockadd').val(),
            'pdtime': $('.pdtimeadd').val(),
            'pdescription': $('.pdescriptionadd').val(),
            'pimage': $('.pimageadd').val(),
        }
        console.log(data);
    });
    $(document).on('click', '.reload', function(e) {
        window.location.reload();
    });
</script>
