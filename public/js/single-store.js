$(document).ready(function () {


    $('.custom-order-btn').click(function () {
        console.log(324);
        $(this).prop('disabled', true);
    });

    // Wallet Setting Switch
    $('body').on('change','.wallet-switch',function () {
        var status = '';
        if($(this).is(':checked')){
            status = 1;
            $('.status-text').text('Enabled')
        }
        else{
            status = 0;
            $('.status-text').text('Disabled')
        }
        $.ajax({
            url: $(this).data('route'),
            type: 'post',
            data:{
                _token: $(this).data('csrf'),
                status : status
            }
        })
    });



    /*BULK ORDER PAY*/
    $('.check-order-all').change(function () {
        unset_bulk_array();
        set_bulk_array();

        if($(this).is(':checked')){
            $('.bulk-div').show();
        }
        else{
            $('.bulk-div').hide();

        }

    });

    $('.check-order').change(function () {
        if($(this).is(':checked')){
            $('.bulk-div').show();
            unset_bulk_array();
            set_bulk_array();
        }
        else{
            unset_bulk_array();
            set_bulk_array();
            if($('.check-order:checked').length === 0){
                $('.bulk-div').hide();
            }

        }

    });
    function set_bulk_array() {
        var values = [];
        $('.check-order:checked').each(function () {
            values.push($(this).val());
        });
         $('#bulk-payment').find('input:hidden[name=orders]').val(values);

    }
    function unset_bulk_array() {
         $('#bulk-payment').find('input:hidden[name=orders]').val('');

    }

    $('.bulk-wallet-btn').click(function () {
        $('#bulk-payment').submit();
    });


    /*Popup Code*/

    var url_string = window.location.href;
    var url = new URL(url_string);
    var c = url.searchParams.get("ftl");
    if(c !== null){
        $.ajax({
            url:$('#questionnaire_modal').data('route'),
            type: 'GET',
            data:{
                shop : $('#questionnaire_modal').data('shop'),
            },
            success:function (response) {
                if(response.popup === 'yes'){
                    $('#questionnaire_modal').modal();
                }
            }

        });
    }


    $('body').on('click','.upload-manager-profile',function () {
        $('.manager-profile').trigger('click');
    });

    $('body').on('change','.manager-profile',function () {
        readURL(this);
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.image-drop').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }

    }


    $('body').on('click','.authenticate_user',function () {
        $('#authenticate_user_form').find('input[type=submit]').trigger('click');
    });
    $('body').on('submit','#authenticate_user_form',function (e) {
        e.preventDefault();
        $('.pre-loader').css('display','flex');
        var form  = $(this);
        $.ajax({
            url : form.attr('action'),
            type : 'post',
            data:form.serialize(),
            success:function (response) {
                $('.pre-loader').css('display','none');
                alertify.set('notifier','position', 'top-right');
                if(response.authenticate === true){
                    $('#associate_modal').modal('hide');
                    Swal.fire({
                        title: ' Are you sure?',
                        html:'<p>You want to Associate this store with this email ('+form.find('#user-email').val()+')</p>',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Associate it!'
                    }).then((result) => {
                        if (result.value) {
                            $('.pre-loader').css('display','flex');
                            $.ajax({
                                url : form.data('route'),
                                type:'post',
                                data:{
                                    _token :form.data('token'),
                                    store:form.data('store'),
                                    email :form.find('#user-email').val()
                                },
                                success:function (response) {
                                    $('.pre-loader').css('display','none');
                                    if(response.status === 'error'){
                                        alertify.error('Assigning Process Failed');
                                    }
                                    else{
                                        if(response.status === 'already_assigned'){
                                            alertify.message('Store ALready Assigned To Given Credentials');
                                        }
                                        else{
                                            Swal.fire(
                                                'Associated!',
                                                'Your store associated with given authentic credentials',
                                                'success'
                                            );
                                        }
                                        location.reload();
                                    }

                                }
                            })
                        }
                        else{
                            location.reload();
                        }
                    });
                }
                else{
                    alertify.error('Credentials Not Correct');
                }
            },
        });
    });

    $('body').on('click','.see-more-block',function () {
        $('.after12').show();
        $(this).hide();
    });
    $('body').on('click','.see-less-block',function () {
        $('.after12').hide();
        $('.see-more-block').show();

    });

    $('.js-tags-input').tagsInput({
        height: '36px',
        width: '100%',
        defaultText: 'Add tag',
        removeWithBackspace: true,
        delimiter: [',']
    });
    /*Retailer Module - Images Update JS*/
    $('body').on('click','.delete-file',function () {
        var $this = $(this);
        var file = $(this).data("file");
        $.ajax({
            url: $(this).data('route'),
            type: 'post',
            data: {
                _token: $(this).data('token'),
                request_type: $(this).data('type'),
                file: file,
            },
            success:function (data) {
                if(data.success === 'ok'){
                    $this.parents('.preview-image').remove();
                }
            }
        });
    });
    $('body').on('click','.img-avatar-variant',function () {
        var target = $(this).data('form');
        $(target).find('input[type=file]').trigger('click');
    });
    $('.varaint_file_input').change(function () {
        $(this).parents('form').submit();
    });

    /* Admin Module - Images UPLOAD JS */
    $('body').on('click','.dropzone',function () {
        $(this).next().trigger('click');
    });
    $('body').on('change','.images-upload',function (e) {
        var $this = $(this);
        var files = e.target.files;
        var filesArr = Array.prototype.slice.call(files);
        filesArr.forEach(function (f) {
            //$this.parent().find('.preview-drop').empty();
            var reader = new FileReader();
            reader.onload = function (e) {
                $this.parent().find('.preview-drop').append(' <div class="col-lg-4 preview-image animated fadeIn">\n' +
                    '            <div class="img-fluid options-item">\n' +
                    '                <img class="img-fluid options-item" src="'+e.target.result+'" alt="">\n' +
                    '            </div>\n' +
                    '        </div>');

            };
            reader.readAsDataURL(f);
        });
    });

    $('body').on('submit','.product-images-form',function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url : $(this).attr('action'),
            type : $(this).attr('method'),
            data : formData,
            cache:false,
            contentType: false,
            processData: false,
        });
    });

    /*Ajax Forms Save*/
    /*Admin Module - Update Product  Save JS*/
    $('.btn_save_retailer_product').click(function () {
        $('.pre-loader').css('display','flex');
        var forms_div =  $(this).data('tabs');
        console.log($(forms_div).find('form').length);
        if($(forms_div).find('form').length > 0){
            let forms = [];
            $(forms_div).find('form').each(function () {
                if($(this).hasClass('product-images-form')){
                    $(this).submit();
                }
                else{
                    forms.push({
                        'data' : $(this).serialize(),
                        'url' : $(this).attr('action'),
                        'method' : $(this).attr('method'),
                    });
                }

            });
            ajaxCall(forms);
        }
    });

    $('.btn_save_my_product').click(function () {
        $('.pre-loader').css('display','flex');
        var forms_div = '.my_product_form_div';
        console.log($(forms_div).find('form').length);
        if($(forms_div).find('form').length > 0){
            let forms = [];
            $(forms_div).find('form').each(function () {
                if($(this).hasClass('product-images-form')){
                    $(this).submit();
                }
                else{
                    forms.push({
                        'data' : $(this).serialize(),
                        'url' : $(this).attr('action'),
                        'method' : $(this).attr('method'),
                    });
                }

            });
            ajaxCall(forms);
        }
    });
    /*Stack ajax*/
    function ajaxCall(toAdd) {
        if (toAdd.length) {
            var request = toAdd.shift();
            var data = request.data;
            var url = request.url;
            var type = request.method;

            $.ajax({
                url: url,
                type:type,
                data: data,
                success: function(response) {
                    ajaxCall(toAdd);
                },
                error:function () {
                    ajaxCall(toAdd);
                }
            });

        } else {
            $('.pre-loader').css('display', 'none');
            Swal.fire(
                'Updated!',
                'Your Product has been Updated Successfully',
                'success'
            );
            setTimeout(function () {
                window.location.reload();
            },1000)

        }
    }

    $('body').on('change','.select_all_checkbox',function () {
        if($(this).is(':checked')){
            $('.select_one_checkbox').prop('checked','checked');
            onSelectAllCommon();
        }
        else{
            $('.select_one_checkbox').prop('checked','');
            display($('.product-count'),true);
            display($('.selected-product-count'),false);
            display($('.checkbox_selection_options'),false);
        }
    });

    $('body').on('change','.select_one_checkbox',function () {
        if ($(this).is(':checked')) {
            $('.select_all_checkbox').prop('checked','checked');
            onSelectAllCommon();
        }
        else{
            var checked = $('.select_one_checkbox:checked').length;
            $('.selected-product-count').empty();
            $('.selected-product-count').append('  <p style="font-size: 13px;font-weight: 600">  Selected  '+checked+' products </p>');
            if(checked === 0){
                $('.select_all_checkbox').prop('checked','');
                display($('.product-count'),true);
                display($('.selected-product-count'),false);
                display($('.checkbox_selection_options'),false);
            }
        }
    });

    $('body').on('click','.import_all_btn ',function () {
        $('.pre-loader').css('display','flex');
        let forms = [];
        if($('.select_one_checkbox:checked').length > 0){

            $('.select_one_checkbox:checked').each(function () {
                forms.push({
                    'url' : $(this).data('url'),
                    'method' : $(this).data('method'),
                });
            });
            StackAjax(forms,'import');
        }
        else{
            $('.pre-loader').css('display','none');
            alertify.error('Please Select One Product To Import!');
        }
    });

    $('body').on('click','.remove_all_btn ',function () {
        $('.pre-loader').css('display','flex');
        let forms = [];
        if($('.select_one_checkbox:checked').length > 0){

            $('.select_one_checkbox:checked').each(function () {
                forms.push({
                    'url' : $(this).data('remove_url'),
                    'method' : $(this).data('method'),
                });
            });
            StackAjax(forms,'remove');
        }
        else{
            $('.pre-loader').css('display','none');
            alertify.error('Please Select One Product To Remove!');
        }
    });

    function display($this,$option) {
        if($option){
            $this.addClass('d-inline-block');
            $this.removeClass('d-none');
        }
        else{
            $this.addClass('d-none');
            $this.removeClass('d-inline-block');
        }

    }

    function onSelectAllCommon() {

        display($('.product-count'),false);
        var selected = $('.select_one_checkbox:checked').length;
        $('.selected-product-count').empty();
        $('.selected-product-count').append('  <p style="font-size: 13px;font-weight: 600">  Selected  '+selected+' products </p>');
        display($('.selected-product-count'),true);
        display($('.checkbox_selection_options'),true);
    }
    function StackAjax(toAdd,call) {
        if (toAdd.length) {
            var request = toAdd.shift();
            var url = request.url;
            var type = request.method;

            $.ajax({
                url: url,
                type:type,
                success: function(response) {
                    StackAjax(toAdd,call);
                },
                error:function () {
                    StackAjax(toAdd,call);
                }
            });

        } else {
            $('.pre-loader').css('display', 'none');
            if(call === 'import'){
                Swal.fire(
                    'Imported!',
                    'Your Products Has Been Imported To Your Store Successfully',
                    'success'
                );
            }
            else{
                Swal.fire(
                    'Deleted!',
                    'Your Products Has Been Deleted Successfully',
                    'success'
                );
            }

            setTimeout(function () {
                window.location.reload();
            },1000)

        }
    }
    /*Select Photos From Existing*/
    $('.choose-variant-image').click(function () {
        var current = $(this);
        $.ajax({
            url: '/variant/'+$(this).data('variant')+'/change/image/'+$(this).data('image')+'?type='+$(this).data('type'),
            type: 'GET',
            success:function (response) {
                if(response.message == 'success'){
                    current.removeClass('bg-info');
                    current.addClass('bg-success');
                    current.text('Updated');
                    alertify.success('Variant image has been updated!');
                    current.parents('.modal').prev()
                        .attr('src', current.prev().attr('src'));
                }
                else{
                    alertify.error('Something went wrong!');
                }
            }
        })

    });

    // $('#paypal_pay_trigger').on('shown.bs.modal', function (e) {
    //     var $this= $('.paypal-pay-button');
    //     var html = '<div class="text-center"> <p>Subtotal: '+ $this.data('subtotal')+' USD<br>WeFullFill Paypal Fee ('+$this.data('percentage')+'%): '+ $this.data('fee')+' USD <br>Total Cost : '+ $this.data('pay')+'</p>  </div><p> A amount of '+ $this.data('pay') +' will be deducted through your Paypal Account</p>';
    //     $('#paypal_pay_trigger').find('.block-content ').html(html);
    // });

    /*Paypal Order Payment Button JS*/
    $('body').on('click','.paypal-pay-button',function () {
        // var button = $(this);
        // $('#paypal_pay_trigger').modal('show');

        // Swal.fire({
        //     title: ' Are you sure?',
        //     html:'<div class="text-center"> <p>Subtotal: '+ $(this).data('subtotal')+' USD<br>WeFullFill Paypal Fee ('+$(this).data('percentage')+'%): '+ $(this).data('fee')+' USD <br>Total Cost : '+ $(this).data('pay')+'</p>  </div><p> A amount of '+ $(this).data('pay') +' will be deducted through your Paypal Account</p>',
        //     icon: 'warning',
        //     showCancelButton: true,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     confirmButtonText: 'Pay'
        // }).then((result) => {
        //     console.log();
        //     if (result.value) {
        //         Swal.fire(
        //             'Processing!',
        //             'You will be redirected to paypal in seconds!',
        //             'success'
        //         );
        //         window.location.href = button.data('href');
        //     }
        // });
    });


    function PaypalCalc(price){
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: price
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    console.log(details);
                    // Show a success message to the buyer
                    alert('Transaction completed by ' + details.payer.name.given_name + '!');
                });
            }
        }).render('#paypal-button-container');
    }

    /*Wallet Order Payment Button JS*/
    $('body').on('click','.wallet-pay-button',function () {
        var button = $(this);
        Swal.fire({
            title: ' Are you sure?',
            html:'<p> A amount of '+ $(this).data('pay') +' will be deducted through your wallet </p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Pay'
        }).then((result) => {
            if (result.value) {
                Swal.fire(
                    'Processing!',
                    'Payment Processing Please Wait!',
                    'success'
                );
                window.location.href = button.data('href');
            }
        });
    });

    $('body').on('click','.bulk-wallet-pay-button',function () {
        var button = $(this);
        Swal.fire({
            title: ' Are you sure?',
            html:'<p> A amount of '+ $(this).data('pay') +' will be deducted through your wallet </p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Pay'
        }).then((result) => {
            if (result.value) {
                Swal.fire(
                    'Processing!',
                    'Payment Processing Please Wait!',
                    'success'
                );
                $('.bulk-payment-form').submit();
                //window.location.href = button.data('href');
            }

            //$('.bulk-payment-form').submit();
            // if (result.value) {
            //     Swal.fire(
            //         'Processing!',
            //         'Payment Processing Please Wait!',
            //         'success'
            //     );
            // }
        });
    });

    $('body').on('click', '.bulk-card-btn', function () {
        console.log($('.bulk-card-form'));
        $('.bulk-card-form').submit();
    });

    $('body').on('click','.calculate_shipping_btn',function () {
        var button = $(this);
        $.ajax({
            url:$(this).data('route'),
            type: 'GET',
            data:{
                product: $(this).data('product')
            },
            success:function (response) {
                var modal = button.data('target');
                $(modal).find('.loader-div').hide();
                $(modal).find('.drop-content').empty();
                $(modal).find('.drop-content').append(response.html);

            }
        });
    });
    $('body').on('change','.shipping_country_select',function () {
        $(this).parents('.modal').find('.drop-content').hide();
        $(this).parents('.modal').find('.loader-div').show();
        var select = $(this);
        $.ajax({
            url:$(this).data('route'),
            type: 'GET',
            data:{
                product: $(this).data('product'),
                country :$(this).val(),
            },
            success:function (response) {
                var modal = '#'+select.parents('.modal').attr('id');
                console.log(modal);
                $(modal).find('.loader-div').hide();
                $(modal).find('.drop-content').empty();
                $(modal).find('.drop-content').append(response.html);
                $(modal).find('.drop-content').show();

            }
        });
    });
    $('body').on('change','.shipping_price_radio',function () {
        if($(this).is(':checked')){
            $(this).parents('.block-content').find('.drop-shipping').text($(this).data('price'));
            $(this).parents('.block-content').find('.calculate_shipping_btn').text($(this).data('country'));
        }
    });

    /*Wishlist Switch for has_product*/
    $('body').on('change','#sw-custom',function () {
        if($(this).is(':checked')){
            $(this).next('.custom-control-label').text('Yes');
            $('#product_shopify_id').attr('required',true);
            $('.product-shopify').show();
        }
        else{
            $(this).next('.custom-control-label').text('No');
            $('#product_shopify_id').attr('required',false);
            $('.product-shopify').hide();
        }
    });
    if(!$('body').find('.rating-stars').hasClass('disabled')){
        /* 1. Visualizing things on Hover - See next part for action on click */
        $('body').on('mouseover','#stars li',function(){
            // $('#stars li').on('mouseover', function(){
            var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

            // Now highlight all the stars that's not after the current hovered star
            $(this).parent().children('li.star').each(function(e){
                if (e < onStar) {
                    $(this).addClass('hover');
                }
                else {
                    $(this).removeClass('hover');
                }
            });

        })
        $('body').on('mouseout','#stars li',function(){
            $(this).parent().children('li.star').each(function(e){
                $(this).removeClass('hover');
            });
        });


        /* 2. Action to perform on click */
        $('body').on('click','#stars li',function(){
            // $('#stars li').on('click', function(){
            $('#rating-input').val($(this).data('value'));
            var onStar = parseInt($(this).data('value'), 10); // The star currently selected
            var stars = $(this).parent().children('li.star');

            for (i = 0; i < stars.length; i++) {
                $(stars[i]).removeClass('selected');
            }

            for (i = 0; i < onStar; i++) {
                $(stars[i]).addClass('selected');
            }



        });
    }

    if($('body').find('input[name=rating]').length > 0){
        $('input[name=rating]').each(function () {
            var rating = $(this).val();
            $(this).closest('div').find('.star').each(function (index) {
                if(index < rating){
                    $(this).addClass('selected');
                }
            })
        });

    }

    if($('body').find('#canvas-graph-one-store').length > 0){
        console.log('ok');
        var config = {
            type: 'bar',
            data: {
                labels: JSON.parse($('#canvas-graph-one-store').attr('data-labels')),
                datasets: [{
                    label: 'Order Count',
                    backgroundColor: '#00e2ff',
                    borderColor: '#00e2ff',
                    data: JSON.parse($('#canvas-graph-one-store').attr('data-values')),
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Summary Orders Count'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }]
                }
            }
        };

        var ctx = document.getElementById('canvas-graph-one-store').getContext('2d');
        window.myBar = new Chart(ctx, config);
    }

    if($('body').find('#canvas-graph-two-store').length > 0){
        console.log('ok');
        var config = {
            type: 'line',
            data: {
                labels: JSON.parse($('#canvas-graph-two-store').attr('data-labels')),
                datasets: [{
                    label: 'Orders Sales',
                    backgroundColor: '#5c80d1',
                    borderColor: '#5c80d1',
                    data: JSON.parse($('#canvas-graph-two-store').attr('data-values')),
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Summary Orders Sales'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Sales'
                        }
                    }]
                }
            }
        };

        var ctx_2 = document.getElementById('canvas-graph-two-store').getContext('2d');
        window.myLine = new Chart(ctx_2, config);
    }

    if($('body').find('#canvas-graph-three-store').length > 0){
        console.log('ok');
        var config = {
            type: 'bar',
            data: {
                labels: JSON.parse($('#canvas-graph-three-store').attr('data-labels')),
                datasets: [{
                    label: 'Profit',
                    backgroundColor: '#89d18a',
                    borderColor: '#5fd154',
                    data: JSON.parse($('#canvas-graph-three-store').attr('data-values')),
                    fill: 'start',
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Summary Profit'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Profit'
                        }
                    }]
                }
            }
        };

        var ctx_3 = document.getElementById('canvas-graph-three-store').getContext('2d');
        window.myLine = new Chart(ctx_3, config);
    }

    if($('body').find('#canvas-graph-four-store').length > 0){
        console.log('ok');
        var config = {
            type: 'line',
            data: {
                labels: JSON.parse($('#canvas-graph-four-store').attr('data-labels')),
                datasets: [{
                    label: 'Products',
                    backgroundColor: '#cd99d1',
                    borderColor: '#cd2bd1',
                    data: JSON.parse($('#canvas-graph-four-store').attr('data-values')),
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Summary New Products'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Products'
                        }
                    }]
                }
            }
        };

        var ctx_4 = document.getElementById('canvas-graph-four-store').getContext('2d');
        window.myLine = new Chart(ctx_4, config);
    }

});
