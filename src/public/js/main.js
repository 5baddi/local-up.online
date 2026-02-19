$(document).ready(function () {
    // $(function() {
    //     $('input[name="daterange"]').daterangepicker({
    //       opens: 'left',
    //       startDate: moment(),
    //     }, function(start, end, label) {
    //       console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    //     });
    //   });

    if (window.location.pathname === '/merchant-dashboard') {
        $('#notifications-bell').css('visibility', 'visible');
    }

    $('.btn-dropdown1').click(function (e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).next().slideUp()
        } else {
            $(this).addClass('active');
            $(this).next().slideDown()
        }
    });

    $('.btn-payouts').click(function (e) {
        e.preventDefault();
        if (!$(this).hasClass('active')) {
            var filter = $(this).attr('id');
            $('.btn-payouts').removeClass('active');
            $(this).addClass('active');
            if (filter == "all-orders") {
                $('.table-payouts tbody tr').fadeIn()
            } else {
                $('.table-payouts tbody tr').fadeOut();
                setTimeout(function () {
                    $('.table-payouts tbody tr[data-filter="' + filter + '"]').fadeIn()
                }, 300)
            }
        }
    });
    $('.box-profile-settings-tabs a').click(function (e) {
        e.preventDefault();
        if (!$(this).hasClass('active')) {
            var tab = $(this).attr('href');
            $('.box-profile-settings-tabs a').removeClass('active');
            $('.box-profile-settings-body').removeClass('active').fadeOut(0);
            $(this).addClass('active');
            setTimeout(function () {
                $(tab).addClass('active').fadeIn(0);
            }, 0)
        }
    });
    $('.btn-menu-lat').click(function (e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).next().slideUp();
            $(this).removeClass('active');
        } else {
            $(this).next().slideDown();
            $(this).addClass('active');
        }
    });
    $('.box-input-file-custom1 input[type="file"]').change(function (e) {
        var fileName = e.target.files[0].name;
        if (fileName.length >= 20) {
            $(this).parent().find("label span").text(fileName.substr(0, 20) + '...')
        } else {
            $(this).parent().find("label span").text(fileName)
        }
    });
    $('.btn-menu-mobile').click(function (e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.box-links-menu-lat').removeClass('active')
        } else {
            if ($('.sidebar').hasClass('active')) {
                $('.sidebar,.btn-notifications').removeClass('active');
            }
            $(this).addClass('active')
            $('.box-links-menu-lat').addClass('active')
        }
    });
    $('.btn-notifications').click(function (e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.sidebar').removeClass('active')
        } else {
            if ($('.box-links-menu-lat').hasClass('active')) {
                $('.box-links-menu-lat,.btn-menu-mobile').removeClass('active');
            }
            $(this).addClass('active')
            $('.sidebar').addClass('active')
        }
    });

    $('.btn-advanced-settings').click(function (e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.item-customize-advanced-settings').slideUp();
            $('.box-submit-advanced-settings').removeClass('active');
        } else {
            $(this).addClass('active')
            $('.item-customize-advanced-settings').slideDown()
            $('.box-submit-advanced-settings').addClass('active');
        }
    });

    $('.item-pay').click(function (e) {
        e.preventDefault();
        modalId = $(this).data('target');
        $(modalId).fadeIn()
    });

    $('.btn-close-modal').click(function (e) {
        e.preventDefault();
        $(this).parent().parent().parent().fadeOut();
    });
    $('.overlay-modal').click(function (e) {
        e.preventDefault();
        $(this).parent().fadeOut();
    });

    $('textarea[maxlength]').keyup(function () {
        //get the limit from maxlength attribute
        var limit = parseInt($(this).attr('maxlength'));
        //get the current text inside the textarea
        var text = $(this).val();
        //count the number of characters in the text
        var chars = text.length;

        //check if there are more characters then allowed
        if (chars > limit) {
            //and if there are use substr to get the text before the limit
            var new_text = text.substr(0, limit);

            //and change the current text with the new text
            $(this).val(new_text);
        }
    });

    // Disabled up to V2
    // $(".box-desing1-col-last textarea").keydown(function (e) {
    //     $(this).next().find('span').text(this.value.length)
    //     //this.value.length
    // });

    // Disabled up to V2
    // $(".box-desing1-col-last textarea").keyup(function (e) {
    //     $(this).next().find('span').text(this.value.length)
    //     //this.value.length
    // });

    $('.btn-item-bank').click(function (e) {
        e.preventDefault();
        if ($(this).parent().hasClass('active')) {
            $(this).parent().removeClass('active');
			$(this).next().slideUp();
			$(this).parent().find('.btn-close-item-bank').fadeOut();
        } else {
            $(this).parent().addClass('active')
			$(this).next().slideDown();
			$(this).parent().find('.btn-close-item-bank').fadeIn();
        }
	});
	$('.btn-close-item-bank').click(function (e) {
		e.preventDefault();
		$(this).fadeOut();
		$(this).parent().removeClass('active');
		$(this).parent().find('.content-item-bank').slideUp();
    });

    $('.box-show-more').click(function (e) {
        e.preventDefault();
        $(this).fadeOut();
        $('.table-responsive-affiliates').addClass('active')
    });
    $('#filter-records').change(function (e) {
        e.preventDefault();
        window.location.href = this.value;
    });
    $('#number-account').keyup(function (event) {

        // skip for arrow keys
        if (event.which >= 37 && event.which <= 40) return;

        // format number
        $(this).val(function (index, value) {
            return value
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{4})+(?!\d))/g, " ");
        });
    });

    $('.item-single-payment input').not("#full_name").keyup(function (event) {
        if (/\D/g.test(this.value)) {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
    })

    $("#date-account").keyup(function (e) {
        $(this).val(function (index, value) {
            return value
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{2})+(?!\d))/g, "/");
        });
    });

    // $('#check_affiliate_form label').click(function() {
    //     idCheck = $(this).attr("for");
    //     if ($("#" + idCheck).prop('checked')) {
    //         $("#affiliate_form_form").slideUp();
    //     }else{
    //         $("#affiliate_form_form").slideDown();
    //     }
    // });

    if ($("#affiliate_form").prop('checked'))
    {
        $("#affiliate_form_form").slideDown();
    }
    else
    {
        $("#affiliate_form_form").slideUp();
    }

    if($("#custom").val() == 1)
    {
        $('#btn-save-custom').prop('disabled', true);
        $('#btn-save-custom').css('background-color', '#A2A8C1');
        $('.save-success').css('display','block');

        setTimeout(function()
        {
            $('#btn-save-custom').prop('disabled', false);
            $('#btn-save-custom').css('background-color', '#3860FB');
            $('.save-success').css('display','none');
        }, 1000)
    }

    console.log($('#openModalMethodPaid').val());

    if($('#openModalMethodPaid').val() == 'emptymethods')
    {
        $('#modalNotMethodPayment').modal('show');
    }
});

$('.show-pass-action').click(function (e) {
    e.preventDefault();
    if($(this).prev().attr('type') == "password"){
        $(this).prev().attr('type', 'text')
    }else{
        $(this).prev().attr('type', 'password')
    }
});

// Disabled up to V2
//Count characters Post Settings
// function countChars(obj){
//     var strLength = obj.value.length;

//     if(strLength <= 80){
//         document.getElementById("charNum").innerHTML = '<span>'+strLength+'</span> of 80 characters';
//     }
// }

//Activate menu option according to Merchant's location in the app
$(document).ready(function(){
    var actualPath = window.location.pathname.split("/");
    var actualPathFilter = window.location.pathname;
    var actualPathFilterMerchant = window.location.pathname;
    console.log(actualPath[1]);

    switch (actualPath[1]) {
        case 'merchant-dashboard':
        case '/home':
            document.getElementById("option-dashboard").classList.add("active");
            break;
        case 'merchant-customize':
            document.getElementById("option-customize").classList.add("active");
            document.getElementById("option-integrations").classList.remove("d-none");
            break;
        case 'merchant-integrations':
            document.getElementById("option-integrations").classList.add("active");
            document.getElementById("option-integrations").classList.remove("d-none");
            break;
        case 'merchant-payouts':
        case 'merchant-single-payout':
        case 'merchant-affiliate-profile':
            document.getElementById("option-payouts").classList.add("active");
            break;
        case 'merchant-account':
        case 'merchant-single-change-plan':
        case 'merchant-single-payment':
            document.getElementById("option-account").classList.add("active");
            break;
        case 'merchant-settings':
            document.getElementById("option-settings").classList.add("active");
            break;
        case 'affiliate-dashboard':
            document.getElementById("option-dashboard-affiliate").classList.add("active");
            break;
        case 'affiliate-payouts':
            console.log('hola');
            document.getElementById("option-payouts-affiliate").classList.add("active");
            break;
        case 'affiliate-settings':
            document.getElementById("option-settings-affiliate").classList.add("active");
            break;
    }

    switch (actualPathFilter) {
        case '/affiliate-dashboard/month':
            document.getElementById("filter-dashboard-month").classList.add("active");
            break;
        case '/affiliate-dashboard/today':
            document.getElementById("filter-dashboard-today").classList.add("active");
            break;
        case '/affiliate-dashboard/year':
            document.getElementById("filter-dashboard-year").classList.add("active");
            break;
        case '/affiliate-payouts/month':
            document.getElementById("filter-payouts-month").classList.add("active");
            break;
        case '/affiliate-payouts/today':
            document.getElementById("filter-payouts-today").classList.add("active");
            break;
        case '/affiliate-payouts/year':
            document.getElementById("filter-payouts-year").classList.add("active");
            break;
    }

    // console.log(actualPathFilterMerchant);
    switch (actualPathFilterMerchant) {
        case '/merchant-dashboard/month':
        case '/home':
            $("#filter-records").val($("#filter-records option:eq(1)").val());
            break;
        case '/merchant-dashboard/today':
            $("#filter-records").val($("#filter-records option:eq(0)").val());
            break;
        case '/merchant-dashboard/year':
            $("#filter-records").val($("#filter-records option:eq(2)").val());
            break;
        case '/merchant-payouts/month':
            $("#filter-records").val($("#filter-records option:eq(1)").val());
            break;
        case '/merchant-payouts/today':
            $("#filter-records").val($("#filter-records option:eq(0)").val());
            break;
        case '/merchant-payouts/year':
            $("#filter-records").val($("#filter-records option:eq(2)").val());
            break;
    }
});

// Affiliate | Hide all Notifications
function hideAllNotifications(affiliateId)
{
    $.ajax(
        {
            type: "GET",
            url: '/affiliate-notify-clear-all/' + affiliateId
        }
    )
    document.getElementById("notifications").style.display = "none";
    $('.link-clear-notification').fadeOut();
}

// Affiliate | Hide Payment Method Notification
function hideNotifyPayMethod(element, affiliateId)
{
    $.ajax(
        {
            type: "GET",
            url: '/affiliate-notify-payout-method/' + affiliateId
        }
    )
    element.parentNode.style.display='none';
};

// Affiliate | Hide Commission Notification
function hideNotifyCommission(element, orderId)
{
    $.ajax(
        {
            type: "GET",
            url: '/affiliate-notify-commission/' + orderId
        }
    )
    element.parentNode.style.display='none';
};

// Merchant | Hide all Notifications
function hideAllNotificationsMerchant(merchantId)
{
    $.ajax(
        {
            type: "GET",
            url: '/merchant-notify-clear-all/' + merchantId
        }
    )
    document.getElementById("notifications").style.display = "none";
    document.getElementById("notifications-pagination").style.display = "none";
    $('.link-clear-notification').fadeOut();
}

// Merchant | Hide Welcome Notification
function hideNotifyWelcome(element, merchantId)
{
    $.ajax(
        {
            type: "GET",
            url: '/merchant-notify-welcome/' + merchantId
        }
    )
    element.parentNode.style.display='none';
};

// Merchant | Hide Commission Notification
function hideNotifyCommissionMerchant(element, orderId)
{
    $.ajax(
        {
            type: "GET",
            url: '/merchant-notify-commission/' + orderId
        }
    )
    element.parentNode.style.display='none';
};

// Merchant | Hide Free Trial Notification
function hideNotification(element){
    element.parentNode.style.display='none';
};

// $('.link-clear-notification').click(function (e) {
//     e.preventDefault();
//     $('.box-content-notification-sidebar .card-notification').fadeOut();
// });

// $('.btn-close-notification').click(function (e) {
//     e.preventDefault();
//     $(this).parent().fadeOut()
// });


// Show preview photo
function readURL(input, imgSelected) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#add-image').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Change labels links photo
$('.custom-input-file').change(function(e){
    var fileName = e. target. files[0]. name;
    readURL(this, $(this).parent().find('.img-preview-file'));
    document.getElementById("aux_photo").value="";
    $('.custom-label-file').text('change')
    $('.custom-label-file-remove').text('remove')
});

// Remove photo before save
function removeProfilePhoto() {
    document.getElementById("add-image").src="img/icon-account.svg";
    $('.custom-label-file').text('upload')
    $('.custom-label-file-remove').text('')
    document.getElementById("aux_photo").value="img/icon-account.svg";
}

// Input only numbers
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode;

    if (charCode != 46 && charCode > 31
        && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

$('#check_affiliate_form label').click(function() {
    idCheck = $(this).attr("for");
    if ($("#" + idCheck).prop('checked')) {
        $("#affiliate_form_form").slideUp();
    }else{
        $("#affiliate_form_form").slideDown();
    }
});

$('#check_preview_popup label').click(function() {
    idCheck = $(this).attr("for");
    if ($("#" + idCheck).prop('checked')) {
        $("#preview-popup").removeClass("active");
    }else{
        $("#preview-popup").addClass("active");
    }
});

if($('#datepicker').length != 0){
    $('#datepicker').datepicker({
        language: 'en',
        dateFormat: 'yyyy/mm/dd',
        minDate: 0,
        autoclose: true
    });
}


$("#phone").intlTelInput({
    // allowExtensions: true,
    autoFormat: true,
     autoHideDialCode: false,
     defaultCountry: "US",
     nationalMode: false,
     preventInvalidNumbers: true,
     utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/14.0.7/js/utils.js"
 });

//Active change Bank info
$(".link-change").click(function(e){
    e.preventDefault();
    if($(this).hasClass('active')){
        $(this).removeClass('active');
        $('.select-banks').hide()
        $('.title-box-item-bank1').show()
        $('.number-box-item-bank1').show()
        $('.input-change-bank').attr({'type':'hidden'})
        $('#type').attr('disabled', true);
        $('#routing_number').attr('readonly', true)
        $('#full_name').attr('readonly', true)
        $('#address').attr('readonly', true)
        $('#country').attr('readonly', true)
        $('#state').attr('readonly', true)
        $('#city').attr('readonly', true)
        $('#postalcode').attr('readonly', true)
    }else{
        $(this).addClass('active');
        $(".link-save-bank-transfer").addClass('active')
        $('.title-box-item-bank1').hide()
        $('.number-box-item-bank1').hide()
        $('.input-change-bank').attr({'type':'text'})
        $('.select-banks').show()
        $('.select-banks').focus()
        $('#type').attr('disabled', false)
        $('#routing_number').attr('readonly', false)
        $('#full_name').attr('readonly', false)
        $('#address').attr('readonly', false)
        $('#country').attr('readonly', false)
        $('#state').attr('readonly', false)
        $('#city').attr('readonly', false)
        $('#postalcode').attr('readonly', false)
    }
});

$(".link-save-bank-transfer").click(function(e){
    if ($('.select-banks option:selected').val() == ''){
        $("#notify-bank").show();
        $("#notify-bank").html("Please select a bank.");
    }
    else{
        if ($('.input-change-bank').val() == ""){
            $("#notify-bank").show();
            $("#notify-bank").html("The Linked Account is required.");
            e.preventDefault();
        }
        else {
            if ($('.input-change-bank').val().length >= 10){
                $("#manage-account-bank").submit();
            }else{
                $("#notify-bank").show();
                $("#notify-bank").html("The Linked Account must have at least 10 digits.");
                e.preventDefault();
            }
        }
    }
    return false;
});

$("form.no-enter-submit").keypress(function(e) {
    if (e.which == 13) {
        return false;
    }
});

$('.link-save-valid').click(function (e) {
    e.preventDefault();
    selectForm = $(this).data('form')
    selectElementParent = $(this).parent().parent();
    selectComparate1 = selectElementParent.find('.input-item-bank-comparate').data('target');
    valComparate1 = selectElementParent.find('.input-item-bank-comparate').val();
    valComparate2 = $(selectComparate1).val();
    if(valComparate1 == valComparate2){
        $(selectForm).submit()
    }else{
        selectElementParent.find('.notify-error').slideDown().text('Values â€‹â€‹do not match')
    }
});

// Function navigate Dashboard with classic select
// $('#select-merchant').change(function () {
//     $("#form-merchant").submit();
// });

$('.link-id').click(function () {
    id = $(this).data('id');

    $("#merchant_id").val(id);

    $("#form-merchant").submit();
});

// Select With Icons
//TOGGLING NESTED ul
$(".drop-down .selected a").click(function() {
    $(".drop-down .options ul").toggle();
    if($('span.arrow-merchant').hasClass('arrow-merchant-down'))
    {
        $('span.arrow-merchant').removeClass( "arrow-merchant-down" ).addClass( "arrow-merchant-up" );
    }
    else
    {
        $('span.arrow-merchant').removeClass( "arrow-merchant-up" ).addClass( "arrow-merchant-down" );
    }
});

//SELECT OPTIONS AND HIDE OPTION AFTER SELECTION
$(".drop-down .options .text-selected").click(function() {
    var text = $(this).html();
    console.log(text);
    $(".drop-down .selected a span").html(text);
    $(".drop-down .options ul").hide();
});

//HIDE OPTIONS IF CLICKED ANYWHERE ELSE ON PAGE
$(document).bind('click', function(e) {
    var $clicked = $(e.target);
    if (! $clicked.parents().hasClass("drop-down"))
        $(".drop-down .options ul").hide();
});

//Change icon arrow
$(document).ready(function(){
    $(".dropdown").on("hide.bs.dropdown", function(){
        $(".btn").html('Dropdown <span class="arrow-merchant"></span>');
    });

    $(".dropdown").on("show.bs.dropdown", function(){
        $(".btn").html('Dropdown <span class="arrow-merchant arrow-merchant-up"></span>');
    });
});