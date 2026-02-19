$(document).ready(function () {
    var lastScrollTop = 0;
    AOS.init({
        once: true,
    });
    $(window).scroll(function (event) {
		var st = $(this).scrollTop();
		if (st > lastScrollTop) {
			$("header").addClass("menu-fixed");
		} else {
			$("header").removeClass("menu-fixed");
		}
    });
    
    $(".btn-mobile").click(function (e) { 
        e.preventDefault();
        if($(this).hasClass("active")){
            $(this).removeClass("active");
            $(".box-links-header, .overlay-menu").fadeOut();
            $("body").removeClass("modal-open");
        }else{
            $(this).addClass("active");
            $(".box-links-header, .overlay-menu").fadeIn();
            $("body").addClass("modal-open");
        }
    });

    $(".overlay-menu").click(function (e) { 
        e.preventDefault();
        $(".box-links-header").fadeOut();
        $(this).fadeOut();
        $(".btn-mobile").removeClass("active")
        $("body").removeClass("modal-open");
    });
   
    $("#carousel-how-it-works").owlCarousel({
        loop: true,
        margin: 30,
        dots: true,
        nav: false,
        smartSpeed: 1000,
        items: 1
    });
    if ( screen.width < 768 ){
    }

    $(".btn-item-faq").click(function (e) { 
        e.preventDefault();
        if($(this).hasClass("active")){
            $(this).removeClass("active");
            $(this).next().slideUp();
        }else{
            $(".btn-item-faq").removeClass("active");
            $(".content-item-faq").slideUp();
            $(this).addClass("active");
            $(this).next().slideDown();
        }
    });
    $(".box-action-check").click(function (e) { 
        e.preventDefault();
        if($(this).hasClass("active")){
            $(this).removeClass("active");
            $(this).next().removeClass("active")
            $(this).prev().addClass("active");
            $("#monthly").show();
            $("#yearly").hide();
        }else{
            $(this).addClass("active");
            $(this).next().addClass("active")
            $(this).prev().removeClass("active");
            $("#monthly").hide();
            $("#yearly").show();
        }
    });
    $(".item-how-it-works").click(function (e) { 
        e.preventDefault();
        if(!$(this).hasClass("active")){
            srcImg = $(this).data("img")
            $(".item-how-it-works").removeClass("active");
            $(".reeplace-img-item").attr("src",srcImg);
            $(this).addClass("active");
            $(this).next().slideDown();
        }
    });

    // Animacion de imagenes
    setTimeout(() => {
        $(".preview-load-page").fadeOut();
        $("body").removeClass("modal-open")
    }, 1000);

    $(".animation-linkto").click(function (e) { 
        e.preventDefault();
        hrefLink = $(this).attr("href")
        $(".preview-load-page").fadeIn();
        setTimeout(() => {
            window.location.href = hrefLink;
        }, 400);
    });
});